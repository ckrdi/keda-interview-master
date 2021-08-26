<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_fields_for_customer_authentication()
    {
        $this->seed('DatabaseSeeder');

        $this->post('api/customer/login', [
            'email' => '',
            'password' => '',
        ], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                    'password'
                ]
            ]);
    }

    public function test_required_fields_for_staff_authentication()
    {
        $this->seed('DatabaseSeeder');

        $this->post('api/staff/login', [
            'email' => '',
            'password' => '',
        ], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email',
                    'password'
                ]
            ]);
    }

    public function test_customers_can_authenticate()
    {
        $this->seed('DatabaseSeeder');

        $this->post('api/customer/login', [
            'email' => 'customerone@gmail.com',
            'password' => 'dummydummy',
        ], ['Accept' => 'application/json'])
            ->assertCreated();
    }

    public function test_customers_can_not_authenticate_with_invalid_password()
    {
        $this->seed('DatabaseSeeder');

        $this->post('api/customer/login', [
            'email' => 'customerone@gmail.com',
            'password' => 'password',
        ])->assertStatus(401);
    }

    public function test_staffs_can_authenticate()
    {
        $this->seed('DatabaseSeeder');

        $this->post('api/staff/login', [
            'email' => 'staffone@gmail.com',
            'password' => 'dummydummy',
        ])->assertCreated();
    }

    public function test_staffs_can_not_authenticate_with_invalid_password()
    {
        $this->seed('DatabaseSeeder');

        $this->post('api/staff/login', [
            'email' => 'staffone@gmail.com',
            'password' => 'password',
        ])->assertStatus(401);
    }

    public function test_customers_can_log_out()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 1
        ]));
        
        $this->post('api/customer/logout')->assertSuccessful();
    }

    public function test_staffs_can_log_out()
    {
        $this->seed('DatabaseSeeder');

        Passport::actingAs(User::factory()->create([
            'user_type_id' => 2
        ]));
        
        $this->post('api/staff/logout')->assertSuccessful();
    }
}
