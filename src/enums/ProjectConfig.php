<?php

namespace GlueAgency\ImageCaption\enums;

enum ProjectConfig: string
{

    case PROVIDERS = 'glueAgency.imageCaptions.providers';
    case CONFIGURATIONS = 'glueAgency.imageCaptions.configurations';

    public static function implode(array $values, string $delimiter = '.'): string
    {
        return implode($delimiter, $values);
    }
}
