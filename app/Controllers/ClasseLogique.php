<?php
namespace App\Controllers;
use App\Models\ClasseLogiqueModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class ClasseLogique extends BaseController
{
    /**
     * Get all ClasseLogiques
     * @return Response
     */
    public function index()
    {
        $model = new ClasseLogiqueModel();
        return $this->getResponse(
            [
                'message' => 'ClasseLogiques retrieved successfully',
                'result' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new ClasseLogique
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
 
        $model = new ClasseLogiqueModel();
        $classelogique = $model->insert($input);
        
      //  $classelogique = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'ClasseLogique added successfully',
                'result' => $classelogique
            ]
        );
    }
    /**
     * Get a single classelogique by ID
     */
    public function show($id)
    {
        try {
            $model = new ClasseLogiqueModel();
            $classelogique = $model->findClasseLogiqueById($id);
            return $this->getResponse(
                [
                    'message' => 'ClasseLogique retrieved successfully',
                    'result' => $classelogique
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find classelogique for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new ClasseLogiqueModel();
            $model->findById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $classelogique = $model->findById($id);
            return $this->getResponse(
                [
                    'message' => 'ClasseLogique updated successfully',
                    'result' => $classelogique
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
            $model = new ClasseLogiqueModel();
            $classelogique = (array) $model->findById($id);
            $model->delete($classelogique);
            return $this
                ->getResponse(
                    [
                        'message' => 'ClasseLogique deleted successfully',
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