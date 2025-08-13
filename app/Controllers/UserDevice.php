<?php
namespace App\Controllers;
use App\Models\UserDeviceModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class UserDevice extends BaseController
{
    /**
     * Get all UserDevices
     * @return Response
     */
    public function index()
    {
        $model = new UserDeviceModel();
        return $this->getResponse(
            [
                'message' => 'UserDevices retrieved successfully',
                'userdevices' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new UserDevice
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
 
        $model = new UserDeviceModel();
        $userdevice = $model->insert($input);
        
      //  $userdevice = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'UserDevice added successfully',
                'userdevice' => $userdevice
            ]
        );
    }
    /**
     * Get a single userdevice by ID
     */
    public function show($id)
    {
        try {
            $model = new UserDeviceModel();
            $userdevice = $model->findUserDeviceById($id);
            return $this->getResponse(
                [
                    'message' => 'UserDevice retrieved successfully',
                    'userdevice' => $userdevice
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find userdevice for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new UserDeviceModel();
            $model->findUserDeviceById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $userdevice = $model->findUserDeviceById($id);
            return $this->getResponse(
                [
                    'message' => 'UserDevice updated successfully',
                    'userdevice' => $userdevice
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
            $model = new UserDeviceModel();
            $userdevice = $model->findUserDeviceById($id);
            $model->delete($userdevice);
            return $this
                ->getResponse(
                    [
                        'message' => 'UserDevice deleted successfully',
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