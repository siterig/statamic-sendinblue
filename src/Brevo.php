<?php

namespace SiteRig\Brevo;

use Illuminate\Support\Facades\Log;
use GuzzleHttp;
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
                    'id' => 0,
                    'title' => 'Error: ' . $lists['message'],
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
    }

    /**
     * Add Subscriber to Brevo
     *
     * @param array $config
     * @param object $submission
     *
     * @return array
     */
    public function addSubscriber(array $config, object $submission_data)
    {
        // Check if marketing permissions were accepted (returns true if not in use)
        if ($this->checkMarketingOptin($config, $submission_data)) {

            // Set data email field
            $this->subscriber_data['email'] = $submission_data->get($config['email_field']);

            if (!empty($config['name_field'])) { // Check if name_field is set
                $this->doMapFields('FIRSTNAME', $config['name_field'], $submission_data->toArray(), ' ');
            }

            // Check for mapped fields
            if ($mapped_fields = Arr::get($config, 'mapped_fields')) {

                // Loop through mapped fields
                collect($mapped_fields)->map(function ($item, $key) use ($submission_data) {

                    // In case there is no mapped form field
                    if (!empty($item["mapped_form_fields"])) {

                        // Check if mapped fields contain last_name
                        if ($item['list_attribute'] == 'LASTNAME') {
                            $this->last_name_field_exists = true;
                        }

                        $this->doMapFields($item['list_attribute'], $item["mapped_form_fields"], $submission_data->toArray());

                    }

                });

            }

            // Check if Automatic Name Split is configured
            if (Arr::get($config, 'auto_split_name', true)) {

                // If there is no last_name field mapped
                if ($this->last_name_field_exists === false) {
                    // Split name by first space character
                    $name_array = explode(' ', $this->subscriber_data_attributes['FIRSTNAME'], 2);

                    // Set data
                    $this->subscriber_data_attributes['FIRSTNAME'] = $name_array[0];
                    $this->subscriber_data_attributes['LASTNAME'] = $name_array[1] ?? '';
                }

            }

            // Set attributes
            $this->subscriber_data['attributes'] = (object) $this->subscriber_data_attributes;

            // Set updates to true
            $this->subscriber_data['updateEnabled'] = true;

            // Set list id
            $this->subscriber_data['listIds'] = [$config['list_id']];

            // send to Brevo
            $response = $this->brevo_contacts->createContact($this->subscriber_data);

            // Check response for errors
            if (!is_null($response) && property_exists($response, 'code') && $response->code == '400') {

                // Generate error to the log
                \Log::error("Brevo - " . $response->error->message);

            }

        }

        // Return the submission
        return [
            'submission' => $submission_data
        ];
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
