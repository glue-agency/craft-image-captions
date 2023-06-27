<?php

namespace GlueAgency\ImageCaption;

use Composer\Autoload\ClassMapGenerator;
use Craft;
use craft\log\MonologTarget;
use GlueAgency\ImageCaption\models\Configuration;
use GlueAgency\ImageCaption\services\AssetService;
use GlueAgency\ImageCaption\services\ConfigurationService;
use GlueAgency\ImageCaption\services\IntegrationService;
use GlueAgency\ImageCaption\services\ProviderService;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;
use ReflectionClass;
use craft\base\Model;
use craft\base\Plugin;

/**
 * Image Captions plugin
 *
 * @method static ImageCaption getInstance()
 * @property-read Configuration        $settings
 * @property-read ConfigurationService $configuration
 * @property-read ProviderService      $provider
 * @property-read IntegrationService   $integration
 * @property-read AssetService         $asset
 */
class ImageCaption extends Plugin
{

    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = true;

    protected static bool $initialized = false;

    public static function config(): array
    {
        return [
            'components' => [
                'configuration' => ConfigurationService::class,
                'provider'      => ProviderService::class,
                'integration'   => IntegrationService::class,
                'asset'         => AssetService::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->registerControllers();
            $this->registerLogger();

            $this->initializeListeners();
        });

        Craft::info(
            Craft::t(
                'image-captions',
                '{name} plugin loaded',
                ['name' => 'Image Captions']
            ),
            __METHOD__
        );
    }

    public static function info(string $message, string $trace = null): void
    {
        if($trace) {
            $message = $message . '\n' . $trace;
        }

        Craft::info($message, 'image-captions');
    }

    public static function error(string $message, string $trace = null): void
    {
        if($trace) {
            $message = $message . '\n' . $trace;
        }

        Craft::error($message, 'image-captions');
    }

    protected function createSettingsModel(): ?Model
    {
        return new Configuration;
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->controller->redirect('image-captions');
    }

    protected function initializeListeners(): void
    {
        if(! self::$initialized) {
            $classMap = ClassMapGenerator::createMap(__DIR__ . '/listeners');

            foreach($classMap as $class => $path) {
                $reflectionClass = new ReflectionClass($class);

                if(! $reflectionClass->isAbstract() && ! $reflectionClass->isInterface()) {
                    $reflectionClass->newInstance();
                }
            }

            self::$initialized = true;
        }
    }

    protected function registerControllers(): void
    {
        if(Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'GlueAgency\\ImageCaption\\console\\controllers';

            return;
        }

        $this->controllerNamespace = 'GlueAgency\\ImageCaption\\controllers';
    }

    protected function registerLogger(): void
    {
        Craft::getLogger()->dispatcher->targets['image-captions'] = new MonologTarget([
            'name'            => 'image-captions',
            'categories'      => ['image-captions'],
            'level'           => LogLevel::INFO,
            'allowLineBreaks' => true,
            'formatter'       => new LineFormatter(
                format: null,
                dateFormat: null,
                allowInlineLineBreaks: true
            ),
        ]);
    }
}
