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
        Schema::create('pagination_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->string('table_name');
            $table->integer('at_page');
            $table->integer('success_code')->comment("0 => Failed,1=>success");
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
        Schema::dropIfExists('pagination_logs');
    }
};
