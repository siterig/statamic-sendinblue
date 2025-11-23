<?php

namespace SiteRig\Brevo;

use Illuminate\Support\Facades\Log;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use Brevo\Client\Api as BrevoAPI;
use Brevo\Client\Configuration as BrevoConfig;
use Brevo\Client\Model as BrevoModel;
use Statamic\Support\Arr;

class Brevo
{
    private $config = null;

    private $brevo_attributes = null;

    private $brevo_contacts = null;

    private $subscriber_data = [];

    private $subscriber_data_attributes = null;

    private $last_name_field_exists = false;

    public function __construct()
    {
        if ($api_key = config('brevo.api_key')) {

            // setup api-key in config
            $this->config = BrevoConfig::getDefaultConfiguration()->setApiKey('api-key', $api_key);

            // create AttributesAPI object
            $this->brevo_attributes = new BrevoAPI\AttributesApi(
                new GuzzleHttp\Client(),
                $this->config
            );

            // create ContactsAPI object
            $this->brevo_contacts = new BrevoAPI\ContactsApi(
                new GuzzleHttp\Client(),
                $this->config
            );

            // create contact object
            $this->subscriber_data = new BrevoModel\CreateContact();
        }
    }

    /**
     * Get Lists from Brevo
     *
     * @param int $list_id
     *
     * @return array
     */
    public function getLists(int $list_id = null)
    {
        // Check if API client is initialized
        if (!$this->brevo_contacts) {
            return [
                [
                    'id' => 0,
                    'title' => 'Error: API Key is not configured',
                ]
            ];
        }

        try {
            // Check if this is a request for a single group
            if ($list_id) {

                // Get single list
                $lists = $this->brevo_contacts->getList($list_id);

                // Check if there was an error getting this list by id
                if (property_exists($lists, 'code')) {

                    // Add error message
                    $contact_lists = [
                        'id' => 0,
                        'title' => 'Error: ' . $lists['message'],
                    ];

                } else {

                    // Add list to array
                    $contact_lists = [
                        'id' => $lists['id'],
                        'title' => $lists['name'],
                    ];

                }

            } else {

                // Set list parameters
                $params = array(
                    'limit' => 50,
                    'offset' => 0,
                    'sort' => 'desc',
                );

                // Get lists from Brevo
                $lists = $this->brevo_contacts->getLists($params['limit'], $params['offset'], $params['sort']);

                // Check if there was an error getting this list by id
                if (property_exists($lists, 'code')) {

                    // Add error message
                    $contact_lists = [
                        [
                            'id' => 0,
                            'title' => 'Error: ' . $lists['message'],
                        ]
                    ];

                } else {

                    // Create new array for groups
                    $contact_lists = [];

                    // Loop through lists and put into new array
                    foreach ($lists['lists'] as $key => $contact_list) {

                        // Add list to array
                        $contact_lists[] = [
                            'id' => $contact_list['id'],
                            'title' => $contact_list['name'],
                        ];

                    }

                }

            }

            // Return the array
            return $contact_lists;

        } catch (ClientException $e) {
            // Handle HTTP client errors (401, 403, etc.)
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : null;
            $errorMessage = 'API Error';

            if ($statusCode === 401) {
                $errorMessage = 'API Key is not enabled or has been deleted on Brevo';
                
                // Try to get the error message from the response
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
            }

            // Return error message in the expected format
            if ($list_id) {
                return [
                    'id' => 0,
                    'title' => 'Error: ' . $errorMessage,
                ];
            } else {
                return [
                    [
                        'id' => 0,
                        'title' => 'Error: ' . $errorMessage,
                    ]
                ];
            }

        } catch (\Exception $e) {
            // Handle any other exceptions
            $errorMessage = 'Unable to fetch lists from Brevo';
            
            // Check if the exception message contains useful error info
            $message = $e->getMessage();
            if (str_contains($message, '401') || str_contains($message, 'unauthorized') || str_contains($message, 'API Key is not enabled')) {
                $errorMessage = 'API Key is not enabled or has been deleted on Brevo';
                
                // Try to extract error message from JSON in exception message
                if (preg_match('/"message":"([^"]+)"/', $message, $matches)) {
                    $errorMessage = $matches[1];
                }
            }

            // Return error message in the expected format
            if ($list_id) {
                return [
                    'id' => 0,
                    'title' => 'Error: ' . $errorMessage,
                ];
            } else {
                return [
                    [
                        'id' => 0,
                        'title' => 'Error: ' . $errorMessage,
                    ]
                ];
            }
        }
    }

