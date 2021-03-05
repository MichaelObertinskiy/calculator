<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class BaseException extends Exception
{
    const INTERNAL_ERROR = 'internal_error';

    protected $key;

    public function __construct($key = "", $message = "", $code = 0, Throwable $previous = null)
    {
        $this->key = $key;
        parent::__construct($message, $code, $previous);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function errors(): array
    {
        if ($this->getKey() === self::INTERNAL_ERROR)
            return [''];
        return $this->getKey() ? [$this->getKey() => [$this->getMessage()]] : [$this->getMessage()];
    }
}
