<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ScrapingController;
use Illuminate\Support\Facades\Http;

class CoverageController extends Controller
{
    public function index()
    {
        return view('coverage.index');
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $type = $request->get('type', 'all'); // all, ligue_ai, tip_brasil
        
        $results = [
            'ligue_ai' => [],
            'tip_brasil' => [],
            'via_cep' => null
        ];

        // Buscar no Ligue Aí
        if ($type === 'all' || $type === 'ligue_ai') {
            $ligueAiData = ScrapingController::getLigueAiData();
            $results['ligue_ai'] = array_filter($ligueAiData, function($item) use ($query) {
                return stripos($item['municipio'], $query) !== false || 
                       stripos($item['uf'], $query) !== false;
            });
        }

        // Buscar na TIP Brasil
        if ($type === 'all' || $type === 'tip_brasil') {
            $tipBrasilData = ScrapingController::getTipBrasilData();
            $results['tip_brasil'] = array_filter($tipBrasilData, function($item) use ($query) {
                return stripos($item['municipio'], $query) !== false || 
                       stripos($item['sigla_estado'], $query) !== false;
            });
        }

        // Se for um CEP, buscar no ViaCEP
        if (preg_match('/^\d{5}-?\d{3}$/', $query)) {
            try {
                $cepResponse = Http::get("https://viacep.com.br/ws/{$query}/json/");
                if ($cepResponse->successful()) {
                    $cepData = $cepResponse->json();
                    if (!isset($cepData['erro'])) {
                        $results['via_cep'] = $cepData;
                        
                        // Buscar cobertura para a cidade encontrada
                        if (isset($cepData['localidade'])) {
                            $ligueAiData = ScrapingController::getLigueAiData();
                            $results['ligue_ai'] = array_filter($ligueAiData, function($item) use ($cepData) {
                                return stripos($item['municipio'], $cepData['localidade']) !== false || 
                                       stripos($item['uf'], $cepData['uf']) !== false;
                            });
                            
                            $tipBrasilData = ScrapingController::getTipBrasilData();
                            $results['tip_brasil'] = array_filter($tipBrasilData, function($item) use ($cepData) {
                                return stripos($item['municipio'], $cepData['localidade']) !== false || 
                                       stripos($item['sigla_estado'], $cepData['uf']) !== false;
                            });
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignorar erros do ViaCEP
            }
        }

        return response()->json($results);
    }

    public function getLigueAiData()
    {
        $data = ScrapingController::getLigueAiData();
        return response()->json($data);
    }

    public function getTipBrasilData()
    {
        $data = ScrapingController::getTipBrasilData();
        return response()->json($data);
    }
}
