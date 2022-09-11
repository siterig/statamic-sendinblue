<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sendinblue API Key
    |--------------------------------------------------------------------------
    |
    | The API key for connecting to the Sendinblue API.
    |
    */

    'api_key' => env('SENDINBLUE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Forms
    |--------------------------------------------------------------------------
    |
    | The form settings for submissions to add to your Sendinblue list.
    |
    */

    'forms' => [
        [
            /*
            |--------------------------------------------------------------------------
            | Form
            |--------------------------------------------------------------------------
            |
            | The handle of the Statamic form to listen for submissions from.
            |
            */

            'form' => null,

            /*
            |--------------------------------------------------------------------------
            | List
            |--------------------------------------------------------------------------
            |
            | The Sendinblue list that the submission should be added to.
            |
            */

            'list_id' => null,

            /*
            |--------------------------------------------------------------------------
            | Name Field
            |--------------------------------------------------------------------------
            |
            | Optional: Select the form field to use for `name`.
            |
            */

            'name_field' => null,

            /*
            |--------------------------------------------------------------------------
            | Email Field
            |--------------------------------------------------------------------------
            |
            | Select the form field to use for `name`.
            |
            */

            'email_field' => null,

            /*
            |--------------------------------------------------------------------------
            | Automatically Split Name
            |--------------------------------------------------------------------------
            |
            | Split into `FIRSTNAME` and `LASTNAME` on Sendinblue. This setting is ignored
            | if you map `LASTNAME` separately.
            |
            */

            'auto_split_name' => true,

            /*
            |--------------------------------------------------------------------------
            | Opt-in Field
            |--------------------------------------------------------------------------
            |
            | Optional: This field should be an un-ticked checkbox that conforms to
            | regulations in your market (e.g. GDPR/ePrivacy)
            |
            */

            'marketing_optin_field' => null,

            /*
            |--------------------------------------------------------------------------
            | Mapped Fields
            |--------------------------------------------------------------------------
            |
            | Optional: Add additional fields that you would like to map here
            |
            */

            'mapped_fields' => [
                [
                    /*
                    |--------------------------------------------------------------------------
                    | Sendinblue Attribute
                    |--------------------------------------------------------------------------
                    |
                    | The attribute on your Sendinblue Contact list
                    |
                    */

                    'list_attribute' => null,

                    /*
                    |--------------------------------------------------------------------------
                    | Form Field
                    |--------------------------------------------------------------------------
                    |
                    | the form field handle to map to
                    |
                    */

                    'mapped_form_fields' => null,
                ],
            ],
        ],
    ],

];
