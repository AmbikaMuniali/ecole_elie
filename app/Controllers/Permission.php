<?php
namespace App\Controllers;
use App\Models\PermissionModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Permission extends BaseController
{
    /**
     * Get all Permissions
     * @return Response
     */
    public function index()
    {
        $model = new PermissionModel();
        return $this->getResponse(
            [
                'message' => 'Permissions retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Permission
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
 
        $model = new PermissionModel();
        $permission = $model->insert($input);
        
      //  $permission = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Permission added successfully',
                'result' => $permission
            ]
        );
    }
    /**
     * Get a single permission by ID
     */
    public function show($id)
    {
        try {
            $model = new PermissionModel();
            $permission = $model->findPermissionById($id);
            return $this->getResponse(
                [
                    'message' => 'Permission retrieved successfully',
                    'result' => $permission
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find permission for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new PermissionModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $permission = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Permission updated successfully',
                    'result' => $permission
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
            $model = new PermissionModel();
            $permission = (array) $model->findById($id);
            $model->delete($permission);
            return $this
                ->getResponse(
                    [
                        'message' => 'Permission deleted successfully',
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