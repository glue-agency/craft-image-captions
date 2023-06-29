<?php

namespace GlueAgency\ImageCaption\models;

use craft\base\Model;
use craft\validators\UniqueValidator;
use GlueAgency\ImageCaption\records\Provider as ProviderRecord;
use GlueAgency\ImageCaption\validators\IntegrationSettingsRequiredValidator;
use GlueAgency\ImageCaption\validators\IntegrationSettingsValidator;

class Provider extends Model
{

    public $id;

    public $name;

    public $handle;

    public $class;

    protected $settings;

    public $dateUpdated;

    public $dateCreated;

    public $uid;

    protected $_settingsAsArray;

    public function getSetting($key): ?string
    {
        $settings = $this->getSettings();

        if(in_array($key, array_keys($settings))) {
            return $settings[$key];
        }

        return null;
    }

    public function getSettings(): array
    {
        if($this->settings === null) {
            return [];
        }

        if(! $this->_settingsAsArray) {
            $this->_settingsAsArray = json_decode($this->settings, true);
        }

        return $this->_settingsAsArray;
    }

    public function setSettings(string|array $value): self
    {
        $this->settings = is_array($value) ? json_encode($value) : $value;

        // Clear the cached settings array when updating the stored settings.
        $this->_settingsAsArray = null;

        return $this;
    }

    protected function defineRules(): array
    {
        $rules = [];

        $rules[] = [['name', 'handle', 'class'], 'required'];
        $rules[] = [['handle'], UniqueValidator::class, 'targetClass' => ProviderRecord::class];
        $rules[] = [['settings'], IntegrationSettingsRequiredValidator::class];

        return $rules;
    }
}
