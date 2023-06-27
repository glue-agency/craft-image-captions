<?php

namespace GlueAgency\ImageCaption\controllers;

use Carbon\Carbon;
use Craft;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\models\Provider;
use yii\web\Response;

class ProvidersController extends Controller
{

    public function actionIndex(): Response
    {
        return $this->renderTemplate('image-captions/providers/index', [
            'providers' => ImageCaption::getInstance()->provider->getConfiguredProviders(),
        ]);
    }

    public function actionNew(): Response
    {
        return $this->renderTemplate('image-captions/providers/edit', [
            'provider'     => new Provider,
            'integrations' => ImageCaption::getInstance()->integration->getAll(),
        ]);
    }

    public function actionSave(): Response
    {
        $request = Craft::$app->getRequest();
        $provider = ImageCaption::getInstance()->provider->getById($request->post('id'));

        if(! $provider) {
            $provider = new Provider;
            $provider->uid = StringHelper::UUID();
        }

        $provider->name = $request->post('name');
        $provider->handle = $request->post('handle');
        $provider->class = $request->post('class');
        $provider->settings = $request->post($provider->class);

        if(ImageCaption::getInstance()->provider->save($provider)) {
            Craft::$app->getSession()->setSuccess(Craft::t('image-captions', 'Provider saved.'));

            return $this->redirectToPostedUrl($provider);
        }

        Craft::$app->getSession()->setError(Craft::t('image-captions', 'Provider not saved.'));

        return $this->renderTemplate('image-captions/providers/edit', [
            'provider'     => $provider,
            'integrations' => ImageCaption::getInstance()->integration->getAll(),
        ]);
    }

    public function actionEdit($providerId): Response
    {
        $provider = ImageCaption::getInstance()->provider->getById($providerId);

        if(! $provider) {
            Craft::$app->getSession()->setError(Craft::t('image-captions', 'Provider not found.'));

            return $this->redirect(UrlHelper::cpUrl('image-captions/providers'));
        }

        return $this->renderTemplate('image-captions/providers/edit', [
            'provider'     => $provider,
            'integrations' => ImageCaption::getInstance()->integration->getAll(),
        ]);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();

        if(ImageCaption::getInstance()->provider->deleteById(Craft::$app->request->post('id'))) {
            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['success' => false]);
    }
}
