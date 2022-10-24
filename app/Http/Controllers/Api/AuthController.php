<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use http\Env\Response;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use function PHPUnit\Framework\throwException;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('apiJwt:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {

        try {

            $login = $request->get("checkgold_login", null);
            $senha = $request->get("checkgold_senha", null);

            if (empty($login))
                throw new \Exception("Informe o login.");

            if (empty($senha))
                throw new \Exception("Informe a senha.");


            $usuario = Usuario::query()->where('login', '=', trim($login))->first();

            if(!$usuario)
                throw new \Exception("Usuário e senha não conferem.");

            if (!Hash::check($senha, $usuario->senha))
                throw new \Exception("Usuário e senha não conferem.");


            $token = auth('api')->claims([
                'usuarioId' => $usuario->id
            ])->fromUser($usuario);

            

            return response()->json([
                'usuario' => $usuario,
                'checkgold_token' => $token
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],  '422');
        }


//        return "ae";
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'message' => 'Sucesso',
            'success' => trans('messages.tokenAuth.logout')
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        if (!$token = JWTAuth::getToken()) {
            return response()->json([
                'message' => trans('messages.given_data'),
                'errors' => ['token' => trans('token_not_send')]
            ], 401);
        }
        try {
            $token = JWTAuth::refresh();
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        }
        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 180,
        ]);
    }


}
