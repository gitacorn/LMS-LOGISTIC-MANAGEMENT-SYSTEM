<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->text('v_announcement_text');
            $table->date('dt_expiry_date');
            $table->boolean('t_is_active')->default(1);
            $table->integer('i_created_by')->nullable();
            $table->integer('i_updated_by')->nullable();
            $table->timestamps();
            
            $table->index(['dt_expiry_date', 't_is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}
