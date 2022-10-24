<?php

namespace App\Repositories;

use App\Models\Marca;
use App\Models\Usuario;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class MarcaRepository extends AbstractRepository
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


            switch ($ordem) {
                default:
                    $ordem = "marca." . $ordem;
            }


            $marcas = Marca::query()
                ->orderBy($ordem, $sentido)
                ->paginate($quantidade, "*", '', $pagina);

            return response()->json([
                'menssage' => "",
                'retorno' => $marcas,
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro",
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
                $marca = Marca::query()->find($id);

                if (!$marca) {
                    throw new \Exception("Marca não encontrada.", 422);
                }
            } else {
                $marca = new Marca();
                $marca->created_from = $usuarioId;
            }

            $marca->updated_from = $usuarioId;

            if (key_exists('nome', $dados)) {
                $marca->nome = $dados['nome'];
            }

            if (!$marca->save()) {
                return response()->json([
                    'menssage' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $marca->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados gravados com sucesso!",
                'data' => ['id' => $marca->id],
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


            $marca = Marca::query()->find($id);

            if (!$marca) {
                throw new \Exception("Marca não encontrada.", 422);
            }


            if (!$marca->delete()) {
                return response()->json([
                    'menssage' => "Erro ao remover dados!",
                    'data' => null,
                    'errors' => $marca->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados removidos com sucesso!",
                'data' => [],
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


}
