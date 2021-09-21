<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChickensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chickens', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('bdate');
            $table->string('photo');
            $table->boolean('write')->default(0);
            $table->boolean('is_pisal')->default(0);
            $table->string('last_seen')->default('0');
            $table->string('age')->nullable()->default(0);
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
        Schema::dropIfExists('chickens');
    }
}
