<?php

namespace AppBundle\Controller\Api;


use AppBundle\Api\ApiProblem;
use Symfony\Component\HttpFoundation\Request;

class ApiVersionController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleVersionAction(Request $request){
        // Get the requested path. The api/ portion is removed from the cath all route configuration
        $request_path = $request->get("path");

        // Get the configured values of Api versions. This will be used to try and fallback to an older API version
        $api_versions = $this->getParameter("api_versions");

        // Extract requested API version from url
        $exploded_path = explode("/", $request_path);
        $url_api_version = $exploded_path[0];

        // Check if the requested API version is correct (we have it in our config)
        if(in_array($url_api_version, $api_versions)){
            // Get the index (on our config) of the requested API version.
            // If we find the version and it is not the first version try to fall back one version by redirecting
            $api_version_config_index = array_search($url_api_version, $api_versions);
            if($api_version_config_index && $api_version_config_index > 0){
                // Build the path of this api for the previeous version
                $previeous_api_version = $api_versions[$api_version_config_index-1];
                $redirect_path = "/api/".$previeous_api_version."/".implode("/", array_slice($exploded_path, 1));
                return $this->redirect($redirect_path);
            }
        }

        // If the above conditions fail retur a 404 Not Found Response
        $apiProblem = new ApiProblem(404);
        $response = $this->get("api.response_factory")->createResponse($apiProblem);
        return $response;
    }
}