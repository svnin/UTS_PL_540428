<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_products()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_product()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
        ]);
        
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/products', [
                             'name' => 'Laptop Test',
                             'price' => 15000000,
                             'stock' => 5
                         ]);

        $response->assertStatus(201);
    }
}