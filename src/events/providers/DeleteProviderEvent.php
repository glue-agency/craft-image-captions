<?php

namespace GlueAgency\ImageCaption\events\providers;

use craft\events\CancelableEvent;
use GlueAgency\ImageCaption\models\Provider;

class DeleteProviderEvent extends CancelableEvent
{

    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;

        parent::__construct();
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
