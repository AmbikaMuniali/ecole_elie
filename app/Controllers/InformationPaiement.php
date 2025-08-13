<?php
namespace App\Controllers;
use App\Models\InformationPaiementModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class InformationPaiement extends BaseController
{
    /**
     * Get all InformationPaiements
     * @return Response
     */
    public function index()
    {
        $model = new InformationPaiementModel();
        return $this->getResponse(
            [
                'message' => 'InformationPaiements retrieved successfully',
                'informationpaiements' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new InformationPaiement
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
 
        $model = new InformationPaiementModel();
        $informationpaiement = $model->insert($input);
        
      //  $informationpaiement = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'InformationPaiement added successfully',
                'informationpaiement' => $informationpaiement
            ]
        );
    }
    /**
     * Get a single informationpaiement by ID
     */
    public function show($id)
    {
        try {
            $model = new InformationPaiementModel();
            $informationpaiement = $model->findInformationPaiementById($id);
            return $this->getResponse(
                [
                    'message' => 'InformationPaiement retrieved successfully',
                    'informationpaiement' => $informationpaiement
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find informationpaiement for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new InformationPaiementModel();
            $model->findInformationPaiementById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $informationpaiement = $model->findInformationPaiementById($id);
            return $this->getResponse(
                [
                    'message' => 'InformationPaiement updated successfully',
                    'informationpaiement' => $informationpaiement
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
            $model = new InformationPaiementModel();
            $informationpaiement = $model->findInformationPaiementById($id);
            $model->delete($informationpaiement);
            return $this
                ->getResponse(
                    [
                        'message' => 'InformationPaiement deleted successfully',
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