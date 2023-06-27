<?php

namespace GlueAgency\ImageCaption\services;

use GlueAgency\ImageCaption\events\integrations\RegisterIntegrationsEvent;
use GlueAgency\ImageCaption\integrations\AltTextAi;
use GlueAgency\ImageCaption\integrations\IntegrationInterface;
use GlueAgency\ImageCaption\models\Provider;
use yii\base\Component;

class IntegrationService extends Component
{

    const EVENT_REGISTER = 'register';

    public array $integrations;

    public function init()
    {
        parent::init();

        $event = new RegisterIntegrationsEvent([
            'integrations' => [
                'altTextAI' => AltTextAi::class,
            ]
        ]);

        $this->trigger(self::EVENT_REGISTER, $event);

        $this->integrations = $event->integrations;
    }

    public function getAll(): array
    {
        return array_map(function($class) {
            return new $class;
        }, $this->integrations);
    }

    public function buildFromProvider(Provider $provider): IntegrationInterface
    {
        $integration = new $provider->class;
        $integration->setSettings($provider->getSettings());

        return $integration;
    }
}
