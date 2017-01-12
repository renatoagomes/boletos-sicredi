<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;

Route::get('/', function () {
    return view('welcome');
});

/** View para facilitar o teste das operacoes **/
Route::get('boleto', function () {
    return view('boleto');
});

/** Rotas para teste **/
Route::get('boleto/pdf', 'BoletoController@testeBoletoPDF');
Route::get('boleto/html', 'BoletoController@testeBoletoHTML');
Route::get('boleto/remessa', 'BoletoController@testeBoletoRemessa');
