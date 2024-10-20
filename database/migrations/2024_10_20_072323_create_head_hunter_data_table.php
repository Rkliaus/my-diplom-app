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
            $table->string('vacancy_id')->unique(); // ID вакансии из API
            $table->string('name');                 // Название вакансии
            $table->string('area_id');              // ID региона/города
            $table->string('area_name');            // Название региона/города
            $table->decimal('salary_from', 10, 2)->nullable(); // Зарплата "от"
            $table->decimal('salary_to', 10, 2)->nullable();   // Зарплата "до"
            $table->string('currency')->nullable(); // Валюта зарплаты
            $table->string('employer_name')->nullable(); // Название работодателя
            $table->timestamp('published_at');      // Дата публикации
            $table->timestamps();                   // Стандартные поля created_at и updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('head_hunter_data');
    }
}
