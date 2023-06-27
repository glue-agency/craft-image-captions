<?php

namespace GlueAgency\ImageCaption\events\integrations;

use craft\events\CancelableEvent;

class RegisterIntegrationsEvent extends CancelableEvent
{

    public array $integrations = [];
}
