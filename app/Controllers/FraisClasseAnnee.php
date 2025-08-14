<?php
namespace App\Controllers;
use App\Models\FraisClasseAnneeModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class FraisClasseAnnee extends BaseController
{
    /**
     * Get all FraisClasseAnnees
     * @return Response
     */
    public function index()
    {
        $model = new FraisClasseAnneeModel();
        return $this->getResponse(
            [
                'message' => 'FraisClasseAnnees retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new FraisClasseAnnee
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
 
        $model = new FraisClasseAnneeModel();
        $fraisclasseannee = $model->insert($input);
        
      //  $fraisclasseannee = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'FraisClasseAnnee added successfully',
                'result' => $fraisclasseannee
            ]
        );
    }
    /**
     * Get a single fraisclasseannee by ID
     */
    public function show($id)
    {
        try {
            $model = new FraisClasseAnneeModel();
            $fraisclasseannee = $model->findFraisClasseAnneeById($id);
            return $this->getResponse(
                [
                    'message' => 'FraisClasseAnnee retrieved successfully',
                    'result' => $fraisclasseannee
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find fraisclasseannee for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new FraisClasseAnneeModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $fraisclasseannee = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'FraisClasseAnnee updated successfully',
                    'result' => $fraisclasseannee
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
            $model = new FraisClasseAnneeModel();
            $fraisclasseannee = (array) $model->findById($id);
            $model->delete($fraisclasseannee);
            return $this
                ->getResponse(
                    [
                        'message' => 'FraisClasseAnnee deleted successfully',
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