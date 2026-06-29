<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class SingleAddToCartTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->text('photo')->nullable();
            $table->integer('stock')->default(100);
            $table->double('price')->default(0);
            $table->double('discount')->default(0);
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->double('price')->default(0);
            $table->string('status')->default('new');
            $table->integer('quantity')->default(0);
            $table->double('amount')->default(0);
            $table->integer('size')->nullable();
            $table->timestamps();
        });
    }

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => bcrypt('secret'),
        ]);
        $this->actingAs($user)->withSession(['user' => $user->id]);

        return $user;
    }

    /** A 10% discounted product added with quantity 2 must charge the discounted line total. */
    public function test_new_cart_line_amount_uses_discounted_price(): void
    {
        $this->actingUser();

        $product = Product::create([
            'title' => 'Sneaker',
            'slug' => 'sneaker',
            'price' => 1000,
            'discount' => 10, // percent
            'stock' => 50,
        ]);

        $this->post(route('single-add-to-cart'), [
            'slug' => $product->slug,
            'quant' => [1 => 2],
            'size' => 40,
        ]);

        $cart = Cart::first();
        $this->assertNotNull($cart);
        // discounted unit price = 1000 - (1000*10/100) = 900; line total for qty 2 = 1800
        $this->assertEquals(900.0, (float) $cart->price);
        $this->assertEquals(1800.0, (float) $cart->amount);
    }

    /** Re-adding the same discounted product must add the discounted line total, not the full price. */
    public function test_existing_cart_line_amount_uses_discounted_price(): void
    {
        $user = $this->actingUser();

        $product = Product::create([
            'title' => 'Sneaker',
            'slug' => 'sneaker',
            'price' => 1000,
            'discount' => 10,
            'stock' => 50,
        ]);

        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 900,
            'quantity' => 1,
            'amount' => 900,
            'size' => 40,
        ]);

        $this->post(route('single-add-to-cart'), [
            'slug' => $product->slug,
            'quant' => [1 => 2],
            'size' => 40,
        ]);

        $cart = Cart::first();
        // existing 900 + discounted 900*2 = 2700
        $this->assertEquals(2700.0, (float) $cart->amount);
    }
}
