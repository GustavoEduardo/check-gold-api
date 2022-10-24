<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Models\GrupoCliente;
use App\Models\Marca;
use App\Models\Pedido;
use App\Models\Usuario;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class FiltrosRepository extends AbstractRepository
{

    protected $model = Usuario::class;

    public function __construct()
    {

    }

    public function marcas($request)
    {
        try {

            $marcas = Marca::query()
                ->select("id as key", "nome as label")
                ->orderBy("nome", "asc")
                ->get();

            return response()->json([
                'menssage' => "",
                'data' => $marcas,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }

    public function categorias($request)
    {
        try {

            $categorias = Categoria::query()
                ->select("id as key", "nome as label")
                ->orderBy("nome", "asc")
                ->get();

            return response()->json([
                'menssage' => "",
                'data' => $categorias,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }

    public function gruposClientes($request)
    {
        try {

            $grupos = GrupoCliente::query()
                ->select("id as key", "nome as label")
                ->orderBy("nome", "asc")
                ->get();

            return response()->json([
                'menssage' => "",
                'data' => $grupos,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro!",
                'data' => null,
                'errors' => $e->getMessage()
            ], 422);
        }

    }

    public function pagamentos($request)
    {
        try {

            $pagamentos = Pedido::query()
                ->select("tipoPagamento as key", "tipoPagamento as label")
                ->groupBy("tipoPagamento")
                ->orderBy("tipoPagamento", "asc")
                ->get();

            return response()->json([
                'menssage' => "",
                'data' => $pagamentos,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro!",
                'data' => null,
                'errors' => $e->getMessage()
            ], 422);
        }

    }


}
