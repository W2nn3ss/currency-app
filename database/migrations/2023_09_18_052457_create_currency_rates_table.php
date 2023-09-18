<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('currency_code');
            $table->decimal('rate', 10, 4);
            $table->decimal('previous_rate', 10, 4)->nullable();
            $table->decimal('rate_difference', 10, 4)->nullable();
            $table->timestamps();
            $table->unique(['date', 'currency_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_rates');
    }
};
