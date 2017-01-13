<?php

namespace App\Repositorios;

use Eduardokum\LaravelBoleto\Pessoa as Pessoa;
use Eduardokum\LaravelBoleto\Boleto\Banco\Sicredi as BoletoSicredi;
use Eduardokum\LaravelBoleto\Boleto\Render\Pdf as Pdf;
use Eduardokum\LaravelBoleto\Cnab\Remessa\Cnab400\Banco\Sicredi as RemessaSicredi;

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
    /** @var array Array com as strings que serao inseridas nas instrucoes  do boleto */
    protected $instrucoesCobranca;

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

        //Inicializando propriedades para garantir que existirao no momento da geracao do boleto
        $this->descDemonstrativo = [];
        $this->instrucoesCobranca = [];
        $this->dataVencimento = new \Carbon\Carbon();
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
      * Metodo para settar o path da logo a ser inserida no boleto
      * @param string Path para o arquivo a partir do public_path()
      */
     public function setLogoEmpresa( $pathLogo = null )
     {
         $this->logoEmpresa = $pathLogo ? $pathLogo : 'logo.png';
     }

    /**
     * Metodo para settar os dados do boleto.
     * @param string[] $arrayDadosBoleto Array contendo os dados do boleto
     * ['valorBoleto', 'dataVencimento', 'sequencialNossoNumero']
     */
    public function setDadosBoleto( $arrayDadosBoleto )
    {
        $this->valorBoleto = array_key_exists('valorBoleto', $arrayDadosBoleto) ? $arrayDadosBoleto['valorBoleto'] : null;
        $this->dataVencimento = array_key_exists('dataVencimento', $arrayDadosBoleto) ? $arrayDadosBoleto['dataVencimento'] : null;
        $this->sequencialNossoNumero = array_key_exists('sequencialNossoNumero', $arrayDadosBoleto) ? $arrayDadosBoleto['sequencialNossoNumero'] : null;
        $this->instrucoesCobranca = array_key_exists('instrucoesCobranca', $arrayDadosBoleto) ? $arrayDadosBoleto['instrucoesCobranca'] : null;
        $this->descDemonstrativo = array_key_exists('descDemonstrativo', $arrayDadosBoleto) ? $arrayDadosBoleto['descDemonstrativo'] : null;
    }

    /**
     * Metodo para criar um boleto
     *
     * @return BoletoSicredi Uma instancia de Boleto, que pode ser renderizada ou utilizada para gerar remessa
     */
    public function gerarBoleto()
    {
        return new BoletoSicredi([
            'logo'                   => $this->logoEmpresa,
            'dataVencimento'         => $this->dataVencimento,
            'valor'                  => $this->valorBoleto,
            'multa'                  => false,
            'juros'                  => false,
            'numero'                 => 1,
            'numeroDocumento'        => $this->sequencialNossoNumero,
            'pagador'                => $this->pagador,
            'beneficiario'           => $this->beneficiario,
            'carteira'               => '1',
            'byte'                   => 2,
            'agencia'                => $this->beneficiarioAgencia,
            'posto'                  => $this->beneficiarioPosto,
            'conta'                  => $this->beneficiarioContaCorrente,
            'descricaoDemonstrativo' => $this->descDemonstrativo,
            'instrucoes'             => $this->instrucoesCobranca,
            'aceite'                 => 'S',
            'especieDoc'             => $this->beneficiarioEspeciedoc,
        ]);

    }

    /**
     * Metodo para fazer download do PDF do Boleto
     * @param BoletoSicredi $boleto Instancia de BoletoSicredi que sera gerado um pdf para download
     * @return Download do PDF
     */
    public function downloadPDF(BoletoSicredi $boleto)
    {
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
     * Metodo para testar a geracao da Remessa
     *
     * @param $boletos array Array com as instancias de BoletoSicredi que serao inclusos na Remessa.
     * @return RemessaSicredi Uma instancia de RemessaSicredi.
     */
    public function gerarRemessa(array $boletos)
    {
        $remessa = new RemessaSicredi(
            [
                'agencia'      => $this->beneficiarioAgencia,
                'carteira'     => '1',
                'conta'        => $this->beneficiarioContaCorrente,
                'idremessa'    => 1,
                'beneficiario' => $this->beneficiario
            ]
        );

        foreach ($boletos as $boleto) {
            $remessa->addBoleto($boleto);
        }

        return $remessa;
    }

    /**
     * Metodo para fazer download da remessa
     * @param RemessaSicredi $remessa Instancia de RemessaSicredi que sera feita download
     * @return Download da remessa em .txt
     */
    public function downloadRemessa(RemessaSicredi $remessa)
    {
        //Salvando na pasta arquivos para retornar
        $remessa->save('arquivos' . DIRECTORY_SEPARATOR . 'sicredi.txt');
        dd('inside downloadRemessa', $remessa);
        return response()->download('arquivos/sicredi.txt', 'remessa-sicredi.txt', ['application/txt']);
    }
}
