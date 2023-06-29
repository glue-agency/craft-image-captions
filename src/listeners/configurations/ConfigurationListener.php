<?php

namespace GlueAgency\ImageCaption\listeners\configurations;

use Carbon\Carbon;
use Craft;
use craft\events\ConfigEvent;
use craft\helpers\Db;
use GlueAgency\ImageCaption\enums\ProjectConfig as ProjectConfigEnum;
use GlueAgency\ImageCaption\enums\Table;
use GlueAgency\ImageCaption\records\Configuration;

class ConfigurationListener
{

    public function __construct()
    {
        Craft::$app->projectConfig
            ->onAdd(ProjectConfigEnum::CONFIGURATIONS->value . '.{uid}', [$this, 'handleChange'])
            ->onUpdate(ProjectConfigEnum::CONFIGURATIONS->value . '.{uid}', [$this, 'handleChange'])
            ->onRemove(ProjectConfigEnum::CONFIGURATIONS->value . '.{uid}', [$this, 'handleRemove']);
    }

    public function handleChange(ConfigEvent $event): void
    {
        $uid = $event->tokenMatches[0];

        $payload = [
            'uid'            => $uid,
            'volumeHandle'   => $event->newValue['volumeHandle'],
            'providerHandle' => $event->newValue['providerHandle'],
            'dateUpdated'    => Db::prepareDateForDb(Carbon::now()),
        ];

        if (! Configuration::find()->where(['=', 'uid', $uid])->exists()) {
            $payload['dateCreated'] = Db::prepareDateForDb(Carbon::now());

            Craft::$app->db->createCommand()
                ->insert(Table::CONFIGURATIONS->value, $payload)
                ->execute();

            return;
        }

        Craft::$app->db->createCommand()
            ->update(Table::CONFIGURATIONS->value, $payload, ['uid' => $uid])
            ->execute();
    }

    public function handleRemove(ConfigEvent $event): void
    {
        $uid = $event->tokenMatches[0];

        if (! $uid) {
            return;
        }

        Craft::$app->db->createCommand()
            ->delete(Table::CONFIGURATIONS->value, ['uid' => $uid])
            ->execute();
    }
}
