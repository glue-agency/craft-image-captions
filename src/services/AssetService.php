<?php

namespace GlueAgency\ImageCaption\services;

use Craft;
use craft\elements\Asset;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\integrations\responses\ErrorResponse;
use GlueAgency\ImageCaption\models\Provider;
use yii\base\Component;

class AssetService extends Component
{

    public function parse(Asset $asset, bool $runningAsQueuedJob = false): bool
    {
        $provider = $this->getProvider($asset);

        // Forcibly set the Asset scenario to default
        $asset->setScenario(Asset::SCENARIO_DEFAULT);

        if(! $provider) {
            return false;
        }

        $integration = ImageCaption::getInstance()->integration->buildFromProvider($provider);
        $response = $integration->parse($asset);

        if($response instanceof ErrorResponse) {
            if(! $runningAsQueuedJob) {
                Craft::$app->getSession()->setError(Craft::t('image-captions', 'Could not generate captions: “{message}”', [
                    'message' => $response->getMessage(),
                ]));
            }

            return false;
        }

        $asset->alt = $response->message;

        return Craft::$app->getElements()->saveElement($asset, false);
    }

    public function getProvider(Asset $asset): ?Provider
    {
        if($this->hasConfiguration($asset)) {
            $providerHandle = ImageCaption::getInstance()->configuration->getProviderHandleForVolume($asset->volume);

            return ImageCaption::getInstance()->provider->getByHandle($providerHandle);
        }

        return null;
    }

    public function hasConfiguration(Asset $asset = null): bool
    {
        if(! $asset) {
            return false;
        }

        if(! $asset->volumeId) {
            return false;
        }

        if(in_array($asset->volume->handle, ImageCaption::getInstance()->configuration->getAllVolumeHandles())) {
            return true;
        }

        return false;
    }
}
