<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Coupon;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class CouponDiscountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->double('price')->default(0);
            $table->string('status')->default('new');
            $table->integer('quantity')->default(0);
            $table->double('amount')->default(0);
            $table->integer('size')->nullable();
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('type')->default('fixed');
            $table->decimal('value', 20, 2);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * A percent coupon must discount the cart's line totals (amount = price * quantity),
     * not the sum of unit prices, otherwise carts with quantity > 1 are under-discounted.
     */
    public function test_percent_coupon_discounts_line_totals_not_unit_prices(): void
    {
        $user = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => bcrypt('secret'),
        ]);
        $this->actingAs($user)->withSession(['user' => $user->id]);

        // One line: unit price 500, quantity 2 => line total 1000.
        Cart::create([
            'user_id' => $user->id,
            'product_id' => 1,
            'order_id' => null,
            'price' => 500,
            'quantity' => 2,
            'amount' => 1000,
            'size' => 40,
        ]);

        Coupon::create([
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
            'status' => 'active',
        ]);

        $this->post(route('coupon-store'), ['code' => 'SAVE10']);

        $coupon = session('coupon');
        $this->assertNotNull($coupon);
        // 10% of the 1000 line total, not 10% of the 500 unit price.
        $this->assertEquals(100.0, (float) $coupon['value']);
    }
}
