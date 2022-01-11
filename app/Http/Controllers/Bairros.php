<?php

namespace App\Http\Controllers;

ini_set('max_execution_time', 72000);

use App\Jobs\SendWpp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Bairros extends Controller
{
    public function show(Request $request , $bairro = null)
    {

        $listaTotal = json_decode(file_get_contents('../database/db.json'), true);

        if($bairro != null){

            $bairro = strtoupper($bairro);

            $lista = array_count_values(array_column($listaTotal, 'bairro'));

            return response()->json(array("total" => $lista[$bairro]));

        }

        $lista = array_count_values(array_column($listaTotal, 'bairro'));

        $lista['TODOS'] = count($listaTotal);

        arsort($lista, SORT_NUMERIC);

        //$lista = array_slice($lista, 0, 16);
        
        return response()->json($lista);
       
    }

    public function listaBairros(){

        //info('listaBairros');
            
        $listaTotal = json_decode(file_get_contents('../database/db.json'), true);
    
        $lista = array_count_values(array_column($listaTotal, 'bairro'));

        $lista['TODOS'] = count($listaTotal);

        arsort($lista, SORT_NUMERIC);

        //$lista = array_slice($lista, 0, 16);

        $lista = array_keys($lista);

        return response()->json($lista);
        
    }

    public function enviar(Request $request){


        $item = json_decode($request->getContent(), true);

        $listaTotal = json_decode(file_get_contents('../database/db.json'), true);

        $url = "http://10.19.92.66:21465/api/guaranta/send-message";
        $urlImage = "http://10.19.92.66:21465/api/guaranta/send-file-base64";
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer $2b$10$O0Pu0jWp7yWseQfHnWlQIu8PoseQq6sdeJjkq6klzVuqWDyJg0Jgm'
        );

        $img = file_get_contents("../database/img.jpeg");

        $img = base64_encode($img);


        \App\Jobs\SendWpp::dispatch($listaTotal, $item, $url, $urlImage, $header , $img);

        
        return response()->json($item);


    }


    public function verificaStatus(){

        $cont = DB::table('contador')->where('id', 1)->get();


        return response()->json($cont);

    }

}
