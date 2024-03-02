<?php
namespace Imantalebi\MoadianPhpSdk\Services;


use Firebase\JWT\JWT;
class JwsService{

    public static function create($privateKey, array $header, array $payload){

        if( ! (isset($header['alg']) && $header['alg'] == 'RS256')){
            throw new \Exception('Cannot create JWS, the supported "alg" is (RS256).');
        }

        $jwtHeader = JWT::urlsafeB64Encode(JWT::jsonEncode($header));

        $jwtPayload = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
 

        $signature    =  JWT::sign( $jwtHeader.".".$jwtPayload,  $privateKey,  $header['alg'] );

        $jwtSig =  JWT::urlsafeB64Encode($signature);

        $jws = $jwtHeader.".".$jwtPayload.".".$jwtSig;

        return $jws;
    }

}