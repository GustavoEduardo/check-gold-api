<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Repositories\UsuarioLogadoRepository;
use Illuminate\Http\Request;

class UsuarioLogadoController extends Controller
{
    protected $repository;

    public function __construct(UsuarioLogadoRepository $model)
    {
        $this->repository = $model;
    }

    public function index(Request $request)
    {
        return $this->repository->index();
    }

//    public function detalhe(Request $request)
//    {
//        return $this->repository->detalhe();
//    }
//
//    public function atualiza(Request $request)
//    {
//        return $this->repository->atualiza($request);
//    }

}
