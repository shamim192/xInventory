<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockReturnItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_return_items', function (Blueprint $table) {
            $table->id();           
            $table->foreignId('stock_return_id')->constrained('stock_returns')->cascadeOnDelete();            
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();             
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();  
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
        Schema::dropIfExists('stock_return_items');
    }
}
