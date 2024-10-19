<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
class HeadHunterApi extends Model
{
    protected $client;

    public function __construct()
 {
  $this->client = new Client([
   'base_uri' => 'https://api.hh.ru/', // базовый URI API
  ]);
 }

public function getVacancies($text = 'php', $area = 1, $page = 0, $perPage = 20)
{
    $response = $this->client->get('vacancies', [
        'query' => [
            'text' => $text,
            'area' => $area,
            'page' => $page,
            'per_page' => $perPage,
        ]
    ]);
    return json_decode($response->getBody()->getContents(), true);
}
}
