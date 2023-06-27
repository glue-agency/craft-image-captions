<?php

namespace GlueAgency\ImageCaption\listeners\providers;

use Carbon\Carbon;
use Craft;
use craft\events\ConfigEvent;
use craft\helpers\Db;
use GlueAgency\ImageCaption\enums\ProjectConfig as ProjectConfigEnum;
use GlueAgency\ImageCaption\enums\Table;

class ProviderListener
{

    public function __construct()
    {
        Craft::$app->projectConfig
            ->onAdd(ProjectConfigEnum::PROVIDERS->value . '.{uid}', [$this, 'handleChange'])
            ->onUpdate(ProjectConfigEnum::PROVIDERS->value . '.{uid}', [$this, 'handleChange'])
            ->onRemove(ProjectConfigEnum::PROVIDERS->value . '.{uid}', [$this, 'handleRemove']);
    }

    public function handleChange(ConfigEvent $event): void
    {
        $uid = $event->tokenMatches[0];
        $id = Db::idByUid(Table::PROVIDERS->value, $uid);

        $payload = [
            'uid'         => $uid,
            'name'        => $event->newValue['name'],
            'handle'      => $event->newValue['handle'],
            'class'       => $event->newValue['class'],
            'settings'    => $event->newValue['settings'],
            'dateUpdated' => Db::prepareDateForDb(Carbon::now()),
        ];

        if (! $id) {
            $payload['dateCreated'] = Db::prepareDateForDb(Carbon::now());

            Craft::$app->db->createCommand()
                ->insert(Table::PROVIDERS->value, $payload)
                ->execute();

            return;
        }

        Craft::$app->db->createCommand()
            ->update(Table::PROVIDERS->value, $payload, ['id' => $id])
            ->execute();
    }

    public function handleRemove(ConfigEvent $event): void
    {
        $uid = $event->tokenMatches[0];
        $id = Db::idByUid(Table::PROVIDERS->value, $uid);

        if (! $id) {
            return;
        }

        Craft::$app->db->createCommand()
            ->delete(Table::PROVIDERS->value, ['id' => $id])
            ->execute();
    }
}
