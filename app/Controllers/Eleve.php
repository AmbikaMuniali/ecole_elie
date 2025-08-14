<?php
namespace App\Controllers;
use App\Models\EleveModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Eleve extends BaseController
{
    /**
     * Get all Eleves
     * @return Response
     */
    public function index()
    {
        $model = new EleveModel();
        return $this->getResponse(
            [
                'message' => 'Eleves retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Eleve
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
 
        $model = new EleveModel();
        $eleve = $model->insert($input);
        
      //  $eleve = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Eleve added successfully',
                'result' => $eleve
            ]
        );
    }
    /**
     * Get a single eleve by ID
     */
    public function show($id)
    {
        try {
            $model = new EleveModel();
            $eleve = $model->findEleveById($id);
            return $this->getResponse(
                [
                    'message' => 'Eleve retrieved successfully',
                    'result' => $eleve
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find eleve for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new EleveModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $eleve = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Eleve updated successfully',
                    'result' => $eleve
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
            $model = new EleveModel();
            $eleve = (array) $model->findById($id);
            $model->delete($eleve);
            return $this
                ->getResponse(
                    [
                        'message' => 'Eleve deleted successfully',
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