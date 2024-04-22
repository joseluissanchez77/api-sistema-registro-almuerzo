<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Http\Response;

class DatabaseException extends Exception
{
    private $statusCode;

    public function __construct(string $message = '', \Throwable $previous = null) {
        $this->statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        parent::__construct($message, $this->statusCode, $previous);
    }

    public function getStatusCode() {
        return $this->statusCode;
    }
}
