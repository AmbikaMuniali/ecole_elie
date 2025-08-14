<?php
namespace App\Controllers;
use App\Models\AnneeScolaireModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class AnneeScolaire extends BaseController
{
    /**
     * Get all AnneeScolaires
     * @return Response
     */
    public function index()
    {
        $model = new AnneeScolaireModel();
        return $this->getResponse(
            [
                'message' => 'AnneeScolaires retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new AnneeScolaire
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
 
        $model = new AnneeScolaireModel();
        $anneescolaire = $model->insert($input);
        
      //  $anneescolaire = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'AnneeScolaire added successfully',
                'result' => $anneescolaire
            ]
        );
    }
    /**
     * Get a single anneescolaire by ID
     */
    public function show($id)
    {
        try {
            $model = new AnneeScolaireModel();
            $anneescolaire = $model->findAnneeScolaireById($id);
            return $this->getResponse(
                [
                    'message' => 'AnneeScolaire retrieved successfully',
                    'result' => $anneescolaire
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find anneescolaire for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new AnneeScolaireModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $anneescolaire = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'AnneeScolaire updated successfully',
                    'result' => $anneescolaire
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
            $model = new AnneeScolaireModel();
            $anneescolaire = (array) $model->findById($id);
            $model->delete($anneescolaire);
            return $this
                ->getResponse(
                    [
                        'message' => 'AnneeScolaire deleted successfully',
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