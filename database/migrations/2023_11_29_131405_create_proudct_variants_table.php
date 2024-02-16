<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proudct_variants', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();           
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->decimal('purchase_price',14,2);
            $table->decimal('mrp',14,2);
            $table->decimal('retail_price',14,2);
            $table->decimal('trade_price',14,2);
            $table->integer('alert_quantity');
            $table->string('model')->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proudct_variants');
    }
};
