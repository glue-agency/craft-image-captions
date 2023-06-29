<?php

namespace GlueAgency\ImageCaption\validators;

use Craft;
use yii\validators\Validator;

class IntegrationSettingsRequiredValidator extends Validator
{

    public function validateAttribute($model, $attribute): void
    {
        $integration = new $model->class;

        foreach($integration->defineSettings() as $setting) {
            if($setting->required && empty($model->{$attribute}[$setting->handle])) {
                $this->addError($model, "{$model->class}.{$setting->handle}", Craft::t('yii', '{attribute} cannot be blank.', ['attribute' => $setting->label]), [
                    'value' => '',
                ]);
            }
        }
    }
}
