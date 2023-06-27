<?php

namespace GlueAgency\ImageCaption\controllers;

use Craft;
use craft\web\Controller;
use GlueAgency\ImageCaption\ImageCaption;
use yii\web\Response;

class AssetsController extends Controller
{

    public function actionIndex($assetId): Response
    {
        $asset = Craft::$app->getAssets()->getAssetById($assetId);

        ImageCaption::getInstance()->asset->parse($asset);

        return $this->redirect($asset->getCpEditUrl());
    }
}
