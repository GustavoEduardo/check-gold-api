<?php

namespace App\Repositories;

use App\Helpers\MyHelpers;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\GrupoCliente;
use App\Models\Marca;

use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GrupoClienteRepository extends AbstractRepository
{


    public function __construct()
    {

    }

    public function table(Request $request)
    {
        try {

            $pagina = $request->get("pagina", 1);
            $quantidade = $request->get("quantidade", 50);
            $ordem = $request->get("ordem", "nome");
            $sentido = $request->get("sentido", "asc");



            $query = GrupoCliente::query();
            $query->orderBy($ordem, $sentido);


            $grupos = $query->paginate($quantidade, "*", '', $pagina);

            return response()->json([
                'menssage' => "",
                'retorno' => [$grupos],
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


    public function detalhe($id)
    {
        try {


            $cliente = Cliente::query()
                ->find($id);

            if (!$cliente) {
                throw new \Exception("Cliente não encontrado");
            }

            return response()->json([
                'menssage' => "",
                'retorno' => [$cliente],
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


    public function gravar($request, $id = null)
    {
        try {

            $dadosToken = new DecriptJwt;
            $usuarioId = $dadosToken->decriptJwt();


            $dados = $request->all();

            if (!empty($id)) {
                $grupoCliente = GrupoCliente::query()->find($id);

                if (!$grupoCliente) {
                    throw new \Exception("Grupo não encontrado.", 422);
                }
            } else {
                $grupoCliente = new GrupoCliente();
                $grupoCliente->created_from = $usuarioId;
            }

            $grupoCliente->updated_from = $usuarioId;

            if (key_exists('nome', $dados)) {
                $grupoCliente->nome = $dados['nome'];
            }


            if (!$grupoCliente->save()) {
                return response()->json([
                    'menssage' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $grupoCliente->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados gravados com sucesso!",
                'data' => ['id' => $grupoCliente->id],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro ao gravar dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }


    public function remover($id = null)
    {
        try {


            $grupoCliente = GrupoCliente::query()->find($id);

            if (!$grupoCliente) {
                throw new \Exception("Grupo não encontrado.", 422);
            }


            if (!$grupoCliente->delete()) {
                return response()->json([
                    'menssage' => "Erro ao remover dados!",
                    'data' => null,
                    'errors' => $grupoCliente->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados removidos com sucesso!",
                'data' => [],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro ao remover dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }


}
