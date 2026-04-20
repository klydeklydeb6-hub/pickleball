<?php

namespace Tests\Feature;

use App\Models\FacilitySetting;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_court_count(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->post('/admin/court-count', [
                'court_count' => 12,
            ]);

        $response->assertRedirect(route('admin.dashboard', ['panel' => 'courts'], absolute: false));

        $this->assertSame(12, FacilitySetting::currentCourtCount());
    }

    public function test_admin_can_create_walk_in_reservation_without_registered_user(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Existing Walk In',
            'booking_date' => now()->toDateString(),
            'time_slot' => '6:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-WALKIN-001',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post('/admin/walk-in-reservations', [
                'customer_name' => 'Walk In Customer',
                'contact_number' => '09175550001',
                'booking_date' => now()->toDateString(),
                'court_number' => 2,
                'time_slot' => '6:00 AM',
                'players' => 2,
                'payment_method' => 'cash',
                'payment_status' => 'Paid',
                'payment_reference' => null,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'user_id' => null,
            'customer_name' => 'Walk In Customer',
            'contact_number' => '09175550001',
            'time_slot' => '6:00 AM',
            'court_number' => 2,
            'payment_method' => 'cash',
            'payment_status' => 'Paid',
        ]);
    }

    public function test_admin_cannot_create_walk_in_on_an_already_booked_court(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Existing Walk In',
            'booking_date' => now()->toDateString(),
            'time_slot' => '7:00 AM',
            'court_number' => 3,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-WALKIN-003',
        ]);

        $response = $this
            ->actingAs($admin)
            ->from('/admin')
            ->post('/admin/walk-in-reservations', [
                'customer_name' => 'Another Walk In',
                'contact_number' => '09175550002',
                'booking_date' => now()->toDateString(),
                'court_number' => 3,
                'time_slot' => '7:00 AM',
                'players' => 2,
                'payment_method' => 'cash',
                'payment_status' => 'Paid',
                'payment_reference' => null,
            ]);

        $response
            ->assertRedirect('/admin')
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('reservations', [
            'customer_name' => 'Another Walk In',
            'booking_date' => now()->toDateString(),
            'court_number' => 3,
            'time_slot' => '7:00 AM',
        ]);
    }

    public function test_admin_can_update_rates(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->post('/admin/rates', [
                'reservation_rate' => 700,
                'paddle_rent_rate' => 150,
                'ball_rate' => 90,
            ]);

        $response->assertRedirect(route('admin.dashboard', ['panel' => 'rates'], absolute: false));

        $settings = FacilitySetting::current();

        $this->assertSame(700, (int) $settings->reservation_rate);
        $this->assertSame(150, (int) $settings->paddle_rent_rate);
        $this->assertSame(90, (int) $settings->ball_rate);
    }

    public function test_admin_can_toggle_public_reservation_name_visibility(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->post('/admin/public-reservations/visibility', [
                'show_public_customer_names' => '1',
            ]);

        $response->assertRedirect(route('admin.dashboard', ['panel' => 'rates'], absolute: false));

        $this->assertTrue(FacilitySetting::publicCustomerNamesVisible());
        $this->assertDatabaseHas('facility_settings', [
            'id' => 1,
            'show_public_customer_names' => 1,
        ]);
    }

    public function test_admin_can_unlock_rain_reschedule_for_registered_customer(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $customer = User::factory()->create([
            'name' => 'Rainy Customer',
        ]);

        $reservation = Reservation::create([
            'user_id' => $customer->id,
            'customer_name' => $customer->name,
            'booking_date' => now()->toDateString(),
            'time_slot' => '8:00 AM',
            'court_number' => 4,
            'players' => 4,
            'payment_method' => 'gcash',
            'payment_reference' => 'GCASH-RAIN-001',
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-RAIN-001',
        ]);

        $response = $this
            ->actingAs($admin)
            ->from('/admin')
            ->post("/admin/reservations/{$reservation->id}/unlock-reschedule");

        $response
            ->assertRedirect('/admin')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'reschedule_reason' => 'Rain / uncovered court',
            'reschedule_deadline' => now()->addDays(15)->toDateString(),
        ]);
    }

    public function test_admin_can_lock_reschedule_for_registered_customer(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $customer = User::factory()->create([
            'name' => 'Lock Customer',
        ]);

        $reservation = Reservation::create([
            'user_id' => $customer->id,
            'customer_name' => $customer->name,
            'booking_date' => now()->toDateString(),
            'time_slot' => '9:00 AM',
            'court_number' => 5,
            'players' => 4,
            'payment_method' => 'gcash',
            'payment_reference' => 'GCASH-LOCK-001',
            'payment_status' => 'Paid',
            'reschedule_unlocked_at' => now(),
            'reschedule_deadline' => now()->addDays(15)->toDateString(),
            'reschedule_reason' => 'Rain / uncovered court',
            'amount' => 500,
            'receipt_no' => 'RCPT-RAIN-LOCK-001',
        ]);

        $response = $this
            ->actingAs($admin)
            ->from('/admin?panel=monitor')
            ->post("/admin/reservations/{$reservation->id}/lock-reschedule");

        $response
            ->assertRedirect('/admin?panel=monitor')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'reschedule_unlocked_at' => null,
            'reschedule_deadline' => null,
            'reschedule_reason' => null,
        ]);
    }
}
