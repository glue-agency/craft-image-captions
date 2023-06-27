<?php

namespace GlueAgency\ImageCaption\services;

use Craft;
use craft\db\Query;
use craft\elements\Asset;
use craft\helpers\Db;
use GlueAgency\ImageCaption\enums\ProjectConfig;
use GlueAgency\ImageCaption\enums\Table;
use GlueAgency\ImageCaption\events\providers\DeleteProviderEvent;
use GlueAgency\ImageCaption\events\providers\SaveProviderEvent;
use GlueAgency\ImageCaption\models\Provider;
use yii\base\Component;

class ProviderService extends Component
{

    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';

    public function getAll(): array
    {
        $providers = [];

        foreach($this->getQuery()->all() as $item) {
            $providers[] = new Provider($item);
        }

        return $providers;
    }

    public function getById($providerId): ?Provider
    {
        if($data = $this->getQuery()->where(['id' => $providerId])->one()) {
            return new Provider($data);
        }

        return null;
    }

    public function getByHandle($providerHandle): ?Provider
    {
        if($data = $this->getQuery()->where(['handle' => $providerHandle])->one()) {
            return new Provider($data);
        }

        return null;
    }

    public function save(Provider $provider): bool
    {
        $isNew = ! $provider->id;

        if(! $provider->validate()) {
            return false;
        }

        if($this->hasEventHandlers(self::EVENT_BEFORE_SAVE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE, $event = new SaveProviderEvent($provider, $isNew));

            if(! $event->isValid) {
                return false;
            }
        }

        Craft::$app->projectConfig
            ->set(
                ProjectConfig::implode([ProjectConfig::PROVIDERS->value, $provider->uid]),
                [
                    'uid'         => $provider->uid,
                    'name'        => $provider->name,
                    'handle'      => $provider->handle,
                    'class'       => $provider->class,
                    'settings'    => $provider->settings,
                ]
            );

        if($isNew) {
            $provider->id = Db::idByUid(Table::PROVIDERS->value, $provider->uid);
        }

        if($this->hasEventHandlers(self::EVENT_AFTER_SAVE)) {
            $this->trigger(self::EVENT_AFTER_SAVE, new SaveProviderEvent($provider, $isNew));
        }

        return true;
    }

    public function deleteById($providerId): bool
    {
        $provider = $this->getById($providerId);

        if(! $provider) {
            return false;
        }

        if($this->hasEventHandlers(self::EVENT_BEFORE_DELETE)) {
            $this->trigger(self::EVENT_BEFORE_DELETE, $event = new DeleteProviderEvent($provider));

            if(! $event->isValid) {
                return false;
            }
        }

        Craft::$app->projectConfig->remove(ProjectConfig::implode([ProjectConfig::PROVIDERS->value, $provider->uid]));

        if($this->hasEventHandlers(self::EVENT_AFTER_DELETE)) {
            $this->trigger(self::EVENT_AFTER_DELETE, new DeleteProviderEvent($provider));
        }

        return true;
    }

    public function getConfiguredProviders(): array
    {
        $providers = [];

        foreach($this->getQuery()->all() as $item) {
            $providers[] = new Provider($item);
        }

        return $providers;
    }

    protected function getQuery()
    {
        return (new Query())
            ->select('*')
            ->from(Table::PROVIDERS->value);
    }
}
