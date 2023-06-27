<?php

namespace GlueAgency\ImageCaption\integrations\responses;

class ParseResponse implements ResponseInterface
{

    public string $message;

    public function __construct(string $message)
    {

        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
