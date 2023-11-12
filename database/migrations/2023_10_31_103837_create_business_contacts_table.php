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
        Schema::create('business_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link');
            $table->string('address');
            $table->string('contact');
            $table->integer('cat_id');
            $table->string('cat_name');
            $table->integer('real_page');
            $table->string('web_page');
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
        Schema::dropIfExists('business_contacts');
    }
};
