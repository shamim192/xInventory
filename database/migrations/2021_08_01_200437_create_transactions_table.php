<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Received', 'Payment','Adjustment']);
            $table->enum('flag', ['Invest', 'Expense', 'Supplier Payment', 'Customer Payment', 'Fund Transfer','Income','Loan']);
            $table->unsignedBigInteger('flagable_id');
            $table->string('flagable_type');           
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete(); 
            $table->dateTime('datetime');
            $table->text('note')->nullable();
            $table->decimal('amount', 10, 2);
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
        Schema::dropIfExists('transactions');
    }
}
