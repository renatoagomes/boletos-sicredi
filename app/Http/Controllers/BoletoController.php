<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositorios\RepositorioBoletos as RepositorioBoletos;

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
                'instrucoesCobranca' => [
                    'Linha 1 - Instruções de cobrança',
                    'Linha 2- Instruções de cobrança',
                    'Linha 3 - Instruções de cobrança'
                ],
                'descDemonstrativo' => [
                    'Linha 1 - Descrição demonstrativo',
                    'Linha 2- Descrição demonstrativo',
                    'Linha 3 - Descrição demonstrativo'
                ],
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
                'instrucoesCobranca' => [
                    'Linha 1 - Instruções de cobrança',
                    'Linha 2- Instruções de cobrança',
                    'Linha 3 - Instruções de cobrança'
                ],
                'descDemonstrativo' => [
                    'Linha 1 - Descrição demonstrativo',
                    'Linha 2- Descrição demonstrativo',
                    'Linha 3 - Descrição demonstrativo'
                ],
                'dataVencimento' => (new \Carbon\Carbon())->addDays(5),
                'valorBoleto' => rand(1, 10),
                'sequencialNossoNumero' => rand(1, 99999)
            ]
        );

        $boleto = $this->repositorioBoletos->gerarBoleto();
        return $boleto->renderHTML();
    }

    /**
     * Rota para teste da geracao da remessa
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

        $this->repositorioBoletos->setDadosBoleto(
            [
                'instrucoesCobranca' => [
                    'Linha 1 - Instruções de cobrança',
                    'Linha 2- Instruções de cobrança',
                    'Linha 3 - Instruções de cobrança'
                ],
                'descDemonstrativo' => [
                    'Linha 1 - Descrição demonstrativo',
                    'Linha 2- Descrição demonstrativo',
                    'Linha 3 - Descrição demonstrativo'
                ],
                'dataVencimento' => (new \Carbon\Carbon())->addDays(5),
                'valorBoleto' => rand(1, 10),
                'sequencialNossoNumero' => rand(1, 99999)
            ]
        );

        $boletos[] = $this->repositorioBoletos->gerarBoleto();

        $this->repositorioBoletos->setDadosBoleto(
            [
                'instrucoesCobranca' => [
                    'Linha 1 - Instruções de cobrança',
                    'Linha 2- Instruções de cobrança',
                    'Linha 3 - Instruções de cobrança'
                ],
                'descDemonstrativo' => [
                    'Linha 1 - Descrição demonstrativo',
                    'Linha 2- Descrição demonstrativo',
                    'Linha 3 - Descrição demonstrativo'
                ],
                'dataVencimento' => (new \Carbon\Carbon())->addDays(5),
                'valorBoleto' => rand(1, 10),
                'sequencialNossoNumero' => rand(1, 99999)
            ]
        );

        $boletos[] = $this->repositorioBoletos->gerarBoleto();
        $remessa = $this->repositorioBoletos->gerarRemessa($boletos);
        return $this->repositorioBoletos->downloadRemessa($remessa);
    }

}
