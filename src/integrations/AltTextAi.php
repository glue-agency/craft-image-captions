<?php

namespace GlueAgency\ImageCaption\integrations;

use Craft;
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
    const KEYWORDS = 'keywords';

    public string $name = 'Alt Text Ai';

    public function parse(Asset $asset): ResponseInterface
    {
        try {
            $client = $this->getAuthorizedClient();
            $language = strtok($asset->getSite()->language, '-');

            $params = [
                'lang'  => $language,
                'image' => [
                    'asset_id' => $asset->id,
                    'url'      => $asset->getUrl(),
                ],
            ];

            if($keywords = $this->getSetting(self::KEYWORDS))
            {
                $parts = explode(' ', $keywords);
                $allowed = array_slice($parts, 0, 6);

                $params['keywords'] = $allowed;
            }

            $response = $client->post('api/v1/images', [
                'json' => $params,
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
                Craft::t('image-captions', 'Endpoint'),
                self::ENDPOINT,
                null,
                true,
                'https://alttext.ai/'
            ),
            new IntegrationSetting(
                IntegrationSetting::PASSWORD,
                Craft::t('image-captions','Token'),
                self::TOKEN,
                null,
                true
            ),
            new IntegrationSetting(
                IntegrationSetting::TEXT,
                Craft::t('image-captions','Keywords'),
                self::KEYWORDS,
                Craft::t('image-captions','Add up to 6 English keywords separated by a space that should be considered when generating the alt text.'),
            )
        ];
    }

    protected function getAuthorizedClient(): Client
    {
        return new Client([
            'base_uri' => $this->getSetting(self::ENDPOINT),
            'headers'  => [
                'X-API-Key' => $this->getSetting(self::TOKEN),
            ],
        ]);
    }
}
