<?php

namespace GlueAgency\ImageCaption\listeners;

use craft\web\twig\variables\CraftVariable;
use GlueAgency\ImageCaption\variables\ImageCaptionVariable;
use yii\base\Event;

class CraftListener
{

    public function __construct()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, [$this, 'registerTwigVariables']);
    }

    public function registerTwigVariables(Event $event): void
    {
        $event->sender->set('imageCaption', ImageCaptionVariable::class);
    }
}
