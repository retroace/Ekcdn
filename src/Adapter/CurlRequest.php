<?php
namespace Retroace\Storage\Adapter;

use Exception;
use LogicException;

class CurlRequest
{
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
        return curl_init($this->getUrl($url)) !== false;
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
            if ($statusCode > 300) {
                $data = json_decode($response, true)['error'];
                throw new LogicException($data, $statusCode);
            }

            return $response;
        } catch (LogicException $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            throw new Exception("Curl error: $e", $e->getCode());
        }
    }
}
