<?php
namespace App\Controllers;
use App\Models\CoursModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Cours extends BaseController
{
    /**
     * Get all Courss
     * @return Response
     */
    public function index()
    {
        $model = new CoursModel();
        return $this->getResponse(
            [
                'message' => 'Courss retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Cours
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
 
        $model = new CoursModel();
        $cours = $model->insert($input);
        
      //  $cours = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Cours added successfully',
                'result' => $cours
            ]
        );
    }
    /**
     * Get a single cours by ID
     */
    public function show($id)
    {
        try {
            $model = new CoursModel();
            $cours = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Cours retrieved successfully',
                    'result' => $cours
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find cours for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new CoursModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $cours = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Cours updated successfully',
                    'result' => $cours
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
            $model = new CoursModel();
            $cours = (array) $model->findById($id);
            $model->delete($cours);
            return $this
                ->getResponse(
                    [
                        'message' => 'Cours deleted successfully',
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