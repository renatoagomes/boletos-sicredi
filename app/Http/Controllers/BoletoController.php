<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositorios\RepositorioBoletos as RepositorioBoletos;
use ZipArchive;

class BoletoController extends Controller
{

    /**
     * @var RepositorioBoletos $repositorioBoletos Instancia do repositorio com a logica operacional
     */
    protected $repositorioBoletos;

    /**
     * Construtor recebendo instancia do Repositorio de Boletos
     */
    function __construct(RepositorioBoletos $repositorio)
    {
        $this->repositorioBoletos = $repositorio;
    }

    /**
     * Rota para teste da geracao do pdf
     *
     * @return Download O arquivo do boleto.pdf
     */
    public function testeBoletoPDF()
    {
        $this->repositorioBoletos->setLogoEmpresa('logo.png');

        $this->repositorioBoletos->setPagador([
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        $this->repositorioBoletos->setDadosBoleto(
            [
                'instrucoesCobranca' => [ 'Linha 1 - Instruções', 'Linha 2 - Instruções', 'Linha 3 - Instruções' ],
                'descDemonstrativo' => [ 'Linha 1 - Descrição', 'Linha 2 - Descrição', 'Linha 3 - Descrição' ],
                'dataVencimento' => (new \Carbon\Carbon())->addDays(5),
                'valorBoleto' => rand(1, 10),
                'sequencialNossoNumero' => rand(1, 99999)
            ]
        );

        $boleto = $this->repositorioBoletos->gerarBoleto();
        return $this->repositorioBoletos->downloadPDF($boleto);
}

    /**
     * Rota para teste da geracao do html
     *
     * @return HTML View com boleto gerado
     */
    public function testeBoletoHTML()
    {
        $this->repositorioBoletos->setLogoEmpresa('logo.png');

        $this->repositorioBoletos->setPagador([
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        $this->repositorioBoletos->setDadosBoleto(
            [
                'instrucoesCobranca' => [ 'Linha 1 - Instruções', 'Linha 2 - Instruções', 'Linha 3 - Instruções' ],
                'descDemonstrativo' => [ 'Linha 1 - Descrição', 'Linha 2 - Descrição', 'Linha 3 - Descrição' ],
                'dataVencimento' => (new \Carbon\Carbon())->addDays(5),
                'valorBoleto' => rand(1, 10),
                'sequencialNossoNumero' => rand(1, 99999)
            ]
        );

        $boleto = $this->repositorioBoletos->gerarBoleto();
        return $boleto->renderHTML();
    }

    /**
     * Rota para teste da geracao da remessa gerando remessa de 1 boleto
     *
     * @return Download remessa.txt para homologacao de 1 boleto
     */
    public function testeBoletoRemessa()
    {
        $boletos = [];
        $this->repositorioBoletos->setPagador([
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        $this->repositorioBoletos->setDadosBoleto([
            'instrucoesCobranca' => [ 'Linha 1 - Instruções', 'Linha 2 - Instruções', 'Linha 3 - Instruções' ],
            'descDemonstrativo' => [ 'Linha 1 - Descrição', 'Linha 2 - Descrição', 'Linha 3 - Descrição' ],
            'dataVencimento' => (new \Carbon\Carbon())->addDays(5),
            'valorBoleto' => rand(1, 10),
            'sequencialNossoNumero' => rand(1, 99999)
        ]);

        $boletos[] = $this->repositorioBoletos->gerarBoleto();
        $remessa = $this->repositorioBoletos->gerarRemessa($boletos);
        return $this->repositorioBoletos->downloadRemessa($remessa);
    }

    /**
     * Rota para gerar 10 boletos e o arquivo remessa deles para homologacao
     *
     * Devolve download do 'arquivos.zip' contendo os 10 titulos e arquivo remessa para homologacao.
     *
     * @return Download arquivos.zip
     */
    public function gerarArquivosHomologacao()
    {
        $this->repositorioBoletos->setLogoEmpresa('logo.png');
        $this->repositorioBoletos->setPagador([
            'nome'      => 'Cliente',
            'endereco'  => 'Rua um, 123',
            'bairro'    => 'Bairro',
            'cep'       => '99999-999',
            'uf'        => 'UF',
            'cidade'    => 'CIDADE',
            'documento' => '999.999.999-99',
        ]);

        //Gerando os 10 boletos
        for ($sequencial = 1; $sequencial < 11; $sequencial++) {
            $this->repositorioBoletos->setDadosBoleto(
                [
                    'instrucoesCobranca' => [ 'Linha 1 - Instruções', 'Linha 2 - Instruções', 'Linha 3 - Instruções' ],
                    'descDemonstrativo' => [ 'Linha 1 - Descrição', 'Linha 2 - Descrição', 'Linha 3 - Descrição' ],
                    'dataVencimento' => (new \Carbon\Carbon())->addDays(rand(1,5)),
                    'valorBoleto' => rand(1, 5),
                    'sequencialNossoNumero' => $sequencial
                ]
            );
            $boleto = $this->repositorioBoletos->gerarBoleto();
            $boletos[] = $boleto;
        }

        $arquivos = $this->repositorioBoletos->gerarPDFs($boletos);
        $remessa = $this->repositorioBoletos->gerarRemessa($boletos);
        $remessa->save('arquivos' . DIRECTORY_SEPARATOR . 'remessa-homologacao.txt');
        $arquivos[] = 'remessa-homologacao.txt';

        //Criando arquivos.zip com os 10 titulos e o arquivo remessa
        $zipname = 'arquivos.zip';
        $zip = new ZipArchive;
        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($arquivos as $nomeArquivo) {
          $zip->addFile('arquivos/'.$nomeArquivo);
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
    }

}
