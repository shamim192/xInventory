<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();           
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();         
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();         
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete(); 
            $table->decimal('unit_quantity', 10, 3)->default(0);
            $table->decimal('quantity', 10, 3)->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('discount_percentage', 14, 2)->default(0);
            $table->decimal('flat_discount_percentage', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);         
            $table->decimal('flat_discount_amount', 14, 2)->default(0);         
            $table->decimal('net_unit_price', 14, 2)->default(0);         
            $table->decimal('net_price', 14, 2)->default(0); 
            $table->decimal('amount', 14, 2)->default(0); 
            $table->decimal('actual_quantity', 10, 3);
            $table->timestamps();           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
}
