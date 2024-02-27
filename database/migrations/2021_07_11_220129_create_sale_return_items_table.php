<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();          
            $table->foreignId('sale_return_id')->constrained('sale_returns')->cascadeOnDelete();            
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();             
            $table->foreignId('sale_item_id')->constrained('sale_items')->cascadeOnDelete();            
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();           
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();           
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete(); 
            $table->decimal('unit_quantity', 10, 3);
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('amount', 10, 2);
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
        Schema::dropIfExists('sale_return_items');
    }
}
