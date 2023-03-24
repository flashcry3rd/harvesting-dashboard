<?php

namespace App\Controllers;
use App\Models\Home_model;

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

        $arr = array(
            "menu" => "/dashboard"
        );
        session()->set($arr);

        // echo $tglAkhirMinggu5;
    
        echo view('index', $data);


    }

    public function getData()
    {
        $model = new Home_model(); 
        $date = date("Y-m-d");
        $year = date("Y");
    
        if(strtotime(date("Y-m-d H:i:s")) < strtotime(date("Y-m-d ")." 06:00:00") ){
            $dateBefore = date("Y-m-d", strtotime('-2 day', strtotime($date)))." 06:00:00";
            $dateNow = date("Y-m-d", strtotime('-1 day', strtotime($date)))." 06:00:00";
            $dateNow1 = date("Y-m-d")." 06:00:00" ;
        }else{
            $dateBefore = date("Y-m-d", strtotime('-1 day', strtotime($date)))." 06:00:00";
            $dateNow = date("Y-m-d")." 06:00:00" ;
            $dateNow1 = date("Y-m-d", strtotime('+1 day', strtotime($date)))." 06:00:00";
        }

        
        $tHariKemaren = "weight_out_time between '$dateBefore' and '$dateNow' " ;
        $tHariIni = "weight_out_time between '$dateNow' and '$dateNow1' " ;
        $tYear = "YEAR(weight_out_time) = '$year' ";

        //perjam 
        
        $hourNow = date("Y-m-d H:")."00:00";
        $hourLimit = date("Y-m-d H:")."59:59" ;
        $tPerJam = "weight_out_time between '$hourNow' and '$hourLimit'";

        //weekly

        $tglTerakhir = date('Y-m-d', strtotime(date('Y-m-t'))) ;
        $tglAwalBulan = date('Y-m-01')." 06:00:00" ;
        // Hari pertama bulan dalam angka PHP
        $isoTglAwal = date('N', strtotime(date('Y-m-01'))) ;
        $totalHariAwal = 0;

        for($i=$isoTglAwal;$i<=7;$i++){
            $totalHariAwal = $totalHariAwal + 1 ; 
            if($i==7){
                $tglMinggu = date("Y-m-d", strtotime("+$totalHariAwal day", strtotime(date("Y-m-01"))))." 06:00:00";
            }
        }
        //Minggu 1 
        $whereMinggu1 = "weight_out_time between '$tglAwalBulan' and '$tglMinggu'" ;
        $data['whereMinggu1'] = date("d", strtotime($tglAwalBulan))." s/d ".date("d F Y", strtotime($tglMinggu))  ;
        //Minggu 2
        $tglAwalMinggu2 = $tglMinggu ;
        $tglAkhirMinggu2 = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($tglMinggu))) ;
        $whereMinggu2 =  "weight_out_time between '$tglAwalMinggu2' and '$tglAkhirMinggu2'" ;
        $data['whereMinggu2'] = date("d", strtotime($tglAwalMinggu2))." s/d ".date("d F Y", strtotime($tglAkhirMinggu2))  ;
        //Minggu 3
        $tglAwalMinggu3 = $tglAkhirMinggu2 ;
        $tglAkhirMinggu3 = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($tglAkhirMinggu2))) ;
        $whereMinggu3 = "weight_out_time between '$tglAwalMinggu3' and '$tglAkhirMinggu3'" ;
        $data['whereMinggu3'] = date("d", strtotime($tglAwalMinggu3))." s/d ".date("d F Y", strtotime($tglAkhirMinggu3))  ;
        //Minggu 4
        $tglAwalMinggu4 = $tglAkhirMinggu3;
        $tglAkhirMinggu4 = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($tglAkhirMinggu3))) ;
        $whereMinggu4 = "weight_out_time between '$tglAwalMinggu4' and '$tglAkhirMinggu4'" ;
        $data['whereMinggu4'] = date("d", strtotime($tglAwalMinggu4))." s/d ".date("d F Y", strtotime($tglAkhirMinggu4))  ;
        //Minggu 5
        $tglAwalMinggu5 = $tglAkhirMinggu4 ;
        $tglAkhirMinggu5 = date("Y-m-d H:i:s", strtotime("+7 day", strtotime($tglAkhirMinggu4))) ;
        if($tglAkhirMinggu5 > $tglTerakhir){
            $tglAkhirMinggu5 = date("Y-m-d", strtotime("+1 day", strtotime($tglTerakhir)))." 06:00:00" ;
        }else{
            $tglAkhirMinggu5 = $tglAkhirMinggu5 ;
        }
        $whereMinggu5 = "weight_out_time between '$tglAwalMinggu5' and '$tglAkhirMinggu5'" ;
        $data['whereMinggu5'] = date("d", strtotime($tglAwalMinggu5))." s/d ".date("d F Y", strtotime($tglAkhirMinggu5))  ;

        //PER KONTRAKTOR
        
        

        ///////////////////////////

        $timbang1 = $model->getSelect("tbl_weight_scale", $tHariKemaren);
        $timbang2 = $model->getSelect("tbl_weight_scale", $tHariIni);
        $timbangAll = $model->getSelect("tbl_weight_scale", $tYear);
        $timbangHour = $model->getSelect("tbl_weight_scale", $tPerJam);
        $timbangMinggu1 = $model->getSelect("tbl_weight_scale", $whereMinggu1);
        $timbangMinggu2 = $model->getSelect("tbl_weight_scale", $whereMinggu2);
        $timbangMinggu3 = $model->getSelect("tbl_weight_scale", $whereMinggu3);
        $timbangMinggu4 = $model->getSelect("tbl_weight_scale", $whereMinggu4);
        $timbangMinggu5 = $model->getSelect("tbl_weight_scale", $whereMinggu5);

        $totalTimbang1 = 0;
        $totalTimbang2 = 0;
        $totalAll = 0 ;
        $totalHour = 0;
        $totalMinggu1 = 0;
        $totalMinggu2 = 0;
        $totalMinggu3 = 0;
        $totalMinggu4 = 0;
        $totalMinggu5 = 0;

        foreach($timbang1 as $t1){
            $totalTimbang1 += ($t1['weight_in'] - $t1['weight_out']);
        }
        foreach($timbang2 as $t2){
            $totalTimbang2 += ($t2['weight_in'] - $t2['weight_out']);
        }
        foreach($timbangAll as $ta){
            $totalAll += ($ta['weight_in'] - $ta['weight_out']); 
        }
        foreach($timbangHour as $th){
            $totalHour += ($th['weight_in'] - $th['weight_out']);
        }
        foreach($timbangMinggu1 as $tm1){
            $totalMinggu1 += ($tm1['weight_in'] - $tm1['weight_out']);
        }
        foreach($timbangMinggu2 as $tm2){
            $totalMinggu2 += ($tm2['weight_in'] - $tm2['weight_out']);
        }
        foreach($timbangMinggu3 as $tm3){
            $totalMinggu3 += ($tm3['weight_in'] - $tm3['weight_out']);
        }
        foreach($timbangMinggu4 as $tm4){
            $totalMinggu4 += ($tm4['weight_in'] - $tm4['weight_out']);
        }
        foreach($timbangMinggu5 as $tm5){
            $totalMinggu5 += ($tm5['weight_in'] - $tm5['weight_out']);
        }
        
        $data['timbang1'] = number_format($totalTimbang1, 2, ",", ".") ;
        $data['timbang2'] = number_format($totalTimbang2, 2, ",", ".") ;
        $data['timbangAll'] = number_format($totalAll, 2,",",".") ;
        $data['timbangHour'] = number_Format($totalHour, 2,",",".") ;
        $data['timbangMinggu1'] = $totalMinggu1 ;
        $data['timbangMinggu2'] = $totalMinggu2 ;
        $data['timbangMinggu3'] = $totalMinggu3 ;
        $data['timbangMinggu4'] = $totalMinggu4 ;
        $data['timbangMinggu5'] = $totalMinggu5 ;
        $data['jamNow'] = date("H").":00 - ".date("H").":59";

        echo json_encode($data);
    }

    public function getDataChart1()
    {
        $model = new Home_model();
        $year = date("Y");
        $tbl = "tbl_weight_scale";
        
        $where['year(weight_out_time)'] = $year;
        $like['jenis_tebu'] = "tebu hijau";
        $tebu_hijau = $model->getSelect($tbl, $where, "", $like);
        for($i=1;$i<=12;$i++){
            $data['totalTh'][$i] = 0;
        }
       
        foreach($tebu_hijau as $th)
        {
            for($i=1;$i<=12;$i++){
                if(strtotime($th['weight_out_time']) >= strtotime("$year-$i-01") && strtotime($th['weight_out_time']) <= strtotime("$year-$i-31")) {
                    $data['totalTh'][$i] += $th['weight_in'] - $th['weight_out'];    
                }
                
            }
        }

        
        $where1['year(weight_out_time)'] = $year;
        $like1['jenis_tebu'] = "tebu bakar";
        $tebu_bakar = $model->getSelect($tbl, $where1,"",$like1);
        for($i=1;$i<=12;$i++){
            $data['totalTb'][$i] = 0;
        }
       
        foreach($tebu_bakar as $tb)
        {
            for($i=1;$i<=12;$i++){
                if(strtotime($tb['weight_out_time']) >= strtotime("$year-$i-01") && strtotime($tb['weight_out_time']) <= strtotime("$year-$i-31")) {
                    $data['totalTb'][$i] += $tb['weight_in'] - $tb['weight_out'];    
                }
                
            }
        }
        
        echo json_encode($data);
        
    }
}
