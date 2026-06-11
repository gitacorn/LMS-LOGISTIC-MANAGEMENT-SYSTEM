<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortToAgentConatinerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('constants.PORT_TO_AGENT_CONTAINER_INFO_TABLE'), function (Blueprint $table) {
            $table->increments('i_id');
            $table->integer('i_port_to_agent_goods_out_master_id');
            $table->integer('i_container_id');
            $table->enum('e_container_status',[config('constants.COMPLETED_STATUS'),config('constants.PENDING_STATUS'),config('constants.PARTIAL_DELIVERY_TYPE')])->default(config('constants.PENDING_STATUS'));
            $table->tinyInteger('t_is_active')->default('1');
            $table->tinyInteger('t_is_deleted')->default('0');
            $table->integer('i_created_id');
            $table->dateTime('dt_created_at');
            $table->integer('i_updated_id')->nullable();;
            $table->dateTime('dt_updated_at')->nullable();
            $table->integer('i_deleted_id')->nullable();
            $table->dateTime('dt_deleted_at')->nullable();
            $table->ipAddress('v_ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('port_to_agent_conatiner_info');
    }
}
