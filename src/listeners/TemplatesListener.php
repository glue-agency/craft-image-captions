<?php

namespace GlueAgency\ImageCaption\listeners;

use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use yii\base\Event;

class TemplatesListener
{

    public function __construct()
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, [$this, 'registerCpTemplates']);
    }

    public function registerCpTemplates(RegisterTemplateRootsEvent $event): void
    {
        $event->roots['image-captions'] = CRAFT_VENDOR_PATH . '/glue-agency/craft-image-captions/src/templates';
    }
}
