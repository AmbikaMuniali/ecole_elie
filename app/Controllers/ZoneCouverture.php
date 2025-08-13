<?php
namespace App\Controllers;
use App\Models\ZoneCouvertureModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class ZoneCouverture extends BaseController
{
    /**
     * Get all ZoneCouvertures
     * @return Response
     */
    public function index()
    {
        $model = new ZoneCouvertureModel();
        return $this->getResponse(
            [
                'message' => 'ZoneCouvertures retrieved successfully',
                'zonecouvertures' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new ZoneCouverture
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
 
        $model = new ZoneCouvertureModel();
        $zonecouverture = $model->insert($input);
        
      //  $zonecouverture = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'ZoneCouverture added successfully',
                'zonecouverture' => $zonecouverture
            ]
        );
    }
    /**
     * Get a single zonecouverture by ID
     */
    public function show($id)
    {
        try {
            $model = new ZoneCouvertureModel();
            $zonecouverture = $model->findZoneCouvertureById($id);
            return $this->getResponse(
                [
                    'message' => 'ZoneCouverture retrieved successfully',
                    'zonecouverture' => $zonecouverture
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find zonecouverture for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new ZoneCouvertureModel();
            $model->findZoneCouvertureById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $zonecouverture = $model->findZoneCouvertureById($id);
            return $this->getResponse(
                [
                    'message' => 'ZoneCouverture updated successfully',
                    'zonecouverture' => $zonecouverture
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
            $model = new ZoneCouvertureModel();
            $zonecouverture = $model->findZoneCouvertureById($id);
            $model->delete($zonecouverture);
            return $this
                ->getResponse(
                    [
                        'message' => 'ZoneCouverture deleted successfully',
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