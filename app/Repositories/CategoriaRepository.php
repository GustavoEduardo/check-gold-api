<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Models\Marca;

use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;

class CategoriaRepository extends AbstractRepository
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
                    $ordem = "categoria." . $ordem;
            }


            $categorias = Categoria::query()
                ->orderBy($ordem, $sentido)
                ->paginate($quantidade, "*", '', $pagina);

            return response()->json([
                'menssage' => "",
                'retorno' => [$categorias],
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
                $categoria = Categoria::query()->find($id);

                if (!$categoria) {
                    throw new \Exception("Categoria não encontrada.", 422);
                }
            } else {
                $categoria = new Categoria();
                $categoria->created_from = $usuarioId;
            }

            $categoria->updated_from = $usuarioId;

            if (key_exists('nome', $dados)) {
                $categoria->nome = $dados['nome'];
            }

            if (!$categoria->save()) {
                return response()->json([
                    'menssage' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $categoria->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados gravados com sucesso!",
                'data' => ['id' => $categoria->id],
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


            $categoria = Categoria::query()->find($id);

            if (!$categoria) {
                throw new \Exception("Categoria não encontrada.", 422);
            }


            if (!$categoria->delete()) {
                return response()->json([
                    'menssage' => "Erro ao remover dados!",
                    'data' => null,
                    'errors' => $categoria->getErrors()
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
