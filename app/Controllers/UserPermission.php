<?php
namespace App\Controllers;
use App\Models\UserPermissionModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class UserPermission extends BaseController
{
    /**
     * Get all UserPermissions
     * @return Response
     */
    public function index()
    {
        $model = new UserPermissionModel();
        return $this->getResponse(
            [
                'message' => 'UserPermissions retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new UserPermission
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
 
        $model = new UserPermissionModel();
        $userpermission = $model->insert($input);
        
      //  $userpermission = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'UserPermission added successfully',
                'result' => $userpermission
            ]
        );
    }
    /**
     * Get a single userpermission by ID
     */
    public function show($id)
    {
        try {
            $model = new UserPermissionModel();
            $userpermission = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'UserPermission retrieved successfully',
                    'result' => $userpermission
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find userpermission for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new UserPermissionModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $userpermission = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'UserPermission updated successfully',
                    'result' => $userpermission
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
            $model = new UserPermissionModel();
            $userpermission = (array) $model->findById($id);
            $model->delete($userpermission);
            return $this
                ->getResponse(
                    [
                        'message' => 'UserPermission deleted successfully',
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