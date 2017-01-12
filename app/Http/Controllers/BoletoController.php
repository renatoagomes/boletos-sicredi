<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositorios\RepositorioBoletos as RepositorioBoletos;

class BoletoController extends Controller
{

    /**
     * @var mixed $repositorioBoletos Instancia do repositorio com a logica operacional
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
     * Rota para a geracao do pdf
     */
    public function testeBoletoPDF()
    {
        return $this->repositorioBoletos->gerarBoleto();
    }

}
