<?php

namespace App\Helpers;

use phpDocumentor\Reflection\Types\Boolean;

class Clockify
{
    /*
    |--------------------------------------------------------------------------
    | Clockify Helper
    |--------------------------------------------------------------------------
    |
    | This helper handles clockify api request and response for the application.
    | The helper used for calling the clockify api's
    |
    */

    public $apiKey;
    public $apiEndpoint;
    public $workspaceId;

    /**
     * Constructs a Clockify object
     * @param String $apiKey clockify API key (see https://clockify.github.io/clockify_api_docs/)
     * @param String $workspace clockify API key (see https://clockify.github.io/clockify_api_docs/#tag-Workspace)
     * @param String $apiEndpoint you shouldn't have to change this
     */
    public function __construct($apiKey, $workspace, $apiEndpoint = "https://api.clockify.me/api/v1/")
    {
        if (!$apiKey) {
            throw new \Exception('You must provide an API key.');
        } else {
            $this->apiKey = $apiKey;
        }

        if (!filter_var($apiEndpoint, FILTER_VALIDATE_URL)) {
            throw new \Exception('You must provide a valid API endpoint.');
        } else {
            $this->apiEndpoint = $apiEndpoint;
        }

        try {
            $clockfiyWorkspaces = $this->apiRequest('workspaces/');
        } catch (\Exception $e) {
            return $e;
        }

        foreach (json_decode($clockfiyWorkspaces) as $clockifyWorkspace) {
            if ($clockifyWorkspace->name === $workspace) {
                $this->workspaceId = $clockifyWorkspace->id;
            }
        }

        if (!$this->workspaceId) {
            throw new \Exception('You must provide a valid workspace.');
        }

        return $this;
    }

    /**
     * Request to api and return the data
     * @param String $apiPath clockify API relative path
     * @param Boolean $payload for set request method
     */
    public function apiRequest($apiPath, $payload = false)
    {
        $requestHeaders = array(
            'Content-Type:application/json',
            'X-Api-Key:' . $this->apiKey
        );

        if ($payload) {
            $requestHeaders[] = 'Content-Length:' . strlen($payload);
        }

        $ch = $this->getCurlObject(
            $this->apiEndpoint . $apiPath,
            $requestHeaders,
            isset($payload) ? $payload : false
        );

        $result = curl_exec($ch);

        if (curl_error($ch)) {
            return curl_error($ch);
        } else {
            return $result;
        }
    }

    /**
     * create a curl object
     * @param String $apiPath clockify API path
     * @param Object $payload for set header objects
     * @param Boolean $payload for set request method
     * @param Boolean $headerFunction for set header function
     */
    public function getCurlObject($url, $headers, $payload, $headerFunction = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($headerFunction) {
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, $headerFunction);
        }

        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($payload) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
        }

        return $ch;
    }
}
