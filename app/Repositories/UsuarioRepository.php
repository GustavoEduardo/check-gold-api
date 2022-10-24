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

                if(strlen($dados['senha'] < 5)){
                    throw new \Exception("Senha deve conter no mínimo 6 caracteres");
                }

                $usuario->senha = Hash::make($dados['senha']);
            }

//            if (key_exists('loja_nome', $dados)) {
//                $usuario->senha = $dados['loja_nome'];
//            }

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


}
