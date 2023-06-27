<?php

namespace GlueAgency\ImageCaption\integrations;

use craft\helpers\App;

abstract class AbstractIntegration
{

    public string $name = 'Integration Name';

    protected array $settings = [];

    public function defineSettings(): array
    {
        return [];
    }

    public function getSetting(string $handle): mixed
    {
        if(in_array($handle, array_keys($this->settings))) {
            $value = $this->settings[$handle];

            return App::parseEnv($value);
        }

        return null;
    }

    public function setSettings(array $settings): self
    {
        foreach($settings as $handle => $value) {
            $this->setSetting($handle, $value);
        }

        return $this;
    }

    public function setSetting(string $handle, mixed $value): self
    {
        $this->settings[$handle] = $value;

        return $this;
    }
}
