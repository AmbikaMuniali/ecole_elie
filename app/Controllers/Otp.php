<?php
namespace App\Controllers;
use App\Models\OtpModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Otp extends BaseController
{
    /**
     * Get all Otps
     * @return Response
     */
    public function index()
    {
        $model = new OtpModel();
        return $this->getResponse(
            [
                'message' => 'Otps retrieved successfully',
                'otps' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Otp
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
 
        $model = new OtpModel();
        $otp = $model->insert($input);
        
      //  $otp = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Otp added successfully',
                'otp' => $otp
            ]
        );
    }
    /**
     * Get a single otp by ID
     */
    public function show($id)
    {
        try {
            $model = new OtpModel();
            $otp = $model->findOtpById($id);
            return $this->getResponse(
                [
                    'message' => 'Otp retrieved successfully',
                    'otp' => $otp
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find otp for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new OtpModel();
            $model->findOtpById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $otp = $model->findOtpById($id);
            return $this->getResponse(
                [
                    'message' => 'Otp updated successfully',
                    'otp' => $otp
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
            $model = new OtpModel();
            $otp = $model->findOtpById($id);
            $model->delete($otp);
            return $this
                ->getResponse(
                    [
                        'message' => 'Otp deleted successfully',
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