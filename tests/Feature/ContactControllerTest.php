<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\SetUpRoles;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase, SetUpRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
        Mail::fake();
    }

    public function test_send_sends_contact_email_to_admin(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('Administrateur');

        $response = $this->actingAs($user)->postJson(route('contact'), [
            'subject' => 'Test Subject',
            'text' => 'Test Message',
        ]);

        $response->assertStatus(200);
        $response->assertJson(true);

        Mail::assertQueued(\App\Mail\ContactAdministrateur::class);
    }

    public function test_send_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('contact'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['subject', 'text']);
    }
}

