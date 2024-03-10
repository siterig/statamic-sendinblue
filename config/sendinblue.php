<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brevo API Key
    |--------------------------------------------------------------------------
    |
    | The API key for connecting to the Brevo API.
    |
    */

    'api_key' => env('BREVO_API_KEY', env('SENDINBLUE_API_KEY')),

    /*
    |--------------------------------------------------------------------------
    | Forms
    |--------------------------------------------------------------------------
    |
    | The form settings for submissions to add to your Brevo list.
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
            | The Brevo list that the submission should be added to.
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
            | Split into `FIRSTNAME` and `LASTNAME` on Brevo. This setting is ignored
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
                    | Brevo Attribute
                    |--------------------------------------------------------------------------
                    |
                    | The attribute on your Brevo Contact list
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
