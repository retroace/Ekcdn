<?php
namespace Retroace\Storage\Exceptions;

class CorsException extends \RuntimeException
{
    private $response;

    public function __construct($message, $code, $response)
    {
        $this->message = $message;
        $this->code = $code;
        $this->response = $response;
    }


    public function getResponse()
    {
        return $this->response;
    }
}
