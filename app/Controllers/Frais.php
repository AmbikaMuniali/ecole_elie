<?php
namespace App\Controllers;
use App\Models\FraisModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Frais extends BaseController
{
    /**
     * Get all Fraiss
     * @return Response
     */
    public function index()
    {
        $model = new FraisModel();
        return $this->getResponse(
            [
                'message' => 'Fraiss retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Frais
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
 
        $model = new FraisModel();
        $frais = $model->insert($input);
        
      //  $frais = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Frais added successfully',
                'result' => $frais
            ]
        );
    }
    /**
     * Get a single frais by ID
     */
    public function show($id)
    {
        try {
            $model = new FraisModel();
            $frais = $model->findFraisById($id);
            return $this->getResponse(
                [
                    'message' => 'Frais retrieved successfully',
                    'result' => $frais
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find frais for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new FraisModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $frais = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Frais updated successfully',
                    'result' => $frais
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
            $model = new FraisModel();
            $frais = (array) $model->findById($id);
            $model->delete($frais);
            return $this
                ->getResponse(
                    [
                        'message' => 'Frais deleted successfully',
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