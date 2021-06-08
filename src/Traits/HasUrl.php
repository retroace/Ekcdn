<?php
namespace Retroace\Storage\Traits;

/**
 * Fetches the url of assets
 */
trait HasUrl
{

    /**
     * Get url of the asset
     * @param string $path Resources to fetch
     * @param boolean $client Fetch User Domain Url
     * @return string
     */
    public function getUrl($path, $client = true)
    {
        $path = str_replace('%2F', '/', $path);
        $path = str_replace(' ', '%20', $path);

        return sprintf('%s/%s', $client  ? $this->baseClient : $this->base, $path);
    }

    /**
     * Returns the URL to perform an HTTP request.
     *
     * @param string $path
     *
     * @return string
     */
    protected function buildUrl($path)
    {
        $path = str_replace('%2F', '/', $path);
        $path = str_replace(' ', '%20', $path);

        return rtrim($this->base, '/') . '/' . $path;
    }


    /**
     * Returns the URL to perform an HTTP request.
     *
     * @param string $path
     *
     * @return string
     */
    protected function buildClientUrl($path)
    {
        $path = str_replace('%2F', '/', $path);
        $path = str_replace(' ', '%20', $path);

        return rtrim($this->baseClient, '/') . '/' . $path;
    }
}
