<?php

namespace Tests\Feature;

use App\Models\FacilitySetting;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_reservations_are_saved_under_the_logged_in_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Maria Santos',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/reserve', [
                'booking_date' => now()->toDateString(),
                'court_number' => 3,
                'time_slot' => '6:00 AM',
                'players' => 4,
                'contact_number' => '09171234567',
                'payment_method' => 'gcash',
                'payment_reference' => 'REF-2026',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'customer_name' => 'Maria Santos',
            'contact_number' => '09171234567',
            'court_number' => 3,
            'time_slot' => '6:00 AM',
            'payment_reference' => 'REF-2026',
        ]);
    }

    public function test_online_reservations_reject_walk_in_payment_method(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/reserve', [
                'booking_date' => now()->toDateString(),
                'court_number' => 1,
                'time_slot' => '6:00 AM',
                'players' => 4,
                'contact_number' => '09179990000',
                'payment_method' => 'cash',
                'payment_reference' => 'CASH-NOT-ALLOWED',
            ]);

        $response
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'court_number' => 1,
            'time_slot' => '6:00 AM',
        ]);
    }

    public function test_online_reservations_can_start_a_paymongo_checkout_session(): void
    {
        config([
            'services.paymongo.payment_link_url' => 'https://paymongo.test/pay/{receipt_no}',
        ]);

        $user = User::factory()->create([
            'name' => 'PayMongo Client',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/reserve', [
                'booking_date' => now()->toDateString(),
                'court_number' => 1,
                'time_slot' => '6:00 AM',
                'players' => 4,
                'contact_number' => '09170001111',
                'payment_method' => 'paymongo',
                'payment_reference' => null,
            ]);

        $reservation = Reservation::query()
            ->where('user_id', $user->id)
            ->where('payment_method', 'paymongo')
            ->firstOrFail();

        $response->assertRedirect('https://paymongo.test/pay/' . $reservation->receipt_no);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'customer_name' => 'PayMongo Client',
            'payment_method' => 'paymongo',
            'payment_status' => 'Pending',
            'time_slot' => '6:00 AM',
            'court_number' => 1,
        ]);
    }

    public function test_online_reservations_require_an_available_selected_court(): void
    {
        $user = User::factory()->create();

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Existing Booking',
            'booking_date' => now()->toDateString(),
            'time_slot' => '7:00 AM',
            'court_number' => 2,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-TAKEN-001',
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/')
            ->post('/reserve', [
                'booking_date' => now()->toDateString(),
                'court_number' => 2,
                'time_slot' => '7:00 AM',
                'players' => 4,
                'contact_number' => '09178887777',
                'payment_method' => 'gcash',
                'payment_reference' => 'REF-TAKEN-COURT',
            ]);

        $response
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'court_number' => 2,
            'time_slot' => '7:00 AM',
        ]);
    }

    public function test_online_reservations_must_be_within_fifteen_days(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/reserve', [
                'booking_date' => now()->addDays(16)->toDateString(),
                'court_number' => 1,
                'time_slot' => '6:00 AM',
                'players' => 4,
                'contact_number' => '09176665555',
                'payment_method' => 'gcash',
                'payment_reference' => 'REF-TOO-FAR',
            ]);

        $response->assertSessionHasErrors('booking_date');

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'payment_reference' => 'REF-TOO-FAR',
        ]);
    }

    public function test_online_reservations_can_book_multiple_hours_and_total_the_hourly_rates(): void
    {
        $user = User::factory()->create([
            'name' => 'Multi Hour User',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/reserve', [
                'booking_date' => now()->toDateString(),
                'court_number' => 1,
                'time_slot' => '6:00 AM',
                'duration_hours' => 2,
                'players' => 2,
                'contact_number' => '09172223333',
                'payment_method' => 'gcash',
                'payment_reference' => 'REF-2-HOURS',
            ]);

        $response->assertRedirect();

        $reservation = Reservation::query()
            ->where('user_id', $user->id)
            ->where('payment_reference', 'REF-2-HOURS')
            ->firstOrFail();

        $this->assertSame(2, $reservation->durationHours());
        $this->assertSame(1000.0, (float) $reservation->amount);
    }

    public function test_online_reservations_reject_overlapping_multi_hour_schedules(): void
    {
        $user = User::factory()->create();

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Existing Two Hour Booking',
            'contact_number' => '09174445555',
            'booking_date' => now()->toDateString(),
            'time_slot' => '6:00 AM',
            'duration_hours' => 2,
            'court_number' => 2,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 1000,
            'receipt_no' => 'RCPT-OVERLAP-2HR-001',
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/')
            ->post('/reserve', [
                'booking_date' => now()->toDateString(),
                'court_number' => 2,
                'time_slot' => '7:00 AM',
                'duration_hours' => 1,
                'players' => 4,
                'contact_number' => '09179998888',
                'payment_method' => 'gcash',
                'payment_reference' => 'REF-OVERLAP',
            ]);

        $response
            ->assertRedirect('/')
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('reservations', [
            'user_id' => $user->id,
            'payment_reference' => 'REF-OVERLAP',
        ]);
    }

    public function test_users_can_reschedule_when_admin_unlocks_for_rain(): void
    {
        $user = User::factory()->create([
            'name' => 'Reschedule User',
        ]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'booking_date' => now()->toDateString(),
            'time_slot' => '9:00 AM',
            'court_number' => 2,
            'players' => 4,
            'payment_method' => 'gcash',
            'payment_reference' => 'REF-RAIN-UNLOCK',
            'payment_status' => 'Paid',
            'reschedule_unlocked_at' => now(),
            'reschedule_deadline' => now()->addDays(15)->toDateString(),
            'reschedule_reason' => 'Rain / uncovered court',
            'amount' => 500,
            'receipt_no' => 'RCPT-RAIN-USER-001',
        ]);

        $response = $this
            ->actingAs($user)
            ->post("/reservations/{$reservation->id}/reschedule", [
                'booking_date' => now()->addDays(3)->toDateString(),
                'court_number' => 5,
                'time_slot' => '10:00 AM',
            ]);

        $response->assertRedirect(route('reservations.receipt', $reservation, false));

        $reservation->refresh();

        $this->assertSame(now()->addDays(3)->toDateString(), $reservation->booking_date->toDateString());
        $this->assertSame(5, $reservation->court_number);
        $this->assertSame('10:00 AM', $reservation->time_slot);
        $this->assertNull($reservation->reschedule_unlocked_at);
        $this->assertNull($reservation->reschedule_deadline);
        $this->assertNull($reservation->reschedule_reason);
    }

    public function test_users_cannot_reschedule_without_admin_unlock(): void
    {
        $user = User::factory()->create();

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'booking_date' => now()->toDateString(),
            'time_slot' => '11:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'gcash',
            'payment_reference' => 'REF-NO-UNLOCK',
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-NO-UNLOCK-001',
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->post("/reservations/{$reservation->id}/reschedule", [
                'booking_date' => now()->addDays(2)->toDateString(),
                'court_number' => 3,
                'time_slot' => '1:00 PM',
            ]);

        $response
            ->assertRedirect('/dashboard')
            ->assertSessionHas('error');

        $reservation->refresh();

        $this->assertSame(now()->toDateString(), $reservation->booking_date->toDateString());
        $this->assertSame(1, $reservation->court_number);
        $this->assertSame('11:00 AM', $reservation->time_slot);
    }

    public function test_guests_are_redirected_to_login_before_reserving(): void
    {
        $response = $this->post('/reserve', [
            'booking_date' => now()->toDateString(),
            'court_number' => 1,
            'time_slot' => '6:00 AM',
            'players' => 4,
            'contact_number' => '09173334444',
            'payment_method' => 'gcash',
            'payment_reference' => 'REF-2026',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_guest_booking_page_hides_the_reserve_slot_card(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSeeText('User Agreement')
            ->assertSeeText('I Agree and Enter the Website')
            ->assertSeeText('Book Now')
            ->assertSee(route('login', ['redirect_to' => 'booking'], false))
            ->assertDontSeeText('Reserve a Slot')
            ->assertDontSeeText('Create an account or sign in first.')
            ->assertSeeText('Reservations for');
    }

    public function test_customer_booking_page_shows_reserve_slot_and_account_summary_for_non_admin_users(): void
    {
        $user = User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/');

        $response
            ->assertOk()
            ->assertSeeText('Current Rates')
            ->assertSeeText('Book Now')
            ->assertSeeText('Reserve a Slot')
            ->assertSeeText('Client User')
            ->assertSeeText('client@example.com')
            ->assertSeeText('Your reservation will be saved under your account name automatically.');
    }

    public function test_admin_booking_page_hides_reserve_slot_and_current_rates(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin.booking@example.com',
        ]);
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->get('/');

        $response
            ->assertOk()
            ->assertDontSeeText('Reserve a Slot')
            ->assertDontSeeText('Current Rates')
            ->assertDontSeeText('admin.booking@example.com')
            ->assertDontSeeText('Your reservation will be saved under your account name automatically.');
    }

    public function test_public_reservation_list_hides_customer_names_by_default(): void
    {
        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Hidden Public Name',
            'booking_date' => now()->toDateString(),
            'time_slot' => '6:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-HIDDEN-PUBLIC-001',
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSeeText('Reserved slot')
            ->assertDontSeeText('Hidden Public Name');
    }

    public function test_public_reservation_list_can_show_customer_names_when_enabled(): void
    {
        FacilitySetting::current()->update([
            'show_public_customer_names' => true,
        ]);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Visible Public Name',
            'booking_date' => now()->toDateString(),
            'time_slot' => '7:00 AM',
            'court_number' => 2,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-VISIBLE-PUBLIC-001',
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSeeText('Visible Public Name')
            ->assertDontSeeText('Walk-in / No account');
    }

    public function test_receipt_page_shows_the_verification_section_for_the_owner(): void
    {
        $user = User::factory()->create([
            'name' => 'Receipt Owner',
        ]);

        $reservation = Reservation::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'contact_number' => '09175554444',
            'booking_date' => now()->toDateString(),
            'time_slot' => '8:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'gcash',
            'payment_reference' => 'GCASH-VERIFY-001',
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-VERIFY-VIEW-001',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('reservations.receipt', $reservation, false));

        $response
            ->assertOk()
            ->assertSeeText('Verification Code')
            ->assertSeeText('Verify This Receipt')
            ->assertSeeText(implode(' ', str_split($reservation->verificationCode(), 4)));
    }

    public function test_public_receipt_verification_page_accepts_a_valid_receipt_code(): void
    {
        $reservation = Reservation::create([
            'user_id' => null,
            'customer_name' => 'Verified Customer',
            'contact_number' => '09176667777',
            'booking_date' => now()->toDateString(),
            'time_slot' => '9:00 AM',
            'court_number' => 2,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 600,
            'receipt_no' => 'RCPT-VERIFY-PUBLIC-001',
        ]);

        $response = $this->get(route('reservations.receipt.verify', [
            'receipt_no' => $reservation->receipt_no,
            'verification_code' => $reservation->verificationCode(),
        ], false));

        $response
            ->assertOk()
            ->assertSeeText('Receipt Verified')
            ->assertSeeText('Verified Customer')
            ->assertSeeText('RCPT-VERIFY-PUBLIC-001');
    }

    public function test_public_receipt_verification_page_rejects_an_invalid_code(): void
    {
        $reservation = Reservation::create([
            'user_id' => null,
            'customer_name' => 'Invalid Check Customer',
            'contact_number' => '09178889999',
            'booking_date' => now()->toDateString(),
            'time_slot' => '10:00 AM',
            'court_number' => 3,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 650,
            'receipt_no' => 'RCPT-VERIFY-PUBLIC-002',
        ]);

        $response = $this->get(route('reservations.receipt.verify', [
            'receipt_no' => $reservation->receipt_no,
            'verification_code' => 'WRONGCODE1234',
        ], false));

        $response
            ->assertOk()
            ->assertSeeText('Receipt Not Verified')
            ->assertDontSeeText('Invalid Check Customer');
    }

    public function test_booking_page_shows_current_court_and_rental_rates(): void
    {
        FacilitySetting::current()->update([
            'reservation_rate' => 650,
            'paddle_rent_rate' => 120,
            'ball_rate' => 80,
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSeeText('Court Rate: PHP 650.00')
            ->assertSeeText('Paddle Rent: PHP 120.00')
            ->assertSeeText('Ball Rate: PHP 80.00');
    }
}
