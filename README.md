# Image Captions

Automatically generate image captions using multiple service providers on a per volume basis.

## Requirements

This plugin requires Craft CMS 4.4.0 or later, and PHP 8.1 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Image Captions”. Then click on the “Install” button in its modal window.

### With Composer

Open your terminal and run the following commands

```bash
# Require the plugin through composer
composer require glue-agency/craft-image-captions

# Install the plugin
php craft plugin/install image-captions
```

## Integrations

Provided out of the box

- [Alt Text AI](https://alttext.ai/)

### Add a Custom Integration

Create your custom Integration by extending the `GlueAgency\ImageCaption\integrations\AbstractIntegration` class and impelmenting the `GlueAgency\ImageCaption\integrations\IntegrationInterface` interface.

Hook the `register` event.

```php
use GlueAgency\ImageCaption\services\IntegrationService;
use GlueAgency\ImageCaption\events\integrations\RegisterIntegrationsEvent;

Event::on(IntegrationService::class, IntegrationService::EVENT_REGISTER, function(RegisterIntegrationsEvent $event) {
    $event->integrations['your-custom-integration-name'] = YourCustomIntegration::class;
});
```


