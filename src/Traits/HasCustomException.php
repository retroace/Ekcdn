<?php
namespace Retroace\Storage\Traits;

use Retroace\Storage\Exceptions\CustomGenericException;

/**
 * Fetches the url of assets
 */
trait HasCustomException
{
    protected function parseResponseAndThrowError($response)
    {
        return (new CustomGenericException())->sendException($response);
    }
}
