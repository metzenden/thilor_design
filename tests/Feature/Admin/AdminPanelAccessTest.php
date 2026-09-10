<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_client_cannot_access_admin_panel(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $this->actingAs($client)->get('/admin')->assertForbidden();
    }

    public function test_admin_can_access_dashboard_and_key_resources(): void
    {
        $admin = User::where('email', 'admin@thilor-design.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertOk();

        foreach ([
            '/admin/products',
            '/admin/categories',
            '/admin/collections',
            '/admin/orders',
            '/admin/coupons',
            '/admin/shipping-methods',
            '/admin/banners',
            '/admin/pages',
            '/admin/reviews',
            '/admin/customers',
            '/admin/admins',
            '/admin/newsletter-subscribers',
            '/admin/contact-messages',
            '/admin/settings',
            '/admin/activity-log',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }
    }

    public function test_manager_cannot_access_admins_or_settings(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $this->actingAs($manager)->get('/admin/admins')->assertForbidden();
        $this->actingAs($manager)->get('/admin/settings')->assertForbidden();
        $this->actingAs($manager)->get('/admin/products')->assertOk();
    }
}
