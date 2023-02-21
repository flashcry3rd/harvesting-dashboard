<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {  
        $client = \Config\Services::curlrequest();
        $response = $client->request("get", 'https://api.open-meteo.com/v1/forecast?latitude=-4.0074708&longitude=105.7665133&current_weather=true&hourly=temperature_2m,relativehumidity_2m,windspeed_10m', [
			"headers" => [
				"Accept" => "application/json"
			]
		]);
      
        $row = json_decode($response->getBody());
        $data['temp'] = array(
          'temperature' =>  $row->current_weather->temperature,
          'time' => $row->current_weather->time,
          'windspeed' => $row->current_weather->windspeed,
          
        );
    
        echo view('index', $data);


    }
}
