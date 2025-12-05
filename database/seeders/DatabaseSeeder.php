<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShipmentLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $roles = ['admin', 'buyer', 'cs1', 'cs2'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $firstUser = User::firstOrCreate([
            'email' => 'ecep@example.com'
        ], [
            'name' => 'Ecep Achmad Sutisna',
            'password' => bcrypt('Test@1234')
        ]);
        $firstUser->assignRole('admin');

        foreach ($roles as $roleName) {
            for ($i = 1; $i <= 10; $i++) {
                $user = User::firstOrCreate([
                    'email' => $roleName . $i . '@example.com',
                ], [
                    'name' => ucfirst($roleName) . " User $i",
                    'password' => bcrypt('password')
                ]);

                $user->assignRole($roleName);
            }
        }

        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'name' => $faker->randomElement(['Vivo', 'Oppo', 'Samsung', 'Eiger', 'Adidas', 'Nike'])
                    . " " . $faker->name(),
                'description' => $faker->sentence(),
                'price' => rand(500, 5000) * 1000,
                'stock' => rand(5, 50)
            ]);
        }

        $buyers = User::role('buyer')->get();
        $products = Product::all();

        foreach ($buyers as $buyer) {

            $selectedProducts = $products->random(rand(1, 3));
            $totalOrderAmount = 0;

            $cart = Cart::create([
                'user_id' => $buyer->id,
                'status' => $faker->randomElement(['active', 'checked_out', 'expired']),
            ]);

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $qty
                ]);
            }

            $order = Order::create([
                'user_id' => $buyer->id,
                'cart_id' => $cart->id,
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'status' => $faker->randomElement(['pending_payment', 'awaiting_verification', 'verified', 'packing', 'shipping', 'completed', 'cancelled']),
                'total_amount' => $totalOrderAmount,
                'expired_at' => Carbon::now()->addHours(24)
            ]);

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                $totalOrderAmount += $product->price * $qty;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $product->price
                ]);
            }


            $order->total_amount = $totalOrderAmount;
            $paymentStatus = $faker->randomElement(['pending', 'verified', 'rejected']);

            Payment::create([
                'order_id' => $order->id,
                'status' => $paymentStatus,
                'proof_path' => $paymentStatus !== 'pending' ? $faker->imageUrl() : ''
            ]);

            if ($paymentStatus === 'verified') {
                $order->status = "verified";
            } else if ($paymentStatus === 'rejected') {
                $order->status = "cancelled";
            } else {
                $order->status = "awaiting_verification";
            }

            $order->save();

            if ($paymentStatus === 'verified') {
                $shipmentStages = ['packing', 'shipped', 'delivered'];

                foreach ($shipmentStages as $stage) {
                    ShipmentLog::create([
                        'order_id' => $order->id,
                        'status' => $stage,
                        'notes' => "Order status updated to $stage",
                    ]);
                }
            }
        }
    }
}