    /**
     * Get Attributes from Brevo
     *
     * @param   string  $attribute_name
     *
     * @return  array
     */
    public function getAttributes(string $attribute_name = null)
    {
        // Check if API client is initialized
        if (!$this->brevo_attributes) {
            return [
                [
                    'id' => '',
                    'title' => 'Error: API Key is not configured',
                ]
            ];
        }

        try {
            // Get attributes from Brevo
            $attributes = $this->brevo_attributes->getAttributes();

            // Create new array for fields
            $attributes_list = [];

            // Loop through fields and put into new array
            foreach ($attributes['attributes'] as $key => $attribute) {

                // Check this isn't the first name or email attribute
                if (!($attribute['name'] == 'FIRSTNAME' || $attribute['name'] == 'EMAIL')) {

                    // Check if this is a request for a single attribute
                    if ($attribute_name) {

                        // Check if this attribute matches
                        if ($attribute['name'] == $attribute_name) {

                            // Add list to array
                            $attributes_list = [
                                'id' => $attribute['name'],
                                'title' => $attribute['name']
                            ];

                        }

                    } else {

                        // Add attribute to array
                        $attributes_list[] = [
                            'id' => $attribute['name'],
                            'title' => $attribute['name']
                        ];

                    }

                }

            }

            // Return the array
            return $attributes_list;

        } catch (ClientException $e) {
            // Handle HTTP client errors (401, 403, etc.)
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : null;
            $errorMessage = 'API Error';

            if ($statusCode === 401) {
                $errorMessage = 'API Key is not enabled or has been deleted on Brevo';
                
                // Try to get the error message from the response
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
            }

            // Return error message in the expected format
            if ($attribute_name) {
                return [
                    'id' => '',
                    'title' => 'Error: ' . $errorMessage,
                ];
            } else {
                return [
                    [
                        'id' => '',
                        'title' => 'Error: ' . $errorMessage,
                    ]
                ];
            }

        } catch (\Exception $e) {
            // Handle any other exceptions
            $errorMessage = 'Unable to fetch attributes from Brevo';
            
            // Check if the exception message contains useful error info
            $message = $e->getMessage();
            if (str_contains($message, '401') || str_contains($message, 'unauthorized') || str_contains($message, 'API Key is not enabled')) {
                $errorMessage = 'API Key is not enabled or has been deleted on Brevo';
                
                // Try to extract error message from JSON in exception message
                if (preg_match('/"message":"([^"]+)"/', $message, $matches)) {
                    $errorMessage = $matches[1];
                }
            }

            // Return error message in the expected format
            if ($attribute_name) {
                return [
                    'id' => '',
                    'title' => 'Error: ' . $errorMessage,
                ];
            } else {
                return [
                    [
                        'id' => '',
                        'title' => 'Error: ' . $errorMessage,
                    ]
                ];
            }
        }
    }

    /**
     * Add Subscriber to Brevo
     *
     * @param array $config
     * @param object $submission_data
     * @return array
     */
    public function addSubscriber(array $config, object $submission_data)
    {
        // Skip processing if $config is empty or marketing opt-in is not accepted
        if (empty($config) || !$this->checkMarketingOptin($config, $submission_data)) {
            return ['submission' => $submission_data];
        }

        // Initialise subscriber data
        $this->subscriber_data['email'] = $submission_data->get($config['email_field']);

        // Map name if name_field is configured
        if (!empty($config['name_field'])) {
            $this->doMapFields('FIRSTNAME', $config['name_field'], $submission_data->toArray(), ' ');
        }

        // Map additional fields
        $this->mapAdditionalFields($config, $submission_data);

        // Automatically split name if enabled and last_name is not mapped
        if (Arr::get($config, 'auto_split_name', true) && !$this->last_name_field_exists) {
            $this->splitName();
        }

        // Finalize subscriber data
        $this->finalizeSubscriberData($config);

        // Send data to Brevo
        $this->sendToBrevo();

        return ['submission' => $submission_data];
    }

