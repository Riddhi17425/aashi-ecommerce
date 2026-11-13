<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // "Notify me" requests from visitors for out-of-stock products.
    public function up(): void
    {
        Schema::create('product_stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('email');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_notifications');
    }
};
