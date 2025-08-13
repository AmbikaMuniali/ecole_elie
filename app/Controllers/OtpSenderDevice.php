<?php
namespace App\Controllers;
use App\Models\OtpSenderDeviceModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class OtpSenderDevice extends BaseController
{
    /**
     * Get all OtpSenderDevices
     * @return Response
     */
    public function index()
    {
        $model = new OtpSenderDeviceModel();
        return $this->getResponse(
            [
                'message' => 'OtpSenderDevices retrieved successfully',
                'otpsenderdevices' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new OtpSenderDevice
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
 
        $model = new OtpSenderDeviceModel();
        $otpsenderdevice = $model->insert($input);
        
      //  $otpsenderdevice = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'OtpSenderDevice added successfully',
                'otpsenderdevice' => $otpsenderdevice
            ]
        );
    }
    /**
     * Get a single otpsenderdevice by ID
     */
    public function show($id)
    {
        try {
            $model = new OtpSenderDeviceModel();
            $otpsenderdevice = $model->findOtpSenderDeviceById($id);
            return $this->getResponse(
                [
                    'message' => 'OtpSenderDevice retrieved successfully',
                    'otpsenderdevice' => $otpsenderdevice
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find otpsenderdevice for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new OtpSenderDeviceModel();
            $model->findOtpSenderDeviceById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $otpsenderdevice = $model->findOtpSenderDeviceById($id);
            return $this->getResponse(
                [
                    'message' => 'OtpSenderDevice updated successfully',
                    'otpsenderdevice' => $otpsenderdevice
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
            $model = new OtpSenderDeviceModel();
            $otpsenderdevice = $model->findOtpSenderDeviceById($id);
            $model->delete($otpsenderdevice);
            return $this
                ->getResponse(
                    [
                        'message' => 'OtpSenderDevice deleted successfully',
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