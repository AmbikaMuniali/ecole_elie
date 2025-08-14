<?php
namespace App\Controllers;
use App\Models\EleveClasseAnneeModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class EleveClasseAnnee extends BaseController
{
    /**
     * Get all EleveClasseAnnees
     * @return Response
     */
    public function index()
    {
        $model = new EleveClasseAnneeModel();
        return $this->getResponse(
            [
                'message' => 'EleveClasseAnnees retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new EleveClasseAnnee
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
 
        $model = new EleveClasseAnneeModel();
        $eleveclasseannee = $model->insert($input);
        
      //  $eleveclasseannee = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'EleveClasseAnnee added successfully',
                'result' => $eleveclasseannee
            ]
        );
    }
    /**
     * Get a single eleveclasseannee by ID
     */
    public function show($id)
    {
        try {
            $model = new EleveClasseAnneeModel();
            $eleveclasseannee = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'EleveClasseAnnee retrieved successfully',
                    'result' => $eleveclasseannee
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find eleveclasseannee for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new EleveClasseAnneeModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $eleveclasseannee = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'EleveClasseAnnee updated successfully',
                    'result' => $eleveclasseannee
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
            $model = new EleveClasseAnneeModel();
            $eleveclasseannee = (array) $model->findById($id);
            $model->delete($eleveclasseannee);
            return $this
                ->getResponse(
                    [
                        'message' => 'EleveClasseAnnee deleted successfully',
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