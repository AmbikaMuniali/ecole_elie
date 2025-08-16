<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class User extends BaseController
{
    /**
     * Get all Users
     * @return Response
     */
    public function index()
    {
        $model = new UserModel();
        return $this->getResponse(
            [
                'message' => 'Users retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new User
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
 
        $model = new UserModel();
        $user = $model->insert($input);
        
      //  $user = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'User added successfully',
                'result' => $user
            ]
        );
    }
    /**
     * Get a single user by ID
     */
    public function show($id)
    {
        try {
            $model = new UserModel();
            $user = $model->findUserById($id);
            return $this->getResponse(
                [
                    'message' => 'User retrieved successfully',
                    'result' => $user
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find user for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new UserModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $user = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'User updated successfully',
                    'result' => $user
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
            $model = new UserModel();
            $user = (array) $model->findById($id);
            $model->delete($user);
            return $this
                ->getResponse(
                    [
                        'message' => 'User deleted successfully',
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