    /**
     * Map additional fields from the configuration.
     *
     * @param array $config
     * @param object $submission_data
     */
    protected function mapAdditionalFields(array $config, object $submission_data)
    {
        $mapped_fields = Arr::get($config, 'mapped_fields', []);
        collect($mapped_fields)->each(function ($item) use ($submission_data) {
            if (!empty($item['mapped_form_fields'])) {
                if ($item['list_attribute'] === 'LASTNAME') {
                    $this->last_name_field_exists = true;
                }
                $this->doMapFields($item['list_attribute'], $item['mapped_form_fields'], $submission_data->toArray());
            }
        });
    }

    /**
     * Split the name into first and last names if applicable.
     */
    protected function splitName()
    {
        $name = $this->subscriber_data_attributes['FIRSTNAME'] ?? '';
        [$first_name, $last_name] = explode(' ', $name, 2) + ['', ''];
        $this->subscriber_data_attributes['FIRSTNAME'] = $first_name;
        $this->subscriber_data_attributes['LASTNAME'] = $last_name;
    }

    /**
     * Finalize subscriber data before sending it to Brevo.
     *
     * @param array $config
     */
    protected function finalizeSubscriberData(array $config)
    {
        $this->subscriber_data['attributes'] = (object) $this->subscriber_data_attributes;
        $this->subscriber_data['updateEnabled'] = true;
        $this->subscriber_data['listIds'] = [$config['list_id']];
    }

    /**
     * Send subscriber data to Brevo.
     */
    protected function sendToBrevo()
    {
        $response = $this->brevo_contacts->createContact($this->subscriber_data);

        if (!is_null($response) && property_exists($response, 'code') && $response->code == '400') {
            \Log::error("Brevo - " . $response->error->message);
        }
    }


    /**
     * Are there any Marketing Opt-in fields setup and have they been accepted?
     *
     * @param array $config
     * @param object $submission_data
     *
     * @return bool
     */
    private function checkMarketingOptin(array $config, object $submission_data)
    {
        $marketing_optin_field = Arr::get($config, 'marketing_optin_field');

        if ($marketing_optin_field) {
            // Check if the field exists in submission data and if it's ticked
            $optin_value = Arr::get($submission_data, $marketing_optin_field);
            if (!empty($optin_value)) {
                return true; // Opt-in is ticked
            } else {
                return false; // Opt-in is not ticked
            }
        } else {
            // If marketing_optin_field is not set in config, return true
            return true;
        }
    }

    /**
     * Combine multiple mapped fields
     *
     * @param $formset_name string
     *
     * @return mixed
     */
    private function getFormConfiguration(string $formset_name)
    {
        return collect($this->getConfig('forms'))->first(function ($ignored, $data) use ($formset_name) {
            return $formset_name == Arr::get($data, 'form');
        });
    }

    /**
     * Map the fields ready for payload sent to Brevo
     *
     * @param $field_name string
     * @param $field_mapped_name string
     * @param $submission_data array
     * @param $separator string
     *
     */
    private function doMapFields(string $field_name, string $field_mapped_name, array $submission_data, string $separator = ", ")
    {
        $field_data = array();

        if (array_key_exists($field_mapped_name, $submission_data)) { // Check if the array key exists
            $field_data[] = $submission_data[$field_mapped_name];
        }

        $field_data = implode($separator, $field_data);
        $this->subscriber_data_attributes[$field_name] = $field_data;
    }
}
