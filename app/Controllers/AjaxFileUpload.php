<?php 

namespace  App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Files\File;

class AjaxFileUpload extends BaseController
{
    public function index()
    {    
         return view('index');
    }
 
    public function upload()
    {  
        helper(['form', 'url']);
         
        
        $validationRule = [
            'file' => [
                'label' => 'Image File',
                'rules' => [
                    'uploaded[file]',
                    'is_image[file]',
                    'mime_in[file,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                    'max_size[file,1024]',
                    'max_dims[file,2024,1768]',
                ],
            ],
        ];
    


        if (! $this->validate($validationRule)) {
            $data = ['errors' => $this->validator->getErrors()];

            return $this -> getResponse(['upload_form'=> $data]);
        }

        $img = $this->request->getFile('file');



        if (! $img->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/';


            $newName = 'img_' . $img->getRandomName();
            $img->move(WRITEPATH . 'uploads', $newName);

            return $this -> getResponse(['name'=> $newName]);
        }

        $data = ['errors' => 'The file has already been moved.'];

        return $this -> getResponse(['upload_form'=> $data]);




       // use CodeIgniter\Files\File;


        
        
    }
}