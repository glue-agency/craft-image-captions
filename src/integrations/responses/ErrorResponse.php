<?php

namespace GlueAgency\ImageCaption\integrations\responses;

class ErrorResponse implements ResponseInterface
{

    public mixed $code;

    public string $message;

    public array $options = [];

    public function __construct(mixed $code, string $message, array $options = [])
    {
        $this->code = $code;
        $this->message = $message;
        $this->options = $options;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
