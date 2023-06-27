<?php

namespace GlueAgency\ImageCaption\migrations;

use craft\db\Migration;
use GlueAgency\ImageCaption\enums\Table;

/**
 * Install migration.
 */
class Install extends Migration
{

    public function safeUp(): bool
    {
        $this->dropTableIfExists(Table::PROVIDERS->value);
        $this->createTable(Table::PROVIDERS->value, [
            'id'          => $this->primaryKey(),
            'name'        => $this->string(100)->notNull(),
            'handle'      => $this->string(100)->notNull()->unique(),
            'class'       => $this->string(),
            'settings'    => $this->json(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'uid'         => $this->uid(),
        ]);

        $this->dropTableIfExists(Table::CONFIGURATIONS->value);
        $this->createTable(Table::CONFIGURATIONS->value, [
            'volumeHandle'   => $this->string(100)->notNull()->unique(),
            'providerHandle' => $this->string(100)->notNull(),
            'dateUpdated'    => $this->dateTime()->notNull(),
            'dateCreated'    => $this->dateTime()->notNull(),
            'uid'            => $this->uid(),
        ]);

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTableIfExists(Table::PROVIDERS->value);
        $this->dropTableIfExists(Table::CONFIGURATIONS->value);

        return true;
    }
}
