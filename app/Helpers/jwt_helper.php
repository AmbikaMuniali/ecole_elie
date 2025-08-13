<?php

use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWTFromRequest($authenticationHeader): string
{
    if (is_null($authenticationHeader)) { //JWT is absent
        throw new Exception('Missing or invalid JWT in request');
    }
    //JWT is sent from client in the format Bearer XXXXXXXXX
    return explode(' ', $authenticationHeader)[1];
}

function validateJWTFromRequest(string $encodedToken)
{
    
    try {

        $strkey = Services::getSecretKey();
        $key = new Key($strkey,'HS256');
        $decodedToken = JWT::decode($encodedToken, $key);
        $userModel = new UserModel();
        $tokenExist = !empty($userModel->search(['where' => ['access_token'=> $encodedToken]]));
        $user = $userModel -> findUserById($decodedToken -> id_user);
        return $user;
        
    } catch (Exception $e) {
        throw new Exception('Invalid JWT in request');
        return null;
    }
    
}

function getSignedJWTForUser(string $id_user)
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'id_user' => $id_user,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration,
    ];

    $key = Services::getSecretKey();
    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
}
