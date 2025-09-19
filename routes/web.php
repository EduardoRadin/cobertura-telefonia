<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoverageController;
use App\Http\Controllers\ScrapingController;

Route::get('/', [CoverageController::class, 'index'])->name('home');

// Rotas de busca
Route::get('/search', [CoverageController::class, 'search'])->name('search');
Route::get('/ligue-ai-data', [CoverageController::class, 'getLigueAiData'])->name('ligue-ai-data');
Route::get('/tip-brasil-data', [CoverageController::class, 'getTipBrasilData'])->name('tip-brasil-data');

// Rotas de scraping
Route::post('/scrape/ligue-ai', [ScrapingController::class, 'scrapeLigueAi'])->name('scrape.ligue-ai');
Route::post('/scrape/tip-brasil', [ScrapingController::class, 'fetchTipBrasil'])->name('scrape.tip-brasil');
Route::get('/viacep/{cep}', [ScrapingController::class, 'fetchViaCep'])->name('viacep');
