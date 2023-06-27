<?php

namespace GlueAgency\ImageCaption\listeners\assets;

use Craft;
use craft\base\Element;
use craft\base\Event;
use craft\elements\Asset;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
use craft\events\RegisterElementActionsEvent;
use craft\events\RegisterElementHtmlAttributesEvent;
use craft\helpers\ElementHelper;
use craft\helpers\Html;
use craft\helpers\Queue;
use craft\helpers\UrlHelper;
use GlueAgency\ImageCaption\elements\actions\ParseAction;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\queue\jobs\GenerateImageCaptionJob;

class AssetListener
{

    public function __construct()
    {
        Event::on(Asset::class, Element::EVENT_AFTER_SAVE, [$this, 'handleAfterSave']);
        Event::on(Asset::class, Element::EVENT_REGISTER_HTML_ATTRIBUTES, [$this, 'registerAdditionalAssetTableAttributes']);
        Event::on(Asset::class, Element::EVENT_DEFINE_ADDITIONAL_BUTTONS, [$this, 'registerAdditionalAssetButtons']);
        Event::on(Asset::class, Element::EVENT_REGISTER_ACTIONS, [$this, 'registerAssetActions']);
    }

    public function handleAfterSave(ModelEvent $event): void
    {
        $asset = $event->sender;

        if (ElementHelper::isDraftOrRevision($asset)) {
            return;
        }

        if(! $event->isNew) {
            return;
        }

        if(ImageCaption::getInstance()->asset->hasConfiguration($asset)) {
            Queue::push(new GenerateImageCaptionJob([
                'asset' => $asset
            ]));
        }
    }

    public function registerAdditionalAssetTableAttributes(RegisterElementHtmlAttributesEvent $event): void
    {
        if(ImageCaption::getInstance()->asset->hasConfiguration($event->sender)) {
            $event->htmlAttributes['data-image-captionable'] = true;
        }
    }

    public function registerAdditionalAssetButtons(DefineHtmlEvent $event): void
    {
        $asset = $event->sender;

        if(ImageCaption::getInstance()->asset->hasConfiguration($asset)) {
            $event->html = Html::tag('a', Craft::t('image-captions', 'Generate alt text'), [
                'class' => ['btn'],
                'href'  => UrlHelper::cpUrl('image-captions/assets/index', ['assetId' => $asset->id]),
            ]);
        }
    }

    public function registerAssetActions(RegisterElementActionsEvent $event): void
    {
        $event->actions[] = ParseAction::class;
    }
}
