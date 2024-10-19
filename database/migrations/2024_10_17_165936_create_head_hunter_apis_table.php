<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadHunterApisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('head_hunter_apis', function (Blueprint $table) {
            $table->id();
            $table->string('text')->nullable();
            $table->integer('area')->default(1);
            $table->integer('page')->default(0);
            $table->integer('per_page')->default(20);
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('head_hunter_apis');
    }
}
