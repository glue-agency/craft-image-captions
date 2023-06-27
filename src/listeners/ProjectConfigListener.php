<?php

namespace GlueAgency\ImageCaption\listeners;

use Carbon\Carbon;
use craft\db\Query;
use craft\events\RebuildConfigEvent;
use craft\helpers\Db;
use craft\services\ProjectConfig;
use GlueAgency\ImageCaption\enums\Table;
use yii\base\Event;

class ProjectConfigListener
{

    public function __construct()
    {
        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, [$this, 'handleRebuild']);
    }

    public function handleRebuild(RebuildConfigEvent $event): void
    {
        $event->config['glueAgency']['imageCaptions'] = [
            'providers'      => $this->buildProviders(),
            'configurations' => $this->buildConfigurations(),
        ];
    }

    protected function buildProviders(): array
    {
        $providers = (new Query())
            ->select('*')
            ->from(Table::PROVIDERS->value)
            ->all();

        $config = [];

        foreach($providers as $provider) {
            $config[$provider['uid']] = [
                'name'        => $provider['name'],
                'handle'      => $provider['handle'],
                'class'       => $provider['class'],
                'settings'    => $provider['settings'],
                'dateUpdated' => Db::prepareDateForDb(Carbon::now()),
                'dateCreated' => Db::prepareDateForDb(Carbon::now()),
                'uid'         => $provider['uid'],
            ];
        }

        return $config;
    }

    protected function buildConfigurations(): array
    {
        $configurations = (new Query())
            ->select('*')
            ->from(Table::CONFIGURATIONS->value)
            ->all();

        $config = [];

        foreach($configurations as $configuration) {
            $config[$configuration['uid']] = [
                'volumeHandle'   => $configuration['volumeHandle'],
                'providerHandle' => $configuration['providerHandle'],
                'dateUpdated'    => Db::prepareDateForDb(Carbon::now()),
                'dateCreated'    => Db::prepareDateForDb(Carbon::now()),
                'uid'            => $configuration['uid'],
            ];
        }

        return $config;
    }
}
