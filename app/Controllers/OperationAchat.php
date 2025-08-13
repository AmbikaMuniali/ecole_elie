<?php
namespace App\Controllers;
use App\Models\OperationAchatModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class OperationAchat extends BaseController
{
    /**
     * Get all OperationAchats
     * @return Response
     */
    public function index()
    {
        $model = new OperationAchatModel();
        return $this->getResponse(
            [
                'message' => 'OperationAchats retrieved successfully',
                'operationachats' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new OperationAchat
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
 
        $model = new OperationAchatModel();
        $operationachat = $model->insert($input);
        
      //  $operationachat = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'OperationAchat added successfully',
                'operationachat' => $operationachat
            ]
        );
    }
    /**
     * Get a single operationachat by ID
     */
    public function show($id)
    {
        try {
            $model = new OperationAchatModel();
            $operationachat = $model->findOperationAchatById($id);
            return $this->getResponse(
                [
                    'message' => 'OperationAchat retrieved successfully',
                    'operationachat' => $operationachat
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find operationachat for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new OperationAchatModel();
            $model->findOperationAchatById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $operationachat = $model->findOperationAchatById($id);
            return $this->getResponse(
                [
                    'message' => 'OperationAchat updated successfully',
                    'operationachat' => $operationachat
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
            $model = new OperationAchatModel();
            $operationachat = $model->findOperationAchatById($id);
            $model->delete($operationachat);
            return $this
                ->getResponse(
                    [
                        'message' => 'OperationAchat deleted successfully',
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