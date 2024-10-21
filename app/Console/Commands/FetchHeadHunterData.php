<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\HeadHunterApiController;
use Illuminate\Support\Facades\App;

class FetchHeadHunterData extends Command
{
    protected $signature = 'headhunter:fetch'; // Команда, которая будет вызвана

    protected $description = 'Fetch vacancies from HeadHunter API and save to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Используем контроллер, чтобы вызвать его метод fetchData
        $controller = App::make(HeadHunterApiController::class);
        $controller->fetchData(new \Illuminate\Http\Request());
        $this->info('Data fetched and saved successfully');
    }
}
