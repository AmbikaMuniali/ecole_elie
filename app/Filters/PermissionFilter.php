<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('user');

        // Check if the user is authenticated. If not, redirect to the login page.
        // This is a basic check. You might have a separate authentication filter for this.
        if (empty($user)) {
            return $this -> returnForbiden();
        }

        // The CodeIgniter Request object has all the information we need.
        // We'll get the current URI segments and the HTTP method.
        $uri = service('uri');
        $resourceName = $uri->getSegment(1); // The first segment is usually the resource name (e.g., 'droitsagent')
        $httpMethod = $request->getMethod(); // e.g., 'get', 'post', 'put', 'delete'

        // Determine the required permission code based on the HTTP method.
        $action = '';
        switch ($httpMethod) {
            case 'get':
                // The 'view' permission is used for both listing all resources and viewing a single one.
                $action = 'view';
                break;
            case 'post':
                // The key change is here:
                // If a second URI segment exists, we assume it's a resource ID,
                // and the action is an 'edit' (update). Otherwise, it's a 'create' action.
                if ($uri->getSegment(2)) {
                    $action = 'edit';
                } else {
                    $action = 'create';
                }
                break;
            case 'put':
            case 'patch':
                $action = 'edit';
                break;
            case 'delete':
                $action = 'delete';
                break;
            default:
                // If the method is not recognized, deny access by default.
                return $this -> returnForbiden();
        }

        // We need to convert the route name (e.g., 'droitsagent') to the permission code name (e.g., 'droits_agent').
        // This is a simple function to handle the known discrepancies based on your provided files.
        $permissionTableName = $this->convertRouteToPermissionName($resourceName);

        // Construct the full permission code string, e.g., 'droits_agent.view'
        $requiredPermission = $permissionTableName . '.' . $action;

        // Get the user's permissions from your helper function
        helper('permission');
        $userPermissions = getAgentPermissions($user);

        // Check if the user has the required permission
        if (!in_array($requiredPermission, $userPermissions)) {
            // User does not have the permission, redirect them.
            // return redirect()->to(site_url('/require-permission'));
            return $this -> returnForbiden( "\nYOU DO NOT HAVE " . json_encode($requiredPermission) . " PERMISSIONS\nYour permissions are : \n" . json_encode($userPermissions));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    public function returnForbiden($message = "") {
        
        // Return a 403 Forbidden response instead of redirecting.
        // This is useful for APIs or a more direct error response.
        $response = service('response');
        $response->setStatusCode(403, 'Forbidden');
        $response->setBody("You do not have the required permissions to access this resource. $message");
        return $response;
    }

    /**
     * Helper function to convert a route resource name to a permission table name.
     * // returns table name from the helper metadata of my tables
     * This can be expanded with more rules or a full mapping if needed.
     */
    protected function convertRouteToPermissionName(string $routeResourceName): string
    {

        helper('tables_metadata');
        $tableMetadata = getTableMetadata();
        foreach ($tableMetadata as $key => $value) {
            if ($routeResourceName === str_replace("_", "", $key)) {
                return $key;
            }
        }
        
        // By default, assume the resource name is the same as the permission name.
        return $routeResourceName;
    }
}
