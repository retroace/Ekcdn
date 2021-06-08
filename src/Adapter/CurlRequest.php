<?php
namespace Retroace\Storage\Adapter;

use Exception;
use LogicException;
use Retroace\Storage\Traits\HasCustomException;

class CurlRequest
{
    use HasCustomException;
    public function __construct($baseUrl, $headers = [])
    {
        $this->headers = $headers;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get url of the site
     * @param string $url
     * @return string
     */
    protected function getUrl($url = "")
    {
        return sprintf('%s/%s', $this->baseUrl, $url);
    }


    /**
     * Check if the client has file
     * @param string $url
     * @return bool
     */
    public function hasFile($url)
    {
        $headers = get_headers($this->getUrl($url));
        return !str_contains($headers[0], '404');
    }

    /**
     * Send formdata post request
     * @param string $url
     * @param array $body
     * @param array $headers
     * @return string|Exception
     */
    public function post($url, $body, $headers = ["Accept: application/json"])
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->getUrl($url),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_CONNECTTIMEOUT => 300,
                CURLOPT_HTTPHEADER => array_merge($this->headers, $headers),
            ]);

            $response = curl_exec($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($response === false) {
                curl_close($curl);
                throw new Exception(curl_error($curl), curl_errno($curl));
            }

            curl_close($curl);
            $data = json_decode($response, true);
            if ($statusCode > 300) {
                return $this->parseResponseAndThrowError($data);
            }

            return $data;
        } catch (LogicException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
