<?php
namespace App\Controllers;
use App\Models\ModuleModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Module extends BaseController
{
    /**
     * Get all Modules
     * @return Response
     */
    public function index()
    {
        $model = new ModuleModel();
        return $this->getResponse(
            [
                'message' => 'Modules retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Module
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
 
        $model = new ModuleModel();
        $module = $model->insert($input);
        
      //  $module = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Module added successfully',
                'result' => $module
            ]
        );
    }
    /**
     * Get a single module by ID
     */
    public function show($id)
    {
        try {
            $model = new ModuleModel();
            $module = $model->findModuleById($id);
            return $this->getResponse(
                [
                    'message' => 'Module retrieved successfully',
                    'result' => $module
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find module for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new ModuleModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $module = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Module updated successfully',
                    'result' => $module
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
            $model = new ModuleModel();
            $module = (array) $model->findById($id);
            $model->delete($module);
            return $this
                ->getResponse(
                    [
                        'message' => 'Module deleted successfully',
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