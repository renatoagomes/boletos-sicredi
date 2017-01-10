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

Route::get('/', function () {
    return view('welcome');
});

use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicred;

Route::get('boleto', function () {

    $beneficiario = new Pessoa([
        'nome' => 'ACME',
        'endereco' => 'Rua um, 123',
        'cep' => '99999-999',
        'uf' => 'UF',
        'cidade' => 'CIDADE',
        'documento' => '99.999.999/9999-99',
    ]);

    $pagador = new Pessoa([
        'nome' => 'Cliente',
        'endereco' => 'Rua um, 123',
        'bairro' => 'Bairro',
        'cep' => '99999-999',
        'uf' => 'UF',
        'cidade' => 'CIDADE',
        'documento' => '999.999.999-99',
    ]);

    $boletoArray = [
        'logo' => 'logo.jpg', // Logo da empresa
        'dataVencimento' => new \Carbon\Carbon('1790-01-01'),
        'valor' => 100.00,
        'multa' => 10.00, // porcento
        'juros' => 2.00, // porcento ao mes
        'juros_apos' =>  1, // juros e multa após
        'diasProtesto' => false, // protestar após, se for necessário
        'numero' => 1,
        'numeroDocumento' => 1,
        'pagador' => $pagador, // Objeto PessoaContract
        'beneficiario' => $beneficiario, // Objeto PessoaContract
        'agencia' => 9999, // BB, Bradesco, CEF, HSBC, Itáu
        'agenciaDv' => 9, // se possuir
        'conta' => 99999, // BB, Bradesco, CEF, HSBC, Itáu, Santander
        'contaDv' => 9, // Bradesco, HSBC, Itáu
        'carteira' => 11, // BB, Bradesco, CEF, HSBC, Itáu, Santander
        'convenio' => 9999999, // BB
        'variacaoCarteira' => 99, // BB
        'range' => 99999, // HSBC
        'codigoCliente' => 99999, // Bradesco, CEF, Santander
        'ios' => 0, // Santander
        'descricaoDemonstrativo' => ['msg1', 'msg2', 'msg3'], // máximo de 5
        'instrucoes' =>  ['inst1', 'inst2'], // máximo de 5
        'aceite' => 1,
        'especieDoc' => 'DM',
    ];

    $boleto = new Sicred($boletoArray);

    //return $boleto->renderPDF();
    // ou
    return $boleto->renderHTML();

});

