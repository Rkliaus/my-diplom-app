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

 public function getVacancies($text = 'php', $area = 1, $page = 0, $perPage = 20, $city = null)
 {
     $query = [
         'text' => $text,
         'area' => $area,
         'page' => $page,
         'per_page' => $perPage,
     ];

     // Если город указан, добавляем его в запрос
     if ($city) {
         $query['area'] = $city;
     }

     $response = $this->client->get('vacancies', ['query' => $query]);
     return json_decode($response->getBody()->getContents(), true);
 }

}
