<?php

namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;

class DecriptJwt
{
    public function decriptJwt()
    {
        $token = JWTAuth::getToken();
        $api = JWTAuth::getPayload($token)->toArray();
        return $api['usuarioId'];
    }

//    public function uniqueLogin()
//    {
//        $token = JWTAuth::getToken();
//        $api = JWTAuth::getPayload($token)->toArray();
//        return $api['access_token'];
//    }
//
//    public function userIp()
//    {
//        $token = JWTAuth::getToken();
//        $api = JWTAuth::getPayload($token)->toArray();
//        return $api['ip_user'];
//    }
//
//    public function userAdimin()
//    {
//        $token = JWTAuth::getToken();
//        $api = JWTAuth::getPayload($token)->toArray();
////        return $api['admin'];
//    }
}
