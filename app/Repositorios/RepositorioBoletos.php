<?php

namespace App\Repositorios;

use Eduardokum\LaravelBoleto\Pessoa as Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi as BoletoSicredi;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf as Pdf;

/**
 * Classe para intermediar a geração de boletos.
 */
class RepositorioBoletos
{
    /** @var Pessoa Instancia de Pessoa */
    protected $beneficiario;
    /** @var Pessoa Instancia de Pessoa */
    protected $pagador;

    /** @var string Path para o logo que sera inserido no boleto */
    protected $logoEmpresa;
    /** @var array Array com as strings que serao inseridas no demonstrativo do boleto */
    protected $descDemonstrativo;
    /** @var array Array com as strings que serao inseridas no rodape do boleto */
    protected $instrucoesRodape;

    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioNome;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioEndereco;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioCep;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioUf;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioCidade;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioDocumento;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioAgencia;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioContaCorrente;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioPosto;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioCarteira;
    /** @var string Dados do beneficiario obtidos do .env*/
    protected $beneficiarioEspeciedoc;

    /**
     * Construtor para executar quaisquer operacoes necessarias
     */
    function __construct()
    {
        $this->setup();
    }

    /**
     * Metodo responsavel por obter as informacoes sensiveis do .env
     * e atribuilas as propriedades do repositorio
     */
    private function setup()
    {
        /** Dados extras do Beneficiario **/
        $this->beneficiarioNome = env('BENEFICIARIO_NOME');
        $this->beneficiarioEndereco = env('BENEFICIARIO_ENDERECO');
        $this->beneficiarioCep = env('BENEFICIARIO_CEP');
        $this->beneficiarioUf = env('BENEFICIARIO_UF');
        $this->beneficiarioCidade = env('BENEFICIARIO_CIDADE');
        $this->beneficiarioDocumento = env('BENEFICIARIO_DOCUMENTO');

        /** Dados bancarios **/
        $this->beneficiarioAgencia = env('BENEFICIARIO_AGENCIA');
        $this->beneficiarioContaCorrente = env('BENEFICIARIO_CONTACORRENTE');
        $this->beneficiarioPosto = env('BENEFICIARIO_POSTO');
        $this->beneficiarioCarteira = env('BENEFICIARIO_CARTEIRA');
        $this->beneficiarioEspeciedoc = env('BENEFICIARIO_ESPECIEDOC');

        $this->beneficiario = new Pessoa(
            [
                'nome'      => $this->beneficiarioNome,
                'endereco'  => $this->beneficiarioEndereco,
                'cep'       => $this->beneficiarioCep,
                'uf'        => $this->beneficiarioUf,
                'cidade'    => $this->beneficiarioCidade,
                'documento' => $this->beneficiarioDocumento,
            ]
        );

        $this->descDemonstrativo = ['linha 1 da demonstracao', 'linha 2 ', 'linha 3'];
        $this->instrucoesRodape = ['rodape1', 'rodape2', 'rodape3'];


    }

    /**
     * Metodo para settar um pagador que sera referenciado na proxima geracao de Boleto
     *
     * @param string[] $arrayPagador Array contendo os dados do pagador
     */
    public function setPagador( $arrayPagador )
    {
        $pagador = new Pessoa(
            [
                'nome'      => array_key_exists('nome', $arrayPagador) ? $arrayPagador['nome'] : '',
                'endereco'  => array_key_exists('endereco', $arrayPagador) ? $arrayPagador['endereco'] : '',
                'bairro'    => array_key_exists('bairro', $arrayPagador) ? $arrayPagador['bairro'] : '',
                'cep'       => array_key_exists('cep', $arrayPagador) ? $arrayPagador['cep'] : '',
                'uf'        => array_key_exists('uf', $arrayPagador) ? $arrayPagador['uf'] : '',
                'cidade'    => array_key_exists('cidade', $arrayPagador) ? $arrayPagador['cidade'] : '',
                'documento' => array_key_exists('documento', $arrayPagador) ? $arrayPagador['documento'] : '',
            ]
        );

        $this->pagador = $pagador;
    }

    /**
     * Metodo para settar os dados do boleto.
     *
     * @param string[] $arrayDadosBoleto Array contendo os dados do boleto
     */
    public function setDadosBoleto( $arrayDadosBoleto )
    {
        $pagador = new Pessoa(
            [
                'nome'      => array_key_exists('nome', $arrayPagador) ? $arrayPagador['nome'] : '',
                'endereco'  => array_key_exists('endereco', $arrayPagador) ? $arrayPagador['endereco'] : '',
                'bairro'    => array_key_exists('bairro', $arrayPagador) ? $arrayPagador['bairro'] : '',
                'cep'       => array_key_exists('cep', $arrayPagador) ? $arrayPagador['cep'] : '',
                'uf'        => array_key_exists('uf', $arrayPagador) ? $arrayPagador['uf'] : '',
                'cidade'    => array_key_exists('cidade', $arrayPagador) ? $arrayPagador['cidade'] : '',
                'documento' => array_key_exists('documento', $arrayPagador) ? $arrayPagador['documento'] : '',
            ]
        );

        $this->pagador = $pagador;
    }


    /**
     * Metodo para gerar um boleto
     * @return Eduardokum\LaravelBoleto\Boleto
     */
    public function testeBoletoPDF()
    {

        $boleto = new BoletoSicredi([
                'logo'                   => 'logo.png',
                'dataVencimento'         => new \Carbon\Carbon(),
                'valor'                  => 100,
                'multa'                  => false,
                'juros'                  => false,
                'numero'                 => 1,
                'numeroDocumento'        => 555,
                'pagador'                => $this->pagador,
                'beneficiario'           => $this->beneficiario,
                'carteira'               => '1',
                'byte'                   => 2,
                'agencia'                => $this->beneficiarioAgencia,
                'posto'                  => $this->beneficiarioPosto,
                'conta'                  => $this->beneficiarioContaCorrente,
                'descricaoDemonstrativo' => $this->descDemonstrativo,
                'instrucoes'             => $this->instrucoesRodape,
                'aceite'                 => 'S',
                'especieDoc'             => $this->beneficiarioEspeciedoc,
            ]);

        $pdf = new Pdf();
        $pdf->addBoleto($boleto);
        $pdf->gerarBoleto($pdf::OUTPUT_SAVE, 'arquivos' . DIRECTORY_SEPARATOR . 'sicredi.pdf');    // ou

        $headers = array(
            'Content-Type: application/pdf',
        );

        $file = public_path() . '/arquivos/sicredi.pdf';

        return response()->download($file, 'boleto-sicredi.pdf', $headers);

    }

    /**
     * Metodo para testar a geracao do HTML do boleto
     */
    public function testeBoletoHTML()
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

        return $boleto->renderHtml();
    }

    /**
     * Metodo para testar a geracao da Remessa
     */
    public function testeBoletoRemessa()
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

    }
}
