<?php
namespace App\Controllers;
use App\Models\AchatModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Achat extends BaseController
{
    /**
     * Get all Achats
     * @return Response
     */
    public function index()
    {
        $model = new AchatModel();
        return $this->getResponse(
            [
                'message' => 'Achats retrieved successfully',
                'achats' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Achat
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
 
        $model = new AchatModel();
        $achat = $model->insert($input);
        
      //  $achat = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Achat added successfully',
                'achat' => $achat
            ]
        );
    }
    /**
     * Get a single achat by ID
     */
    public function show($id)
    {
        try {
            $model = new AchatModel();
            $achat = $model->findAchatById($id);
            return $this->getResponse(
                [
                    'message' => 'Achat retrieved successfully',
                    'achat' => $achat
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find achat for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new AchatModel();
            $model->findAchatById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $achat = $model->findAchatById($id);
            return $this->getResponse(
                [
                    'message' => 'Achat updated successfully',
                    'achat' => $achat
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
            $model = new AchatModel();
            $achat = $model->findAchatById($id);
            $model->delete($achat);
            return $this
                ->getResponse(
                    [
                        'message' => 'Achat deleted successfully',
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