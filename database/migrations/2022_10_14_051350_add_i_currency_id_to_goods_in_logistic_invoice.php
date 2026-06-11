<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddICurrencyIdToGoodsInLogisticInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('constants.GOODS_IN_LOGISTIC_INVOICE_TABLE'), function (Blueprint $table) {
            $table->integer('i_currency_id')->nullable()->after('d_total_charge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
