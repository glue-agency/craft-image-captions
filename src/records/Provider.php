<?php

namespace GlueAgency\ImageCaption\records;

use craft\db\ActiveRecord;
use GlueAgency\ImageCaption\enums\Table;

class Provider extends ActiveRecord
{

    public static function tableName()
    {
        return Table::PROVIDERS->value;
    }
}
