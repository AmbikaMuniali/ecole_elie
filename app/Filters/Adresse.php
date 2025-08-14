<?php
namespace App\Controllers;
use App\Models\AdresseModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Adresse extends BaseController
{
    /**
     * Get all Adresses
     * @return Response
     */
    public function index()
    {
        $model = new AdresseModel();
        return $this->getResponse(
            [
                'message' => 'Adresses retrieved successfully',
                'adresses' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Adresse
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
 
        $model = new AdresseModel();
        $adresse = $model->insert($input);
        
      //  $adresse = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Adresse added successfully',
                'adresse' => $adresse
            ]
        );
    }
    /**
     * Get a single adresse by ID
     */
    public function show($id)
    {
        try {
            $model = new AdresseModel();
            $adresse = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Adresse retrieved successfully',
                    'adresse' => $adresse
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find adresse for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new AdresseModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $adresse = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'Adresse updated successfully',
                    'adresse' => $adresse
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
            $model = new AdresseModel();
            $adresse = $model->findById($id);
            $model->delete($adresse);
            return $this
                ->getResponse(
                    [
                        'message' => 'Adresse deleted successfully',
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