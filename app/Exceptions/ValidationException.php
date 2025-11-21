<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

class ValidationException extends Exception
{
    protected MessageBag $errors;

    public function __construct(string $message, MessageBag $errors, int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): MessageBag
    {
        return $this->errors;
    }

    public static function withErrors(string $message, MessageBag $errors): self
    {
        return new self($message, $errors);
    }
}
