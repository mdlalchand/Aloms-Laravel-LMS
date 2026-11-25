<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('coupons', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('type')->default('percent'); // percent|fixed
            $t->decimal('value', 10, 2)->default(0);
            $t->decimal('min_amount', 10, 2)->default(0);
            $t->unsignedInteger('usage_limit')->default(0);
            $t->unsignedInteger('used_count')->default(0);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_no')->unique();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $t->decimal('subtotal', 10, 2)->default(0);
            $t->decimal('discount', 10, 2)->default(0);
            $t->decimal('total', 10, 2)->default(0);
            $t->string('status')->default('pending'); // pending|paid|failed|cancelled|refunded
            $t->string('name')->nullable();
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->text('address')->nullable();
            $t->string('note')->nullable();
            $t->timestamps();
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('item_type'); // course|ebook|product
            $t->unsignedBigInteger('item_id');
            $t->string('title');
            $t->decimal('price', 10, 2)->default(0);
            $t->unsignedInteger('qty')->default(1);
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('gateway')->default('bkash');
            $t->string('transaction_id')->nullable();
            $t->string('sender_number')->nullable();
            $t->decimal('amount', 10, 2)->default(0);
            $t->string('status')->default('pending'); // pending|verified|rejected
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
    }
};
