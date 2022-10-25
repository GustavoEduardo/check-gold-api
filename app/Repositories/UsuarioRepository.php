<?php

namespace App\Repositories;

use App\Models\Usuario;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioRepository extends AbstractRepository
{

    protected $model = Usuario::class;

    public function __construct()
    {

    }

    public function gravar($request, $id = null)
    {
        try {
            $dados = $request->all();

            if (!empty($id)) {
                $usuario = Usuario::query()->find($id);

                if (!$usuario) {
                    throw new \Exception("Usuario não encontrado.", 422);
                }
            } else {
                $usuario = new Usuario();
            }


            if (key_exists('nome', $dados)) {
                $usuario->nome = $dados['nome'];
            }

            if (key_exists('email', $dados)) {

                $existe = !empty(Usuario::query()
                    ->where("email", "=", $dados['email'])
                    ->first()
                );

                if($existe){
                    throw new \Exception("Email já cadastrado");
                }

                $usuario->email = $dados['email'];
                $usuario->login = $dados['email'];
            }

            if (key_exists('senha', $dados)) {

                if(strlen($dados['senha'] < 6)){
                    throw new \Exception("Senha deve conter no mínimo 6 caracteres");
                }

                $usuario->senha = Hash::make($dados['senha']);
            }



            if (!$usuario->save()) {
                return response()->json([
                    'message' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $usuario->getErrors()
                ], 422);
            }


            return response()->json([
                'message' => "Dados gravados com sucesso!",
                'data' => ['id' => $usuario->id],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'message' => "Erro ao gravar dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }

    public function editar($request, $id = null)
    {
        try {
            $dados = $request->all();

            if (empty($id)) {
                throw new \Exception("Usuario não encontrado.", 422);

            } else {
                $usuario = Usuario::query()->find($id);

                if (!$usuario) {
                    throw new \Exception("Usuario não encontrado.", 422);
                }

                $usuario = new Usuario();
            }


            if (key_exists('nome', $dados)) {
                $usuario->nome = $dados['nome'];
            }

            if (key_exists('email', $dados)) {

                $existe = !empty(Usuario::query()
                    ->where("email", "=", $dados['email'])
                    ->first()
                );

                if($existe){
                    throw new \Exception("Email já cadastrado");
                }

                $usuario->email = $dados['email'];
                $usuario->login = $dados['email'];
            }

            if (key_exists('senha', $dados)) {

                if(strlen($dados['senha'] < 6)){
                    throw new \Exception("Senha deve conter no mínimo 6 caracteres");
                }

                $usuario->senha = Hash::make($dados['senha']);
            }



            if (!$usuario->save()) {
                return response()->json([
                    'message' => "Erro ao gravar dados!",
                    'data' => null,
                    'errors' => $usuario->getErrors()
                ], 422);
            }


            return response()->json([
                'message' => "Dados gravados com sucesso!",
                'data' => ['id' => $usuario->id],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'message' => "Erro ao gravar dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

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
                    $ordem = "usuario." . $ordem;
            }


            $usuarios = Usuario::query()
                ->orderBy($ordem, $sentido)
                ->paginate($quantidade, "*", '', $pagina);

            return response()->json([
                'menssage' => "",
                'retorno' => $usuarios,
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


    public function remover($id = null)
    {
        try {


            $usuario = Usuario::query()->find($id);

            if (!$usuario) {
                throw new \Exception("usuario não encontrado.", 422);
            }


            if (!$usuario->delete()) {
                return response()->json([
                    'menssage' => "Erro ao remover dados!",
                    'data' => null,
                    'errors' => $usuario->getErrors()
                ], 422);
            }

            return response()->json([
                'menssage' => "Dados removidos com sucesso!",
                'data' => [],
                'errors' => []
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'menssage' => "Erro ao remover os dados!",
                'data' => null,
                'errors' => array($e->getMessage())
            ], 422);
        }

    }

}
