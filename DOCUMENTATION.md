# Brevo for Statamic Documentation

## Setup

### Install the add-on

The recommended way to install this add-on is via the Statamic Marketplace or in the Control Panel in Statamic. Alternatively you can use composer in your project root:

```
  composer require siterig/sendinblue
```

Statamic will automatically register the add-on.


### Add your Brevo API key to the `.env` file

In your `.env` file add a new line with your Brevo API key.

```
  BREVO_API_KEY=your-key-goes-here
```


### Create a form in Statamic

Your form only needs an email field as an absolute minimum, but our recommended form setup is:

- Name
- Email
- Marketing Opt-in


### Brevo settings

For each Statamic Form that you want to connect with Brevo you can add a form entry in the Brevo settings.

#### Brevo API Key

This is a read-only field that displays your current API key that is set in the `.env` file.


#### Form

Select the Statamic Form you would like to capture submissions from.


#### List

Select the list you would like to add the contact to.


#### Name Field

This is the field you want to use to capture someones name, by default the add-on will split this name by the first space character into first name and last name to be sent to your Brevo list. You can disable Automatic Name Splitting using the setting listed below or by mapping an additional field to Brevo's `LASTNAME` attribute.


#### Email Field

Along with a List Id, this is the only attribute that is required by Brevo on a submission. We don't do anything special with this field so you'll need to make sure you have validation setup in Statamic and/or your front-end code if required.


#### Automatically Split Name

When enabled this splits the Name Field into first name and last name using the first space character it finds. This setting is ignored if you map a seperate field to `LASTNAME`.


#### Opt-in Field

This should ideally be an un-ticked checkbox that conforms to data protection regulations in your region. If the user does not tick this checkbox the submission to Statamic will still go through but the details will not be sent to Brevo.


#### Mapped Fields

This is where you can map any additional Brevo attributes such as `LASTNAME` or `SMS` as well as any custom attributes you've created.


