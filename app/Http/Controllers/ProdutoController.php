<?php

namespace App\Http\Controllers;


use App\Repositories\ProdutoRepository;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    protected $repository;

    public function __construct(ProdutoRepository $model)
    {
        $this->repository = $model;
    }

    public function table(Request $request)
    {
        return $this->repository->table($request);
    }

    public function gerarSku(Request $request)
    {
        return $this->repository->gerarSku($request);
    }

    public function detalhe($id)
    {
        return $this->repository->detalhe($id);
    }
    public function gravar(Request $request, $id = null)
    {
        return $this->repository->gravar($request, $id);
    }
    public function remover($id = null)
    {
        return $this->repository->remover($id);
    }

}
