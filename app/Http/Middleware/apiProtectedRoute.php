<?php

namespace App\Http\Middleware;


use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class apiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            JWTAuth::parseToken();
            $dadosToken = JWTAuth::getPayload(JWTAuth::getToken())->toArray();

            if (!$dadosToken['usuarioId'])
                throw new \Exception('Token inválido!', 401);

            $usuario = Usuario::query()->find($dadosToken['usuarioId']);

            if (!$usuario)
                throw new \Exception('Token inválido!', 401);

            $user = JWTAuth::authenticate();

        } catch (\Exception $e) {

            if ($e instanceof TokenInvalidException) {
                return response()->json(['status' => trans('messages.tokenAuth.invalid')], 401);
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json(['status' => trans('messages.tokenAuth.expired')], 401);
            } else
                return response()->json(['status' => trans('messages.tokenAuth.notFound')], 403);
        }

        return $next($request);

    }
}
