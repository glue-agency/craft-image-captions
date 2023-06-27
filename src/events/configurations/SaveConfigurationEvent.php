<?php

namespace GlueAgency\ImageCaption\events\configurations;

use craft\events\CancelableEvent;
use GlueAgency\ImageCaption\models\Configuration;

class SaveConfigurationEvent extends CancelableEvent
{

    protected Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        parent::__construct();
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
