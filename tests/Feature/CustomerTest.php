<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_can_see_their_messages()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 1
        ]));

        $this->get('api/customer/messages')
            ->assertSuccessful()
            ->assertJsonStructure([
                'sent_messages',
                'received_messages'
            ]);
    }

    public function test_required_fields_for_creating_a_message()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 1
        ]));

        $this->post('api/customer/messages', [
            'subject' => '',
            'message' => '',
            'recipient' => ''
        ], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'subject',
                    'message',
                    'recipient'
                ]
            ]);
    }

    public function test_customers_can_create_a_message()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 1
        ]));

        $this->post('api/customer/messages', [
            'subject' => 'Test',
            'message' => 'Test',
            'recipient' => 1
        ], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonStructure([
                'sender_id',
                'sent_to_id',
                'subject',
                'message'
            ]);
    }

    public function test_customers_can_not_create_a_message_for_themselves()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 1
        ])); // the id is 5 because a newly created user

        $this->post('api/customer/messages', [
            'subject' => 'Test',
            'message' => 'Test',
            'recipient' => 5
        ], ['Accept' => 'application/json'])
            ->assertStatus(405)
            ->assertJson([
                'message' => 'You cannot send message to yourself'
            ]);
    }
}
