<?php
namespace App\Controllers;
use App\Models\ClasseModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Classe extends BaseController
{
    /**
     * Get all Classes
     * @return Response
     */
    public function index()
    {
        $model = new ClasseModel();
        return $this->getResponse(
            [
                'message' => 'Classes retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Classe
     */
    public function store()
    {
        $rules = [
            //'name' => 'required',   //INSERT VALIDATION RULES
            //
            //
        ];
        $input = $this->getRequestInput($this->request);
        if ($this->validateRequest($input, $rules)) {
            return $this
                ->getResponse(
                    $this->validator->getErrors(),
                    ResponseInterface::HTTP_BAD_REQUEST
                );
        }
 
        $model = new ClasseModel();
        $classe = $model->insert($input);
        
      //  $classe = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Classe added successfully',
                'result' => $classe
            ]
        );
    }
    /**
     * Get a single classe by ID
     */
    public function show($id)
    {
        try {
            $model = new ClasseModel();
            $classe = $model->findClasseById($id);
            return $this->getResponse(
                [
                    'message' => 'Classe retrieved successfully',
                    'result' => $classe
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find classe for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new ClasseModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $classe = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Classe updated successfully',
                    'result' => $classe
                ]
            );
        } catch (Exception $exception) {
            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }


     public function delete($id) {

       return $this -> destroy($id);
    }

    public function create() {
        return $this -> store();
    }
    public function destroy($id)
    {
        try {
            $model = new ClasseModel();
            $classe = (array) $model->findById($id);
            $model->delete($classe);
            return $this
                ->getResponse(
                    [
                        'message' => 'Classe deleted successfully',
                    ]
                );
        } catch (Exception $exception) {
            return $this->getResponse(
                [
                    'message' => $exception->getMessage()
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
}