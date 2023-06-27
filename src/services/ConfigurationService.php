<?php

namespace GlueAgency\ImageCaption\services;

use Craft;
use craft\db\Query;
use craft\models\Volume;
use GlueAgency\ImageCaption\enums\ProjectConfig;
use GlueAgency\ImageCaption\enums\Table;
use GlueAgency\ImageCaption\events\configurations\DeleteConfigurationEvent;
use GlueAgency\ImageCaption\events\configurations\SaveConfigurationEvent;
use GlueAgency\ImageCaption\models\Configuration;
use yii\base\Component;

class ConfigurationService extends Component
{
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';

    public function getAll(): array
    {
        $configurations = [];

        foreach($this->getQuery()->all() as $item) {
            $configurations[] = new Configuration($item);
        }

        return $configurations;
    }

    public function getAllVolumeHandles(): array
    {
        return array_map(function($configuration) {
            return $configuration->volumeHandle;
        }, $this->getAll());
    }

    public function getAllProviderHandles(): array
    {
        return array_map(function($configuration) {
            return $configuration->providerHandle;
        }, $this->getAll());
    }

    public function getById(string|int $configurationId): ?Configuration
    {
        if($data = $this->getQuery()->where(['id' => $configurationId])->one()) {
            return new Configuration($data);
        }

        return null;
    }

    public function getByVolumeHandle(string $volumeHandle): ?Configuration
    {
        if($data = $this->getQuery()->where(['volumeHandle' => $volumeHandle])->one()) {
            return new Configuration($data);
        }

        return null;
    }

    public function findByVolumeHandles(array $volumeHandles): array
    {
        $data = $this->getQuery()->where(['IN', 'volumeHandle', $volumeHandles])->all();

        if(! empty($data)) {
            return array_map(function($item) {
                return new Configuration($item);
            }, $data);
        }

        return [];
    }

    public function getByProviderHandle(string $providerHandle): ?Configuration
    {
        if($data = $this->getQuery()->where(['providerHandle' => $providerHandle])->one()) {
            return new Configuration($data);
        }

        return null;
    }

    public function findByProviderHandles(array $providerHandles): array
    {
        $data = $this->getQuery()->where(['IN', 'providerHandle', $providerHandles])->all();

        if(! empty($data)) {
            return array_map(function($item) {
                return new Configuration($item);
            }, $data);
        }

        return [];
    }

    public function sync(array $configurations): bool
    {
        // Get the exising configurations
        $existingConfigurations = $this->getAll();

        // Create new configurations and update existing
        foreach($configurations as $configuration) {
            if(! $configuration->validate()) {
                return false;
            }

            if($this->hasEventHandlers(self::EVENT_BEFORE_SAVE)) {
                $this->trigger(self::EVENT_BEFORE_SAVE, $event = new SaveConfigurationEvent($configuration));

                if(! $event->isValid) {
                    return false;
                }
            }

            Craft::$app->projectConfig
                ->set(
                    ProjectConfig::implode([ProjectConfig::CONFIGURATIONS->value, $configuration->uid]),
                    [
                        'uid'            => $configuration->uid,
                        'volumeHandle'   => $configuration->volumeHandle,
                        'providerHandle' => $configuration->providerHandle,
                    ]
                );

            if($this->hasEventHandlers(self::EVENT_AFTER_SAVE)) {
                $this->trigger(self::EVENT_AFTER_SAVE, new SaveConfigurationEvent($configuration));
            }
        }

        // Compare $existingConfigurations with the newly posted
        // $configurations and determine which are missing
        $missingConfigurations = array_filter($existingConfigurations, function($existing) use ($configurations) {
            return ! in_array($existing->uid,  array_map(function($configuration) { return $configuration->uid; }, $configurations));
        });

        // Delete missing configurations
        foreach($missingConfigurations as $missingConfiguration) {
            if(! $this->delete($missingConfiguration)) {
                return false;
            }
        }

        return true;
    }

    public function deleteById($configurationId): bool
    {
        $configuration = $this->getById($configurationId);

        if(! $configuration) {
            return false;
        }

        return $this->delete($configuration);
    }

    public function delete(Configuration $configuration): bool
    {
        if($this->hasEventHandlers(self::EVENT_BEFORE_DELETE)) {
            $this->trigger(self::EVENT_BEFORE_DELETE, $event = new DeleteConfigurationEvent($configuration));

            if(! $event->isValid) {
                return false;
            }
        }

        Craft::$app->projectConfig->remove(ProjectConfig::implode([ProjectConfig::CONFIGURATIONS->value, $configuration->uid]));

        if($this->hasEventHandlers(self::EVENT_AFTER_DELETE)) {
            $this->trigger(self::EVENT_AFTER_DELETE, new DeleteConfigurationEvent($configuration));
        }

        return true;
    }

    public function getProviderHandleForVolume(Volume $volume): ?string
    {
        foreach($this->getAll() as $configuration) {
            if($volume->handle == $configuration->volumeHandle) {
                return $configuration->providerHandle;
            }
        }

        return null;
    }

    protected function getQuery()
    {
        return (new Query())
            ->select('*')
            ->from(Table::CONFIGURATIONS->value);
    }
}
