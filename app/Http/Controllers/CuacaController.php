<?php

namespace App\Http\Controllers;

use App\Models\API_Cuaca; 
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CuacaController extends Controller
{
    public function index(): JsonResponse
    {
        $suhuMax = API_Cuaca::max('suhu'); 
        $suhuMin = API_Cuaca::min('suhu'); 
        $suhuRata = API_Cuaca::average('suhu'); 

        $nilaiSuhuHumidMax = API_Cuaca::where('suhu', $suhuMax) 
            ->where('humid', API_Cuaca::max('humid')) 
            ->select('id as idx', 'suhu as suhun', 'humid', 'lux as kecerahan', 'ts as timestamp')
            ->get();

        $monthYearMax = API_Cuaca::select(DB::raw("DATE_FORMAT(ts, '%c-%Y') as month_year")) 
            ->groupBy(DB::raw("DATE_FORMAT(ts, '%c-%Y')"))
            ->orderBy('month_year', 'desc')
            ->limit(2)
            ->get();

        $responseData = [
            'suhumax' => $suhuMax,
            'suhumin' => $suhuMin,
            'suhurata' => round($suhuRata, 2),
            'nilai_suhu_max_humid_max' => $nilaiSuhuHumidMax,
            'month_year_max' => $monthYearMax,
        ];

        return response()->json($responseData);
    }
}
