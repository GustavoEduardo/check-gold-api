<?php

namespace App\Repositories;

use App\Models\Usuario;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DecriptJwt;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioLogadoRepository extends AbstractRepository
{

    protected $model = Usuario::class;
    protected $usuarioId = null;

    public function __construct()
    {


        try {
            $dadosToken = new DecriptJwt;
            $this->usuarioId = $dadosToken->decriptJwt();
        } catch (\Exception $e) {
            $this->usuarioId = null;
        }

    }

    public function index()
    {

        $usuario = Usuario::query()->find($this->usuarioId);


        return response()->json($usuario);

    }


}
