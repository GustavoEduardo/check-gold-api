<?php

namespace App\Http\Controllers;


use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $repository;

    public function __construct(ClienteRepository $model)
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
