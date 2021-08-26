<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_staffs_can_see_their_messages_and_all_messages()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));

        $this->get('api/staff/messages')
            ->assertSuccessful()
            ->assertJsonStructure([
                'all_messages',
                'sent_messages',
                'received_messages'
            ]);
    }

    public function test_required_fields_for_creating_a_message()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));

        $this->post('api/staff/messages', [
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

    public function test_staffs_can_create_a_message()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));

        $this->post('api/staff/messages', [
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
            'user_type_id' => 2
        ])); // the id is 5 because a newly created user

        $this->post('api/staff/messages', [
            'subject' => 'Test',
            'message' => 'Test',
            'recipient' => 5
        ], ['Accept' => 'application/json'])
            ->assertStatus(405)
            ->assertJson([
                'message' => 'You cannot send message to yourself'
            ]);
    }

    public function test_staffs_can_see_all_customers()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));

        $this->get('api/staff/customers')
            ->assertSuccessful()
            ->assertJsonStructure([
                'customers'
            ]);
    }

    public function test_staffs_can_delete_a_customer()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));

        $this->delete('api/staff/customers/1')
            ->assertSuccessful()
            ->assertJson([
                'message' => 'Successfully deleted'
            ]);
    }

    public function test_staffs_can_restore_a_customer()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));

        $this->delete('api/staff/customers/1');

        $this->post('api/staff/customers/1')
            ->assertSuccessful()
            ->assertJsonStructure([
                'customer',
                'message'
            ]);
    }
}
