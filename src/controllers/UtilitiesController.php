<?php

namespace GlueAgency\ImageCaption\controllers;

use Craft;
use craft\elements\Asset;
use craft\helpers\Queue;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\queue\jobs\GenerateImageCaptionJob;
use yii\web\Response;

class UtilitiesController extends Controller
{

    public function actionIndex(): Response
    {
        $volumeHandles = Craft::$app->getRequest()->getBodyParam('volumes', []);

        if(empty($volumeHandles)) {
            Craft::$app->getSession()->setError(Craft::t('image-captions', 'No volumes selected.'));

            return $this->redirect(UrlHelper::cpUrl('utilities/image-captions'));
        }

        if($volumeHandles === '*') {
            $volumes = Craft::$app->volumes->getAllVolumes();
        } else {
            $volumes = array_filter(Craft::$app->volumes->getAllVolumes(), function($volume) use ($volumeHandles) {
              return in_array($volume->handle, $volumeHandles);
            });
        }

        $allowed = array_filter($volumes, function($volume) {
            return in_array($volume->handle, ImageCaption::getInstance()->configuration->getAllVolumeHandles());
        });

        if(empty($allowed)) {
            Craft::$app->getSession()->setError(Craft::t('image-captions', 'No allowed volumes selected.'));

            return $this->redirect(UrlHelper::cpUrl('utilities/image-captions'));
        }

        foreach($allowed as $volume) {
            // @todo batch this query
            $assets = Asset::find()
                ->volumeId($volume->id)
                ->kind('image')
                ->includeSubfolders(true)
                ->limit(null)
                ->all();

            foreach($assets as $asset) {
                // @todo batch queue job?
                Queue::push(new GenerateImageCaptionJob($asset));
            }
        }

        Craft::$app->getSession()->setSuccess(Craft::t('image-captions', 'Indexing started.'));

        return $this->redirect(UrlHelper::cpUrl('utilities/image-captions'));
    }
}
