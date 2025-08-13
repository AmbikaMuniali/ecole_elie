<?php
namespace App\Controllers;
use App\Models\OperationCommandeModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class OperationCommande extends BaseController
{
    /**
     * Get all OperationCommandes
     * @return Response
     */
    public function index()
    {
        $model = new OperationCommandeModel();
        return $this->getResponse(
            [
                'message' => 'OperationCommandes retrieved successfully',
                'operationcommandes' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new OperationCommande
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
 
        $model = new OperationCommandeModel();
        $operationcommande = $model->insert($input);
        
      //  $operationcommande = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'OperationCommande added successfully',
                'operationcommande' => $operationcommande
            ]
        );
    }
    /**
     * Get a single operationcommande by ID
     */
    public function show($id)
    {
        try {
            $model = new OperationCommandeModel();
            $operationcommande = $model->findOperationCommandeById($id);
            return $this->getResponse(
                [
                    'message' => 'OperationCommande retrieved successfully',
                    'operationcommande' => $operationcommande
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find operationcommande for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new OperationCommandeModel();
            $model->findOperationCommandeById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $operationcommande = $model->findOperationCommandeById($id);
            return $this->getResponse(
                [
                    'message' => 'OperationCommande updated successfully',
                    'operationcommande' => $operationcommande
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
            $model = new OperationCommandeModel();
            $operationcommande = $model->findOperationCommandeById($id);
            $model->delete($operationcommande);
            return $this
                ->getResponse(
                    [
                        'message' => 'OperationCommande deleted successfully',
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