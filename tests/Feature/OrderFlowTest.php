<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

class OrderFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'email' => 'testbuyer@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->buyer->assignRole('buyer');
    }

    public function test_buyer_can_add_to_cart()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($this->buyer)->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertStatus(200);
    }

    public function test_checkout()
    {
        $cart = Cart::create([
            'user_id' => $this->buyer->id,
            'status' => 'checked_out',
        ]);
        $products = Product::all();
        $selectedProducts = $products->random(rand(1, 3));
        foreach ($selectedProducts as $product) {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        $response = $this->actingAs($this->buyer)->postJson('/api/order/checkout');

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'order_id']);
    }
}
