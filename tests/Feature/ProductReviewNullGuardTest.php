<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductReview;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductReviewNullGuardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('role')->default('user');
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

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('rate')->default(5);
            $table->text('review')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    private function actingUser(): User
    {
        $user = User::create([
            'name' => 'Reviewer',
            'email' => 'reviewer@example.com',
            'password' => bcrypt('secret'),
            'role' => 'user',
        ]);
        $this->actingAs($user)->withSession(['user' => $user->id]);

        return $user;
    }

    public function test_store_review_invalid_product_slug_flashes_error(): void
    {
        $this->actingUser();

        $response = $this->post(route('review.store', ['slug' => 'non-existent-product']), [
            'rate' => 5,
            'review' => 'Great product!',
        ]);

        $response->assertSessionHas('error', 'Sản phẩm không tồn tại!');
    }

    public function test_destroy_review_invalid_id_flashes_error(): void
    {
        $this->actingUser();

        $response = $this->delete(route('review.destroy', ['review' => 9999]));

        $response->assertSessionHas('error', 'Không tìm thấy đánh giá!');
    }
}
