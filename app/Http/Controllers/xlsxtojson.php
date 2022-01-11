<?php


namespace App\Http\Controllers;

ini_set('memory_limit', '-1');

use Illuminate\Http\Request;
use SimpleXLSX;

class xlsxtojson extends Controller
{
    public function index()
    {
        if ( $xlsx = SimpleXLSX::parse('../database/db.xlsx') ) {
            // Produce array keys from the array values of 1st array element
            $header_values = $rows = [];
            foreach ( $xlsx->rows() as $k => $r ) {
                if ( $k === 0 ) {
                    $header_values = $r;
                    continue;
                }
                $rows[] = array_combine( $header_values, $r );
            }
        }

        return response()->json($rows);
    }
}
