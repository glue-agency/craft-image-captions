<?php

namespace GlueAgency\ImageCaption\variables;

use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\services\ConfigurationService;

class ImageCaptionVariable
{

    public function getConfiguration(): ConfigurationService
    {
        return ImageCaption::getInstance()->configuration;
    }
}
