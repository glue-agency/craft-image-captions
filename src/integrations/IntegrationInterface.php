<?php

namespace GlueAgency\ImageCaption\integrations;

use craft\elements\Asset;
use GlueAgency\ImageCaption\integrations\responses\ResponseInterface;

interface IntegrationInterface
{
    public function parse(Asset $asset): ResponseInterface;
}
