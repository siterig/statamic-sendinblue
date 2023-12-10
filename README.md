# Sendinblue for Statamic [![Latest Version](https://img.shields.io/github/release/siterig/statamic-sendinblue.svg?style=flat-square)](https://github.com/siterig/statamic-sendinblue/releases)

Sendinblue for Statamic lets you subscribe contact form submissions to your Sendinblue lists.

You can contacts to your lists, automatically split single name fields into FIRSTNAME and LASTNAME attributes, use an opt-in field, collect GDPR compliant marketing preferences and of course map any custom fields that you like.

This is not an official add-on by Sendinblue and as such support requests should be submitted [here](https://rockandscissor.atlassian.net/servicedesk/customer/portal/2) on our support centre.

This addon uses [Forma](https://statamic.com/addons/silentz/forma) by Erin Dalzell and will be automatically installed for you.


## Documentation

Read it on the [Statamic Marketplace](https://statamic.com/addons/siterig/sendinblue/docs) or contribute to it [here on GitHub](DOCUMENTATION.md).


## Requirements

* PHP 8.2 or higher
* Laravel 9 or 10
* Statamic v4.0 or higher


## Installation

You should install via the Statamic Marketplace at [https://statamic.com/addons/siterig/sendinblue](https://statamic.com/addons/siterig/sendinblue) or you can use composer in your project root:

```
  composer require siterig/sendinblue
```

Set your Sendinblue API key in the `.env` file within your project:

```
  SENDINBLUE_API_KEY=your-api-key-goes-here
```

Then all that's left to do is publish the config file to `config/sendinblue.php`:

```
  php artisan vendor:publish --tag="sendinblue-config"
```

Now you can configure your form settings within the Control Panel from the Sendinblue menu option.


## Developers

Matt Stone, Craig Bowler, Jamie McGrory


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Contributing

See our [contributing guide](CONTRIBUTING.md) for more information.


## License

This is commercial software. You may use the package for your sites. Each site requires it's own license.
