<?php

namespace App\Http\Controllers;

use App\Repositories\FiltrosRepository;
use Illuminate\Http\Request;

class FiltrosController extends Controller
{
    protected $repository;

    public function __construct(FiltrosRepository $model)
    {
        $this->repository = $model;
    }

    public function marcas(Request $request)
    {
        return $this->repository->marcas($request);
    }

    public function categorias(Request $request)
    {
        return $this->repository->categorias($request);
    }

    public function gruposClientes(Request $request)
    {
        return $this->repository->gruposClientes($request);
    }

    public function pagamentos(Request $request)
    {
        return $this->repository->pagamentos($request);
    }
}
