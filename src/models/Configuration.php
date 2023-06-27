<?php

namespace GlueAgency\ImageCaption\models;

use Craft;
use craft\base\Model;
use craft\models\Volume;
use GlueAgency\ImageCaption\ImageCaption;

class Configuration extends Model
{

    public $volumeHandle;

    public $providerHandle;

    public $dateUpdated;

    public $dateCreated;

    public $uid;

    public function getVolume(): Volume
    {
        return Craft::$app->volumes->getVolumeByHandle($this->volumeHandle);
    }

    public function getProvider(): Provider
    {
        return ImageCaption::getInstance()->provider->byHandle($this->providerHandle);
    }
}
