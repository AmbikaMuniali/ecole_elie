<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\Auth;
use Exception;

class MyAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

        // $user = $session
        // $auth = new Auth();
        // if (! $auth->isLoggedIn()) {
        //     return redirect()->to(site_url('/requirelogin'));
        // }

        
        

        $session = session ();
        $user = $session -> get('user');



        if (empty($user)) {

            // try to log him with credential

            
            $connectedByToken = $this -> tryLoginByToken($request);

            if(!$connectedByToken) return redirect()->to(site_url('/require-login' ));
        }

        


        
        
    }



    public function after(RequestInterface $request,
                          ResponseInterface $response,
                          $arguments = null)
    {
        
        
        
    }


    public function tryLoginByToken($request) {


        // 1. LOGIN PAR ACCESS TOKEN
        helper('jwt');

        $header = $request->getHeaderLine('Authorization');

        if (empty($header)) return false;
        $jwt = getJWTFromRequest($header);

        $user = validateJWTFromRequest($jwt);

        
        if($user) {
            
            $session = session();
            $session -> set('user', $user);
            return true;
        }

        return false;
    }


    
}





// <?php

// namespace App\Filters;

// use CodeIgniter\Filters\FilterInterface;
// use CodeIgniter\HTTP\RequestInterface;
// use CodeIgniter\HTTP\ResponseInterface;
// use App\Controllers\ResponseInterface;

// class AuthFilter implements FilterInterface
// {
//     public function before(RequestInterface $request, $arguments = null)
//     {
//         $auth = service('auth');

//         if (! $auth->isLoggedIn()) {
//             return redirect()->to(site_url('login'));
//         }
//     }
// }