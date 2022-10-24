<?php

namespace App\Http\Controllers;


use App\Repositories\GrupoClienteRepository;
use Illuminate\Http\Request;

class GrupoClienteController extends Controller
{
    protected $repository;

    public function __construct(GrupoClienteRepository $model)
    {
        $this->repository = $model;
    }

    public function table(Request $request)
    {
        return $this->repository->table($request);
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
