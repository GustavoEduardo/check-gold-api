<?php

namespace App\Http\Controllers;

use App\Repositories\UsuarioRepository;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    protected $repository;

    public function __construct(UsuarioRepository $model)
    {
        $this->repository = $model;
    }

    public function gravar(Request $request)
    {
        return $this->repository->gravar($request);
    }

    public function editar(Request $request, $id = null)
    {
        return $this->repository->editar($request,$id);
    }

    public function table(Request $request)
    {
        return $this->repository->table($request);
    }

    public function remover($id = null)
    {
        return $this->repository->remover($id);
    }

}
