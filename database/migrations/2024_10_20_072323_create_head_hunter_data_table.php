<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadHunterDataTable extends Migration
{

    public function up()
    {
        Schema::create('head_hunter_data', function (Blueprint $table) {
            $table->id();
            $table->string('hh_id')->unique()->nullable(); // Уникальный идентификатор вакансии с HeadHunter
            $table->string('title'); // Название вакансии
            $table->string('company'); // Название компании
            $table->string('salary')->nullable(); // Диапазон зарплат или NULL
            $table->string('country'); // Страна или регион
            $table->string('city')->nullable(); // Город (может отсутствовать)
            $table->timestamp('published_at'); // Дата публикации вакансии
            $table->timestamps(); // Временные метки created_at и updated_at
        });
    }


    public function down()
    {
        Schema::dropIfExists('head_hunter_data');
    }
}
