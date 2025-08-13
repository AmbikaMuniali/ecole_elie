<?php
namespace App\Controllers;
use App\Models\AgentModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
class Agent extends BaseController
{
    /**
     * Get all Agents
     * @return Response
     */
    public function index()
    {
        $model = new AgentModel();
        return $this->getResponse(
            [
                'message' => 'Agents retrieved successfully',
                'agents' => $model->selectAll()
            ]
        );
    }
    /**
     * Create a new Agent
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
 
        $model = new AgentModel();
        $agent = $model->insert($input);
        
      //  $agent = // INSERTED ID
        return $this->getResponse(
            [
                'message' => 'Agent added successfully',
                'agent' => $agent
            ]
        );
    }
    /**
     * Get a single agent by ID
     */
    public function show($id)
    {
        try {
            $model = new AgentModel();
            $agent = $model->findAgentById($id);
            return $this->getResponse(
                [
                    'message' => 'Agent retrieved successfully',
                    'agent' => $agent
                ]
            );
        } catch (Exception $e) {
            return $this->getResponse(
                [
                    'message' => 'Could not find agent for specified ID'
                ],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    }
        public function update($id)
    {
        try {
            $model = new AgentModel();
            $model->findAgentById($id);
          $input = $this->getRequestInput($this->request);
          
            $model->update($id, $input);
            $agent = $model->findAgentById($id);
            return $this->getResponse(
                [
                    'message' => 'Agent updated successfully',
                    'agent' => $agent
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
            $model = new AgentModel();
            $agent = $model->findAgentById($id);
            $model->delete($agent);
            return $this
                ->getResponse(
                    [
                        'message' => 'Agent deleted successfully',
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