<?php

namespace Tests\Feature\Shop;

use App\Models\Address;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationAndAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_contact_form_rejects_invalid_input(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_accepts_valid_input(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Awa Sarr',
            'email' => 'awa@example.com',
            'message' => 'Bonjour, avez-vous cette robe en taille L ?',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('contact_messages', ['email' => 'awa@example.com']);
    }

    public function test_checkout_information_requires_mandatory_fields(): void
    {
        $response = $this->post(route('checkout.information.store'), []);

        $response->assertSessionHasErrors([
            'name', 'email', 'phone', 'address_line', 'city', 'shipping_method_id',
        ]);
    }

    public function test_newsletter_rejects_invalid_email(): void
    {
        $this->post(route('newsletter.store'), ['email' => 'invalid'])
            ->assertSessionHasErrors('email');
    }

    public function test_client_cannot_edit_another_clients_address(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('client');
        $intruder = User::factory()->create();
        $intruder->assignRole('client');

        $address = Address::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)
            ->get(route('account.adresses.edit', $address))
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('account.adresses.destroy', $address))
            ->assertForbidden();

        $this->assertDatabaseHas('addresses', ['id' => $address->id]);
    }

    public function test_guest_cannot_submit_a_product_review(): void
    {
        $product = \App\Models\Product::factory()->create();

        $this->post(route('reviews.store', $product), ['rating' => 5])
            ->assertRedirect(route('login'));
    }

    public function test_client_cannot_review_the_same_product_twice(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');
        $product = \App\Models\Product::factory()->create();

        $this->actingAs($client)->post(route('reviews.store', $product), [
            'rating' => 5, 'comment' => 'Superbe !',
        ])->assertSessionHasNoErrors();

        $this->actingAs($client)->post(route('reviews.store', $product), [
            'rating' => 4, 'comment' => 'Nouvel essai',
        ])->assertForbidden();

        $this->assertSame(1, $product->reviews()->count());
    }
}
