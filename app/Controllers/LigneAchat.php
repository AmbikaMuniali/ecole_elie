<?php
namespace App\Controllers;
use App\Models\LigneAchatModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class LigneAchat extends BaseController
{
    /**
     * Get all LigneAchats
     * @return Response
     */
    public function index()
    {
        $model = new LigneAchatModel();
        return $this->getResponse(
            [
                'message' => 'LigneAchats retrieved successfully',
                'ligneachats' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new LigneAchat
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
 
        $model = new LigneAchatModel();
        $ligneachat = $model->insert($input);
        
      //  $ligneachat = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'LigneAchat added successfully',
                'ligneachat' => $ligneachat
            ]
        );
    }
    /**
     * Get a single ligneachat by ID
     */
    public function show($id)
    {
        try {
            $model = new LigneAchatModel();
            $ligneachat = $model->findLigneAchatById($id);
            return $this->getResponse(
                [
                    'message' => 'LigneAchat retrieved successfully',
                    'ligneachat' => $ligneachat
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find ligneachat for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new LigneAchatModel();
            $model->findLigneAchatById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $ligneachat = $model->findLigneAchatById($id);
            return $this->getResponse(
                [
                    'message' => 'LigneAchat updated successfully',
                    'ligneachat' => $ligneachat
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
            $model = new LigneAchatModel();
            $ligneachat = $model->findLigneAchatById($id);
            $model->delete($ligneachat);
            return $this
                ->getResponse(
                    [
                        'message' => 'LigneAchat deleted successfully',
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