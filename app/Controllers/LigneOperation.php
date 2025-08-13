<?php
namespace App\Controllers;
use App\Models\LigneOperationModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class LigneOperation extends BaseController
{
    /**
     * Get all LigneOperations
     * @return Response
     */
    public function index()
    {
        $model = new LigneOperationModel();
        return $this->getResponse(
            [
                'message' => 'LigneOperations retrieved successfully',
                'ligneoperations' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new LigneOperation
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
 
        $model = new LigneOperationModel();
        $ligneoperation = $model->insert($input);
        
      //  $ligneoperation = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'LigneOperation added successfully',
                'ligneoperation' => $ligneoperation
            ]
        );
    }
    /**
     * Get a single ligneoperation by ID
     */
    public function show($id)
    {
        try {
            $model = new LigneOperationModel();
            $ligneoperation = $model->findLigneOperationById($id);
            return $this->getResponse(
                [
                    'message' => 'LigneOperation retrieved successfully',
                    'ligneoperation' => $ligneoperation
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find ligneoperation for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new LigneOperationModel();
            $model->findLigneOperationById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $ligneoperation = $model->findLigneOperationById($id);
            return $this->getResponse(
                [
                    'message' => 'LigneOperation updated successfully',
                    'ligneoperation' => $ligneoperation
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
            $model = new LigneOperationModel();
            $ligneoperation = $model->findLigneOperationById($id);
            $model->delete($ligneoperation);
            return $this
                ->getResponse(
                    [
                        'message' => 'LigneOperation deleted successfully',
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