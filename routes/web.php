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

Route::get('boleto', function () {
    return view('boleto');
});



Route::get('boleto/pdf', function () {

    $beneficiario = new Pessoa(
        [
            'nome'      => 'ACME',
            'endereco'  => 'Rua um, 123',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '99.999.999/9999-99',
        ]
    );

    $pagador = new Pessoa(
        [
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]
    );

    $boleto = new Sicredi(
        [
            'logo'                   => 'logo.png',
            'dataVencimento'         => new \Carbon\Carbon(),
            'valor'                  => 100,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => 1,
            'numeroDocumento'        => 1,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => '1',
            'byte'                   => 2,
            'agencia'                => 1111,
            'posto'                  => 11,
            'conta'                  => 11111,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite'                 => 'S',
            'especieDoc'             => 'DM',
        ]
    );

    $pdf = new Eduardokum\LaravelBoleto\Boleto\Render\Pdf();
    $pdf->addBoleto($boleto);
    $pdf->gerarBoleto($pdf::OUTPUT_SAVE, 'arquivos' . DIRECTORY_SEPARATOR . 'sicredi.pdf');    // ou

    $headers = array(
              'Content-Type: application/pdf',
            );

    $file = public_path() . '/arquivos/sicredi.pdf';

    return response()->download($file, 'boleto-sicredi.pdf', $headers);

});

Route::get('boleto/html', function () {

    $beneficiario = new Pessoa(
        [
            'nome'      => 'ACME',
            'endereco'  => 'Rua um, 123',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '99.999.999/9999-99',
        ]
    );

    $pagador = new Pessoa(
        [
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]
    );

    $boleto = new Sicredi(
        [
            'logo'                   => 'logo.png',
            'dataVencimento'         => new \Carbon\Carbon(),
            'valor'                  => 100,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => 1,
            'numeroDocumento'        => 1,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => '1',
            'byte'                   => 2,
            'agencia'                => 1111,
            'posto'                  => 11,
            'conta'                  => 11111,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite'                 => 'S',
            'especieDoc'             => 'DM',
        ]
    );

    return $boleto->renderHtml();

});

Route::get('boleto/remessa', function () {

    $beneficiario = new Pessoa(
        [
            'nome'      => 'ACME',
            'endereco'  => 'Rua um, 123',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '99.999.999/9999-99',
        ]
    );

    $pagador = new Pessoa(
        [
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]
    );

    $boleto = new Sicredi(
        [
            'logo'                   => 'logo.png',
            'dataVencimento'         => new \Carbon\Carbon(),
            'valor'                  => 100,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => 1,
            'numeroDocumento'        => 1,
            'pagador'                => $pagador,
            'beneficiario'           => $beneficiario,
            'carteira'               => '1',
            'byte'                   => 2,
            'agencia'                => 1111,
            'posto'                  => 11,
            'conta'                  => 11111,
            'descricaoDemonstrativo' => ['demonstrativo 1', 'demonstrativo 2', 'demonstrativo 3'],
            'instrucoes'             => ['instrucao 1', 'instrucao 2', 'instrucao 3'],
            'aceite'                 => 'S',
            'especieDoc'             => 'DM',
        ]
    );

    // Criando arquivo remessa
    $remessa = new \Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Sicredi(
        [
            'agencia'      => 2606,
            'carteira'     => '1',
            'conta'        => 12510,
            'idremessa'    => 1,
            'beneficiario' => $beneficiario,
        ]
    );

    $remessa->addBoleto($boleto);
    $remessa->save('arquivos' . DIRECTORY_SEPARATOR . 'sicredi.txt');

    //Retornando arquivo .txt da remessa para download
    return response()->download('arquivos/sicredi.txt', 'remessa-sicredi.txt', ['application/txt']);

});
