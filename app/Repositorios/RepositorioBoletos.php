<?php

namespace App\Repositorios;

use Eduardokum\LaravelBoleto\Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf;

/**
 * Classe para intermediar a geração de boletos.
 */
class RepositorioBoletos
{
    protected $beneficiario;
    protected $pagador;
    protected $logo;
    protected $descricaoDemonstrativo;
    protected $instrucoesRodape;
    protected $dadosBoleto;


    /**
     * Metodo para gerar um boleto
     * @return Eduardokum\LaravelBoleto\Boleto
     */
    public function gerarBoleto()
    {

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

        $pdf = new Pdf();
        $pdf->addBoleto($boleto);
        $pdf->gerarBoleto($pdf::OUTPUT_SAVE, 'arquivos' . DIRECTORY_SEPARATOR . 'sicredi.pdf');    // ou

        $headers = array(
            'Content-Type: application/pdf',
        );

        $file = public_path() . '/arquivos/sicredi.pdf';

        return response()->download($file, 'boleto-sicredi.pdf', $headers);

    }
}