<?php

namespace GlueAgency\ImageCaption\listeners;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

class UrlRulesListener
{

    public function __construct()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, [$this, 'registerCpRoutes']);
    }

    public function registerCpRoutes(RegisterUrlRulesEvent $event): void
    {
        $event->rules['image-captions/providers'] = 'image-captions/providers/index';
        $event->rules['image-captions/providers/new'] = 'image-captions/providers/new';
        $event->rules['image-captions/providers/save'] = 'image-captions/providers/save';
        $event->rules['image-captions/providers/<providerId:\d+>'] = 'image-captions/providers/edit';
        $event->rules['image-captions/providers/delete'] = 'image-captions/providers/delete';

        $event->rules['image-captions/configurations'] = 'image-captions/configurations/index';
        $event->rules['image-captions/configurations/save'] = 'image-captions/configurations/save';

        $event->rules['image-captions/assets/index'] = 'image-captions/assets/index';

        $event->rules['image-captions/utilities/index'] = 'image-captions/utilities/index';
    }
}
