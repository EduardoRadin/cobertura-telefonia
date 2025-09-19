<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\LigueAiCoverage;
use App\Models\TipBrasilCoverage;

class ScrapingController extends Controller
{
    public function scrapeLigueAi()
    {
        try {
            $response = Http::get('https://suporte-ligueai.tomticket.com/kb/telefonia-itx-e-portabilidade/cobertura-ligue-ai');
            
            if (!$response->successful()) {
                return response()->json(['error' => 'Falha ao acessar o site'], 500);
            }

            $crawler = new Crawler($response->body());
            
            $ligueAiData = [];
            
            // Procurar por todas as linhas da tabela (tr)
            $rows = $crawler->filter('tr');
            $count = 0;
            
            $rows->each(function (Crawler $row, $i) use (&$count, &$ligueAiData) {
                $cells = $row->filter('td');
                
                // Verificar se a linha tem pelo menos 3 células
                if ($cells->count() >= 3) {
                    $uf = trim($cells->eq(0)->text());
                    $cn = trim($cells->eq(1)->text());
                    $municipio = trim($cells->eq(2)->text());
                    
                    // Verificar se não é uma linha vazia ou cabeçalho
                    if (!empty($uf) && !empty($cn) && !empty($municipio) && 
                        $uf !== 'UF' && $cn !== 'CN' && $municipio !== 'MUNICIPIO') {
                        
                        $ligueAiData[] = [
                            'id' => $count + 1,
                            'uf' => $uf,
                            'cn' => $cn,
                            'municipio' => $municipio,
                        ];
                        $count++;
                    }
                }
            });

            // Armazenar no cache por 1 hora
            Cache::put('ligue_ai_data', $ligueAiData, 3600);

            return response()->json([
                'message' => 'Dados do Ligue Aí importados com sucesso',
                'count' => count($ligueAiData)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchTipBrasil()
    {
        try {
            $response = Http::get('https://cobertura.tipbrasil.com.br/php_consultaAreasAtendidas.php');
            
            if (!$response->successful()) {
                return response()->json(['error' => 'Falha ao acessar a API TIP Brasil'], 500);
            }

            $data = $response->json();
            
            $tipBrasilData = [];
            
            if (is_array($data)) {
                foreach ($data as $item) {
                    $tipBrasilData[] = [
                        'id' => count($tipBrasilData) + 1,
                        'municipio' => $item['municipio'] ?? '',
                        'cnl_al' => $item['cnlAl'] ?? '',
                        'sigla_estado' => $item['siglaEstado'] ?? '',
                        'cn' => $item['cn'] ?? '',
                        'cnl' => $item['cnl'] ?? '',
                        'maestro' => $item['Maestro'] ?? '',
                        'area_local' => $item['areaLocal'] ?? '',
                    ];
                }
            }

            // Armazenar no cache por 1 hora
            Cache::put('tip_brasil_data', $tipBrasilData, 3600);

            return response()->json([
                'message' => 'Dados da TIP Brasil importados com sucesso',
                'count' => count($tipBrasilData)
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function fetchViaCep($cep)
    {
        try {
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            
            if (!$response->successful()) {
                return response()->json(['error' => 'CEP não encontrado'], 404);
            }

            $data = $response->json();
            
            if (isset($data['erro'])) {
                return response()->json(['error' => 'CEP não encontrado'], 404);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getLigueAiData()
    {
        return Cache::get('ligue_ai_data', []);
    }

    public static function getTipBrasilData()
    {
        return Cache::get('tip_brasil_data', []);
    }
}
