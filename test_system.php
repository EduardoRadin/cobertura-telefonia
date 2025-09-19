<?php

// Script para testar o sistema de cobertura telefônica

echo "=== Testando Sistema de Cobertura Telefônica ===\n\n";

// Testar importação do Ligue Aí
echo "1. Testando importação do Ligue Aí...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/scrape/ligue-ai');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ Sucesso: {$data['message']}\n";
    echo "  Registros importados: {$data['count']}\n\n";
} else {
    echo "✗ Erro na importação do Ligue Aí (HTTP $httpCode)\n\n";
}

// Testar importação da TIP Brasil
echo "2. Testando importação da TIP Brasil...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/scrape/tip-brasil');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ Sucesso: {$data['message']}\n";
    echo "  Registros importados: {$data['count']}\n\n";
} else {
    echo "✗ Erro na importação da TIP Brasil (HTTP $httpCode)\n\n";
}

// Testar busca por cidade
echo "3. Testando busca por cidade (São Paulo)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/search?query=São Paulo&type=all');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ Sucesso na busca\n";
    echo "  Resultados Ligue Aí: " . count($data['ligue_ai']) . "\n";
    echo "  Resultados TIP Brasil: " . count($data['tip_brasil']) . "\n\n";
} else {
    echo "✗ Erro na busca (HTTP $httpCode)\n\n";
}

// Testar busca por CEP
echo "4. Testando busca por CEP (01001-000)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/viacep/01001-000');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ Sucesso na consulta do CEP\n";
    echo "  CEP: {$data['cep']}\n";
    echo "  Cidade: {$data['localidade']}\n";
    echo "  Estado: {$data['uf']}\n\n";
} else {
    echo "✗ Erro na consulta do CEP (HTTP $httpCode)\n\n";
}

echo "=== Teste concluído ===\n";
echo "Acesse http://localhost:8000 para usar a interface web\n";
