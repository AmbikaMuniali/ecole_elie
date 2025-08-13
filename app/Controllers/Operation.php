<?php
namespace App\Controllers;
use App\Models\OperationModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Operation extends BaseController
{
    /**
     * Get all Operations
     * @return Response
     */
    public function index()
    {
        $model = new OperationModel();
        return $this->getResponse(
            [
                'message' => 'Operations retrieved successfully',
                'operations' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Operation
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
 
        $model = new OperationModel();
        $operation = $model->insert($input);
        
      //  $operation = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Operation added successfully',
                'operation' => $operation
            ]
        );
    }
    /**
     * Get a single operation by ID
     */
    public function show($id)
    {
        try {
            $model = new OperationModel();
            $operation = $model->findOperationById($id);
            return $this->getResponse(
                [
                    'message' => 'Operation retrieved successfully',
                    'operation' => $operation
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find operation for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new OperationModel();
            $model->findOperationById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $operation = $model->findOperationById($id);
            return $this->getResponse(
                [
                    'message' => 'Operation updated successfully',
                    'operation' => $operation
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
            $model = new OperationModel();
            $operation = $model->findOperationById($id);
            $model->delete($operation);
            return $this
                ->getResponse(
                    [
                        'message' => 'Operation deleted successfully',
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