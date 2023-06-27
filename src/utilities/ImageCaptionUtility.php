<?php

namespace GlueAgency\ImageCaption\utilities;

use Craft;
use craft\base\Utility;
use GlueAgency\ImageCaption\ImageCaption;

class ImageCaptionUtility extends Utility
{

    public static function displayName(): string
    {
        return Craft::t('image-captions', 'Image Captions');
    }

    public static function id(): string
    {
        return 'image-captions';
    }

    public static function iconPath(): ?string
    {
        return ImageCaption::getInstance()->getBasePath() . DIRECTORY_SEPARATOR . 'icon-mask.svg';
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('image-captions/utility/index', [
            'volumes' => Craft::$app->volumes->getAllVolumes(),
            'configurations' => ImageCaption::getInstance()->configuration->getAllVolumeHandles(),
        ]);
    }
}
