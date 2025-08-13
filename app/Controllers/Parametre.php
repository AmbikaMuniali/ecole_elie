<?php
namespace App\Controllers;
use App\Models\ParametreModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Parametre extends BaseController
{
    /**
     * Get all Parametres
     * @return Response
     */
    public function index()
    {
        $model = new ParametreModel();
        return $this->getResponse(
            [
                'message' => 'Parametres retrieved successfully',
                'parametres' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Parametre
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
 
        $model = new ParametreModel();
        $parametre = $model->insert($input);
        
      //  $parametre = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Parametre added successfully',
                'parametre' => $parametre
            ]
        );
    }
    /**
     * Get a single parametre by ID
     */
    public function show($id)
    {
        try {
            $model = new ParametreModel();
            $parametre = $model->findParametreById($id);
            return $this->getResponse(
                [
                    'message' => 'Parametre retrieved successfully',
                    'parametre' => $parametre
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find parametre for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new ParametreModel();
            $model->findParametreById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $parametre = $model->findParametreById($id);
            return $this->getResponse(
                [
                    'message' => 'Parametre updated successfully',
                    'parametre' => $parametre
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
            $model = new ParametreModel();
            $parametre = $model->findParametreById($id);
            $model->delete($parametre);
            return $this
                ->getResponse(
                    [
                        'message' => 'Parametre deleted successfully',
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