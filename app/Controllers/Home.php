<?php

namespace App\Controllers;
use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index()
    {
        // return view('Common');
        return $this->getResponse(['message' => 'WELCOME TO KASOKO\'O']);
    }

    public function test() {
        // $model = new UsersModel();
        // return base_url();
        helper('url');
        return view('myPaths');
        
    }

    public function admin() {
        // $model = new UsersModel();
        // return base_url();
        helper('url');
        return view('admindashboard');
        
    }

    public function gestionAgent() {
        helper ('url');
        return view('gestion_agents');
    }

    /*
     *
     * INFORM CLIENT HE CANT CONNECT THE SITE WITHOUT CONNEXION
     *
     */
    public function requireLogin() {
        return $this->getResponse(
            [
                'message' => 'user not connected',
                'error' => 'user not connected',
            ], ResponseInterface::HTTP_UNAUTHORIZED
        );
    }


    /*
     *
     * INFORM CLIENT HE CANT ACCESS THE RESULT WITHOUT PERMISSION
     *
     */
    public function requirePermission() {
        return $this->getResponse(
            [
                'message' => 'user has not permission',
                'error' => 'user has not permission',
            ], ResponseInterface::HTTP_UNAUTHORIZED
        );
    }
}
