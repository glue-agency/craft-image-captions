<?php

namespace GlueAgency\ImageCaption\events\providers;

use craft\events\CancelableEvent;
use GlueAgency\ImageCaption\models\Provider;

class SaveProviderEvent extends CancelableEvent
{

    protected Provider $provider;

    protected bool $isNew;

    public function __construct(Provider $provider, bool $isNew)
    {
        $this->provider = $provider;
        $this->isNew = $isNew;

        parent::__construct();
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }
}
