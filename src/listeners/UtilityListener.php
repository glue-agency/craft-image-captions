<?php

namespace GlueAgency\ImageCaption\listeners;

use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use GlueAgency\ImageCaption\utilities\ImageCaptionUtility;
use yii\base\Event;

class UtilityListener
{

    public function __construct()
    {
        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, [$this, 'registerUtility']);
    }

    public function registerUtility(RegisterComponentTypesEvent $event): void
    {
        $event->types[] = ImageCaptionUtility::class;
    }
}
