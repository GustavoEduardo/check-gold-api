<?php

namespace App\Http\Controllers;

use App\Repositories\CategoriaRepository;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    protected $repository;

    public function __construct(CategoriaRepository $model)
    {
        $this->repository = $model;
    }

    public function table(Request $request)
    {
        return $this->repository->table($request);
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
