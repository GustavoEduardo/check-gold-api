<?php

namespace App\Http\Controllers;

use App\Repositories\PedidoRepository;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected $repository;

    public function __construct(PedidoRepository $model)
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
    public function atualziarStatus($id)
    {
        return $this->repository->atualziarStatus($id);
    }

}
