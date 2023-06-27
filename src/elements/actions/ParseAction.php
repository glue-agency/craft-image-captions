<?php

namespace GlueAgency\ImageCaption\elements\actions;

use Craft;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Queue;
use GlueAgency\ImageCaption\ImageCaption;
use GlueAgency\ImageCaption\queue\jobs\GenerateImageCaptionJob;

class ParseAction extends ElementAction
{
    /**
     * @var string|null The trigger label
     */
    public ?string $label = null;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        if (! isset($this->label)) {
            $this->label = Craft::t('image-captions', 'Generate Alt Text');
        }
    }

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml(): ?string
    {
        Craft::$app->getView()->registerJsWithVars(fn($type) => <<<JS
(() => {
    new Craft.ElementActionTrigger({
        type: $type,
        bulk: true,
        validateSelection: \$selectedItems => Garnish.hasAttr(\$selectedItems.find('.element'), 'data-image-captionable'),
    });
})();
JS, [static::class]);

        return null;
    }

    public function performAction(ElementQueryInterface $query): bool
    {
        $assets = $query->all();

        foreach($assets as $asset) {
            Queue::push(new GenerateImageCaptionJob([
                'asset' => $asset
            ]));
        }

        return true;
    }
}
