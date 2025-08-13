<?php
namespace App\Controllers;
use App\Models\DroitsAgentModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class DroitsAgent extends BaseController
{
    /**
     * Get all DroitsAgents
     * @return Response
     */
    public function index()
    {
        $model = new DroitsAgentModel();
        return $this->getResponse(
            [
                'message' => 'DroitsAgents retrieved successfully',
                'droitsagents' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new DroitsAgent
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
 
        $model = new DroitsAgentModel();
        $droitsagent = $model->insert($input);
        
      //  $droitsagent = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'DroitsAgent added successfully',
                'droitsagent' => $droitsagent
            ]
        );
    }
    /**
     * Get a single droitsagent by ID
     */
    public function show($id)
    {
        try {
            $model = new DroitsAgentModel();
            $droitsagent = $model->findDroitsAgentById($id);
            return $this->getResponse(
                [
                    'message' => 'DroitsAgent retrieved successfully',
                    'droitsagent' => $droitsagent
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find droitsagent for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new DroitsAgentModel();
            $model->findDroitsAgentById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $droitsagent = $model->findDroitsAgentById($id);
            return $this->getResponse(
                [
                    'message' => 'DroitsAgent updated successfully',
                    'droitsagent' => $droitsagent
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
            $model = new DroitsAgentModel();
            $droitsagent = (array) $model->findDroitsAgentById($id);
            $model->delete($droitsagent['id']);
            return $this
                ->getResponse(
                    [
                        'message' => 'DroitsAgent deleted successfully',
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