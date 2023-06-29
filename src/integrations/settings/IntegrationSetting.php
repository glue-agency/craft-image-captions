<?php

namespace GlueAgency\ImageCaption\integrations\settings;

class IntegrationSetting
{

    const TEXT = 'text';
    const PASSWORD = 'password';
    const BOOL = 'bool';
    const AUTOCOMPLETE = 'autocomplete';

    public string $type;

    public string $label;

    public string $handle;

    public ?string $instructions;

    public bool $required;

    public ?string $default;

    public function __construct(string $type, string $label, string $handle, string $instructions = null, bool $required = false, string $default = null)
    {
        $this->type = $type;
        $this->label = $label;
        $this->handle = $handle;
        $this->instructions = $instructions;
        $this->required = $required;
        $this->default = $default;
    }
}
