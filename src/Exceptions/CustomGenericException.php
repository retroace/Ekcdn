<?php
namespace Retroace\Storage\Exceptions;

use Exception;

class CustomGenericException
{
    // Easy exception
    protected $exceptions = [
        0 => "FileNotFound",

        // Parameter or process exceptions
        3 => "InvalidApiKey",
        4 => "InactiveProject",
        5 => "UnverifiedUser",

        // Disk and file exceptions
        11 => "FilePostLimit",
        12 => "FileNotAllowed",
        13 => "ExceededDiskUsage",
        14 => "DirectoryNotFound",

        // Server exception
        21 => "DomainNotAllowed",

    ];

    public function sendException($response, $message = null)
    {
        $code = $response['code'];
        if ($code == 1) {
            return;
        }

        $exceptionClass = 'Retroace\Storage\Exceptions\\'. $this->exceptions[$code] .'Exception';

        if (class_exists($exceptionClass)) {
            throw new $exceptionClass($this->getApiErrorMessage($response), $code, $response);
        }

        throw new Exception($message, 400);
    }

    /**
     * Fetch api error message from api response
     * @param array $response
     * @return string
     */
    protected function getApiErrorMessage($response)
    {
        return isset($response['errors']) && isset($response['errors']['title']) ? $response['errors']['title'] : "Error while uploading image";
    }
}
