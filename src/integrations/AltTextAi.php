<?php

namespace GlueAgency\ImageCaption\integrations;

use craft\elements\Asset;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\integrations\responses\ErrorResponse;
use GlueAgency\ImageCaption\integrations\responses\ResponseInterface;
use GlueAgency\ImageCaption\integrations\settings\IntegrationSetting;
use GlueAgency\ImageCaption\integrations\responses\ParseResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AltTextAi extends AbstractIntegration implements IntegrationInterface
{

    const TOKEN = 'token';
    const ENDPOINT = 'endpoint';

    public string $name = 'Alt Text Ai';

    public function parse(Asset $asset): ResponseInterface
    {
        try {
            $client = $this->getAuthorizedClient();
            $language = strtok($asset->getSite()->language, '-');

            $response = $client->post('api/v1/images', [
                'json' => [
                    'lang'  => $language,
                    'image' => [
                        'asset_id' => $asset->id,
                        'url'      => $asset->getUrl(),
                    ],
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return new ParseResponse($data['alt_text']);
        } catch(RequestException $e) {
            ImageCaption::error($e->getMessage(), $e->getTraceAsString());

            return new ErrorResponse($e->getCode(), $e->getMessage());
        }
    }

    public function defineSettings(): array
    {
        return [
            new IntegrationSetting(
                IntegrationSetting::AUTOCOMPLETE,
                'Endpoint',
                self::ENDPOINT,
                null,
                true,
                'https://alttext.ai/'
            ),
            new IntegrationSetting(
                IntegrationSetting::PASSWORD,
                'Token',
                self::TOKEN,
                null,
                true
            )
        ];
    }

    public function getAuthorizedClient(): Client
    {
        return new Client([
            'base_uri' => $this->getSetting(self::ENDPOINT),
            'headers'  => [
                'X-API-Key' => $this->getSetting(self::TOKEN),
            ],
        ]);
    }
}
