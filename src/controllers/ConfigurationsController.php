<?php

namespace GlueAgency\ImageCaption\controllers;

use Craft;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\models\Configuration;
use yii\web\Response;

class ConfigurationsController extends Controller
{

    public function actionIndex(): Response
    {
        return $this->renderTemplate('image-captions/configurations/index', [
            'volumes' => Craft::$app->getVolumes()->getAllVolumes(),
            'providersAsOptions' => array_merge(
                [
                    [
                        'label'=> null,
                        'value'=> null
                    ]
                ],
                array_map(function($provider) {
                    return [
                        'label' => $provider->name,
                        'value' => $provider->handle,
                    ];
                }, ImageCaption::getInstance()->provider->getAll())
            )
        ]);
    }

    public function actionSave(): Response
    {
        $request = Craft::$app->getRequest();
        $data = $request->getParam('configuration');
        $configured = array_filter($data);

        $configurations = [];
        $existingConfigurations = ImageCaption::getInstance()->configuration->findByVolumeHandles(array_keys($configured));

        foreach($configured as $volumeHandle => $providerHandle) {
            $configuration = current(array_filter($existingConfigurations, function($existingConfiguration) use ($volumeHandle) {
                return $existingConfiguration->volumeHandle == $volumeHandle;
            }));

            if(! $configuration) {
                $configuration = new Configuration;
                $configuration->uid = StringHelper::UUID();
            }

            $configuration->volumeHandle = $volumeHandle;
            $configuration->providerHandle = $providerHandle;

            $configurations[] = $configuration;
        }

        if(ImageCaption::getInstance()->configuration->sync($configurations)) {
            Craft::$app->getSession()->setSuccess(Craft::t('image-captions', 'Configurations saved.'));

            return $this->redirect(UrlHelper::cpUrl('image-captions/configurations'));
        }

        Craft::$app->getSession()->setError(Craft::t('image-captions', 'Configurations not saved.'));

        return $this->renderTemplate('image-captions/configurations', [
            'volumes' => Craft::$app->getVolumes()->getAllVolumes(),
        ]);
    }
}
