<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');          
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete(); 
            $table->date('date');
            $table->unsignedBigInteger('total_quantity')->default(0);
            $table->decimal('subtotal_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('vat_percent')->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('flat_discount_percent')->default(0);
            $table->decimal('flat_discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
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
        Schema::dropIfExists('sales');
    }
}
