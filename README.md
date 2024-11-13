# DbpRelayVeritasBundle

[GitHub](https://gitlab.tugraz.at/398EE57581B44C9A/dbp-relay-verity) |
[Packagist](https://packagist.org/packages/dbp/relay-verity-bundle)

The verity bundle provides an API for interacting with objects to validate (e.g. a PDF to validate against PDF/A-1b).

## Bundle installation

You can install the bundle directly from [packagist.org](https://packagist.org/packages/dbp/relay-verity-bundle).

```bash
composer require dbp/relay-verity-bundle
```

## Integration into the Relay API Server

* Add the bundle to your `config/bundles.php` in front of `DbpRelayCoreBundle`:

```php
...
Dbp\Relay\VerityBundle\DbpRelayVerityBundle::class => ['all' => true],
Dbp\Relay\CoreBundle\DbpRelayCoreBundle::class => ['all' => true],
];
```

If you were using the [DBP API Server Template](https://packagist.org/packages/dbp/relay-server-template)
as template for your Symfony application, then this should have already been generated for you.

* Run `composer install` to clear caches

## Configuration

For this create `config/packages/dbp_relay_verity.yaml` in the app with the following
content:

```yaml
dbp_relay_verity:
  backends:
    pdfa:
      url: '%env(VERA_PDF_URI)%'
      validator: 'Dbp\Relay\VerityBundle\Service\PDFAValidationAPI'
      maxsize: 33554432
    
  profiles:
    archive:
      name: 'Check PDFs for archiving complacency'
      rule: 'pdfa.validity == true && pdfa_b2.validity == true'
      checks:
        pdfa:
          backend: 'pdfa'
          flavour: 'auto'
        pdfa_b2:
          backend: 'pdfa'
          flavour: 'b2'
```

There are two sections in this bundle configuration:

1. `backends` for the configuration of backend services
2. `profiles` for the checks available via the API

In this example, a **backend** with the name `pdfa` is implemented by the PHP class `PDFAValidationAPI` with settings for the external `url` and the maximum allowed file size `maxsize` for files (binary, not base64).
There is also a **profile** defines with the name `archive`. A profile performs all `checks` and stores the results (the `validity` and also `errors`) in a variable named like the **check**, here `pdfa` and `pdfa_b2`. Each check has the name of its `backend` to use and a `flavour` to set the type of check to perform. 
The results of all checks are then evaluated by the `rule` of the profile. The syntax of the rule is almost PHP syntax, but before any function is available, they must be registered first!

If you were using the [DBP API Server Template](https://packagist.org/packages/dbp/relay-server-template)
as template for your Symfony application, then the configuration file should have already been generated for you.

For more info on bundle configuration see <https://symfony.com/doc/current/bundles/configuration.html>.

## Development & Testing

* Install dependencies: `composer install`
* Run tests: `composer test`
* Run linters: `composer run lint`
* Run cs-fixer: `composer run cs-fix`

## Bundle dependencies

Don't forget you need to pull down your dependencies in your main application if you are installing packages in a bundle.

```bash
# updates and installs dependencies of dbp/relay-verity-bundle
composer update dbp/relay-verity-bundle
```

## Scripts

## Error codes

### `/verity/reports`

#### POST

| relay:errorId                | Status code | Description                                     | relay:errorDetails | Example                          |
|------------------------------|-------------|-------------------------------------------------|--------------------|----------------------------------|
| `verity:report-not-created`  | 500         | The submission could not be created.            | `message`          | `['message' => 'Error message']` |
| `verity:report-invalid-json` | 422         | The dataFeedElement doesn't contain valid json. | `message`          |                                  |

### `/verity/reports/{identifier}`

#### GET

| relay:errorId             | Status code | Description               | relay:errorDetails | Example |
|---------------------------|-------------|---------------------------|--------------------|---------|
| `verity:report-not-found` | 404         | Submission was not found. |                    |         |

## Roles

This bundle needs the role `ROLE_SCOPE_VALIDATION` assigned to the user to get permissions to fetch data.
To create a new submission entry the Symfony role `ROLE_SCOPE_VALIDATION-POST` is required.

## Events

To extend the behavior of the bundle the following event is registered:

### VerityRequestEvent

This event allows you to send a request for validation internally.
See `tests/EventSubscriberTest.php` as an example.

### VerityEvent

This event allows you to get notifications for (all) verity reports.

An event subscriber receives a `Dbp\Relay\VerityBundle\Event\VerityEvent` instance:

```php
<?php

namespace App\EventSubscriber;

use Dbp\Relay\VerityBundle\Event\VerityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class VerityEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            VeritasEvent::NAME => 'onVerity',
        ];
    }

    public function onVerity(VeritasEvent $event)
    {
        $report = $event->getReport();

        // TODO: extract relevant information
        $valid = $report->isValid();
    }
}
```
