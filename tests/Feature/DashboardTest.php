<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_users_can_view_their_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('My Dashboard')
            ->assertSeeText('Book Now')
            ->assertDontSee('Admin Dashboard');
    }

    public function test_admin_dashboard_shows_modern_summary_cards(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $customer = User::factory()->create([
            'name' => 'Juan Dela Cruz',
        ]);

        Reservation::create([
            'user_id' => $customer->id,
            'customer_name' => $customer->name,
            'booking_date' => now()->toDateString(),
            'time_slot' => '6:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'gcash',
            'payment_reference' => 'GCASH-1234',
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-TEST-001',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('Admin Dashboard')
            ->assertSeeText('Book Now')
            ->assertSeeText('Visitors')
            ->assertSeeText('Users')
            ->assertSeeText('Booking')
            ->assertSeeText('Repeat Booking')
            ->assertDontSeeText('Registered Users')
            ->assertDontSeeText('Paid Income')
            ->assertSeeText('Juan Dela Cruz');
    }

    public function test_admin_route_is_available_for_admin_users(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->get('/admin');

        $response
            ->assertOk()
            ->assertSee('Admin Dashboard');
    }

    public function test_admin_route_is_forbidden_for_non_admin_users(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_dashboard_shows_range_report_with_income_and_names(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Walk In Ana',
            'booking_date' => now()->subDay()->toDateString(),
            'time_slot' => '6:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-REPORT-001',
        ]);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Walk In Ben',
            'booking_date' => now()->subDay()->toDateString(),
            'time_slot' => '7:00 AM',
            'court_number' => 2,
            'players' => 2,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Unpaid',
            'amount' => 500,
            'receipt_no' => 'RCPT-REPORT-002',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin?panel=income&start_date=' . now()->subDay()->toDateString() . '&end_date=' . now()->toDateString());

        $response
            ->assertOk()
            ->assertSee('Income Report')
            ->assertSeeText('Registered Users')
            ->assertSeeText('Paid Income')
            ->assertSee('Walk In Ana')
            ->assertSee('Walk In Ben')
            ->assertSee('PHP 500.00');
    }

    public function test_admin_analytics_panel_shows_modern_booking_summary_cards(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Analytics Walk In',
            'booking_date' => now()->subDay()->toDateString(),
            'time_slot' => '6:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-ANALYTICS-001',
        ]);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Analytics Walk In',
            'booking_date' => now()->toDateString(),
            'time_slot' => '7:00 AM',
            'court_number' => 2,
            'players' => 2,
            'payment_method' => 'gcash',
            'payment_reference' => 'ANALYTICS-REF',
            'payment_status' => 'Paid',
            'amount' => 650,
            'receipt_no' => 'RCPT-ANALYTICS-002',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin?panel=analytics&start_date=' . now()->subDay()->toDateString() . '&end_date=' . now()->toDateString());

        $response
            ->assertOk()
            ->assertSeeText('Booking Analytics')
            ->assertSeeText('Visitors')
            ->assertSeeText('Users')
            ->assertSeeText('Booking')
            ->assertSeeText('Repeat Booking')
            ->assertSeeText('Unique customers in the selected range.')
            ->assertSeeText('Extra bookings created by returning customers.')
            ->assertSeeText('Daily Booking Trend')
            ->assertSeeText('Demand Snapshot')
            ->assertSeeText('Revenue Pulse')
            ->assertSeeText(now()->subDay()->format('M d, Y'))
            ->assertSeeText(now()->format('M d, Y'))
            ->assertSeeText('Analytics')
            ->assertDontSeeText('Registered Users');
    }

    public function test_admin_monitor_can_search_customer_name(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Ana Search',
            'booking_date' => now()->toDateString(),
            'time_slot' => '6:00 AM',
            'court_number' => 1,
            'players' => 4,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-MONITOR-001',
        ]);

        Reservation::create([
            'user_id' => null,
            'customer_name' => 'Ben Hidden',
            'booking_date' => now()->toDateString(),
            'time_slot' => '7:00 AM',
            'court_number' => 2,
            'players' => 2,
            'payment_method' => 'cash',
            'payment_reference' => null,
            'payment_status' => 'Paid',
            'amount' => 500,
            'receipt_no' => 'RCPT-MONITOR-002',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get('/admin?panel=monitor&date=' . now()->toDateString() . '&customer=Ana');

        $response
            ->assertOk()
            ->assertSee('Reservation Monitor')
            ->assertSee('Ana Search')
            ->assertDontSee('Ben Hidden');
    }

    public function test_admin_dashboard_shows_walk_in_form_and_rates_panel(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->get('/admin?panel=rates');

        $response
            ->assertOk()
            ->assertSeeText('Rates & Rentals')
            ->assertSee('Court booking rate');
    }

    public function test_admin_rates_panel_shows_public_reservation_visibility_control(): void
    {
        $adminRole = Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);

        $response = $this
            ->actingAs($admin)
            ->get('/admin?panel=rates');

        $response
            ->assertOk()
            ->assertSeeText('Public Reservation List')
            ->assertSeeText('Show customer names only on the public reservation list');
    }
}
