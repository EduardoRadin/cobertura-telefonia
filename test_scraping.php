<?php

require 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;

echo "Testando webscraping do site Ligue Aí...\n";

try {
    $response = Http::get('https://suporte-ligueai.tomticket.com/kb/telefonia-itx-e-portabilidade/cobertura-ligue-ai');
    
    if (!$response->successful()) {
        echo "Erro: Falha ao acessar o site (HTTP " . $response->status() . ")\n";
        exit(1);
    }

    echo "Site acessado com sucesso!\n";
    echo "Tamanho da resposta: " . strlen($response->body()) . " bytes\n";

    $crawler = new Crawler($response->body());
    
    // Procurar por tabelas
    $tables = $crawler->filter('table');
    echo "Tabelas encontradas: " . $tables->count() . "\n";
    
    if ($tables->count() > 0) {
        $rows = $tables->first()->filter('tr');
        echo "Linhas na primeira tabela: " . $rows->count() . "\n";
        
        // Mostrar as primeiras 3 linhas
        $rows->each(function (Crawler $row, $i) {
            if ($i < 3) {
                $cells = $row->filter('td');
                echo "Linha $i: " . $cells->count() . " células\n";
                if ($cells->count() >= 3) {
                    echo "  UF: " . trim($cells->eq(0)->text()) . "\n";
                    echo "  CN: " . trim($cells->eq(1)->text()) . "\n";
                    echo "  Município: " . trim($cells->eq(2)->text()) . "\n";
                }
            }
        });
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
