<?php

namespace SiteRig\Brevo\Http\Controllers;

use Edalzell\Forma\ConfigController as BaseController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\Configuration;
use Illuminate\Support\Arr;

class ConfigController extends BaseController
{
    /**
     * Validate API key before loading config into the form.
     *
     * @param string $handle
     * @return array
     */
    protected function preProcess(string $handle): array
    {
        $config = config($handle);
        
        // Check if API key is set
        $apiKey = Arr::get($config, 'api_key');
        
        if ($apiKey) {
            // Validate the API key by making a simple API call
            try {
                $brevoConfig = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
                $contactsApi = new ContactsApi(new Client(), $brevoConfig);
                
                // Try to get lists with limit 1 as a validation check
                $contactsApi->getLists(1, 0);
            } catch (ClientException $e) {
                // Check if it's a 401 Unauthorized error
                $response = $e->getResponse();
                $statusCode = $response ? $response->getStatusCode() : null;
                
                if ($statusCode === 401) {
                    // Try to get the error message from the response
                    $errorMessage = 'Not enabled or has been deleted on Brevo';
                    
                    if ($response) {
                        try {
                            $responseBody = json_decode($response->getBody()->getContents(), true);
                            if (isset($responseBody['message'])) {
                                $errorMessage = $responseBody['message'];
                            }
                        } catch (\Exception $bodyException) {
                            // If we can't read the body, use the default message
                        }
                    }
                    
                    // Display the error message in the API key field
                    $config['api_key'] = '⚠️ Brevo API Error: ' . htmlspecialchars($errorMessage);
                }
            } catch (\Exception $e) {
                // Check for 401 errors in other exception types
                $message = $e->getMessage();
                
                if (str_contains($message, '401') ||
                    str_contains($message, 'unauthorized') ||
                    str_contains($message, 'API Key is not enabled')) {
                    
                    // Extract error message if available
                    $errorMessage = 'Not enabled or has been deleted on Brevo';
                    if (preg_match('/"message":"([^"]+)"/', $message, $matches)) {
                        $errorMessage = $matches[1];
                    }
                    
                    // Store the error message in a separate field that will be displayed
                    $config['api_key'] = '⚠️ Brevo API Error: ' . htmlspecialchars($errorMessage);
                }
            }
        }
        
        return $config;
    }
}
