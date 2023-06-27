<?php

namespace GlueAgency\ImageCaption\queue\jobs;

use craft\elements\Asset;
use craft\i18n\Translation;
use craft\queue\BaseJob;
use GlueAgency\ImageCaption\ImageCaption;

class GenerateImageCaptionJob extends BaseJob
{

    public Asset $asset;

    protected function defaultDescription(): ?string
    {
        return Translation::prep('image-captions', 'Generating alt text for "{name}"', [
            'name' => $this->asset->title,
        ]);
    }

    public function execute($queue): void
    {
        ImageCaption::getInstance()->asset->parse($this->asset, true);

        $this->setProgress($queue, 1);
    }
}
