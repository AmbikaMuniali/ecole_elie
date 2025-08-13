<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Controllers\Auth;
use Exception;

class MyMessageNotificationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // ... (your before filter logic)
    }

    public function after(RequestInterface $request,
                            ResponseInterface $response,
                            $arguments = null)
    {
        helper('OtpSenderDevice');
        $msg = json_decode($response->getBody());
        if (isset($msg->message)) {
            $id = $msg->message;
            $data = (array)model('MessageModel')->findMessageById($id);
            $data ['type_notif'] = 'NewMessage';
            $user = model('UserModel') -> findUserById($data['fkuser']);
            $data['user_id'] = $user -> id;



            // $data    = json_encode($data);

            // Send the response to the client immediately
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                // Fallback if not running under FastCGI (e.g., CLI)
                sendNewMessageNotification($data);
                return; // Or handle differently
            }

            // The code below will run in the background after the response is sent
            sendNewMessageNotification($data);
        }
    }
}