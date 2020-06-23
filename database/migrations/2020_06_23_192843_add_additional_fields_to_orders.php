<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_price', 8, 2);
            $table->integer('quantity');
            $table->string('currency');
            $table->string('attribute_name');
            $table->string('attribute_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total_price');
            $table->dropColumn('quantity');
            $table->dropColumn('currency');
            $table->dropColumn('attribute_name');
            $table->dropColumn('attribute_value');
        });
    }
}
