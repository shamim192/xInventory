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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('model')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();                
            $table->foreignId('base_unit_id')->constrained('base_units')->cascadeOnDelete();        
            $table->decimal('purchase_price', 14, 2)->default(0);            
            $table->decimal('mrp', 14, 2)->nullable();
            $table->decimal('discount_percentage', 14, 2)->nullable();
            $table->enum('status', ['Active','Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
