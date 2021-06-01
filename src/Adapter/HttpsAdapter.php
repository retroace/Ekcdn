<?php

namespace Retroace\Storage\Adapter;

use CURLFile;
use GuzzleHttp\Client;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Traits\Macroable;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\Util\MimeType;
use LogicException;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Provides an adapter using PHP native HTTP functions.
 */
class HttpsAdapter implements AdapterInterface
{
    use Macroable, NotSupportingVisibilityTrait;
    /**
     * The base URL.
     *
     * @var string
     */
    protected $base;

     /**
     * The base URL.
     *
     * @var string
     */
    protected $baseClient;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var bool
     */
    protected $supportsHead;

    /**
     * Constructs an HttpAdapter object.
     *
     * @param string $base         The base URL
     * @param bool   $supportsHead Whether the endpoint supports HEAD requests
     * @param array  $context      Context options
     */
    public function __construct($base, $supportsHead = true, array $context = [], $clientSetting = [])
    {
        $this->base = $base;
        $this->baseClient = $clientSetting['url'];
        $this->supportsHead = $supportsHead;
        $this->context = $context;

        // Add in some safe defaults for SSL/TLS. Don't know why PHPUnit/Xdebug
        // messes this up.
        $this->context += [
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'SNI_enabled' => true,
                'disable_compression' => true,
            ],
        ];


        $this->client = new CurlRequest($this->base, [ 'Api-Key: '. $clientSetting['headers']['apiKey'] ]);
        $this->assetsRequest = new CurlRequest($this->baseClient);
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $response = $this->client->post('api/v1/copy', [
            'origin' => $path,
            'destination' => $newpath,
        ]);
        return (bool) $response;
    }

    /**
     * @inheritdoc
     */
    public function createDir($path, Config $config)
    {
        $response = $this->client->post('api/v1/make-directory', [ 'path' => $path ]);

        return (bool) $response;
    }


    /**
     * get url of the image
     * @param string $url
     * @return string
     */
    protected function getUrl($url, $client = false)
    {
        $path = str_replace('%2F', '/', $url);
        $path = str_replace(' ', '%20', $path);
        return sprintf('%s/%s', $client  ? $this->baseClient : $this->base, $path);
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        if(!str_contains($path, "."))
        {
            throw new LogicException("You must provide file name for delete. Did you mean delete directory?");
        }

        $response = $this->client->post('api/v1/delete', [
            'path' => $path,
            'directory' => true
        ]);
        return (bool) $response;
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($path)
    {
        if(str_contains($path, "."))
        {
            throw new LogicException("You must provide valid folder name to delete. Did you mean to delete file?");
        }

        $response = $this->client->post('api/v1/delete', [
            'path' => $path,
            'directory' => false
        ]);

        return true;
    }

    public function getPathPrefix()
    {
        return $this->context['url'] ;
    }

    /**
     * Returns the base path.
     *
     * @return string The base path
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        if (false === $headers = $this->head($path)) {
            return false;
        }

        return ['type' => 'file'] + $this->parseMetadata($path, $headers);
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }


    /**
     * @inheritdoc
     */
    public function has($path)
    {
        try{
            return (bool) $this->assetsRequest->hasFile($path);
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {

        $response = $this->client->post('api/v1/list', [
            'directory' => $directory,
            'recursive' => $recursive
        ]);

        $data = json_decode($response, true)['data'];
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        $context = stream_context_create($this->context);
        $contents = file_get_contents($this->getUrl($path, true), false, $context);

        if ($contents === false) {
            return false;
        }

        return compact('path', 'contents');
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        $context = stream_context_create($this->context);
        $stream = fopen($this->getUrl($path, true), 'rb', false, $context);

        if ($stream === false) {
            return false;
        }

        return [
            'path' => $path,
            'stream' => $stream,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $response = $this->client->post('api/v1/move', [
            'origin' => $path,
            'destination' => $newpath,
        ]);
        return (bool) $response;
    }

    /**
     * Sets the HTTP context options.
     *
     * @param array $context The context options
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        throw new \LogicException('HttpAdapter does not support visibility. Path: ' . $path . ', visibility: ' . $visibility);
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, Config $conf)
    {
        throw new \LogicException("Updating is not supported", 400);
        return false;
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        throw new \LogicException("Updating via strem is not supported", 400);
        return false;
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, Config $config)
    {
        $allPath = explode('/', $path);
        $hasFileName = str_contains($allPath[count($allPath) - 1], ".");
        if ($hasFileName) {
            $fileName = $allPath[count($allPath) - 1];
            array_pop($allPath);
            $directory = implode("/", $allPath);
        } else {
            $directory = $path;
            $fileName = $contents->getClientOriginalName();
        }
        $allPath = $directory."/".$fileName;
        $content = $contents->getPathName();

        $path = $this->client->post('api/v1/upload' ,['file' => new CURLFile($content),'directory' => $allPath,'makeDirectory' => 'true']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        return false;
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
     * Performs a HEAD request.
     *
     * @param string $path
     *
     * @return array|false
     */
    protected function head($path)
    {
        $defaults = stream_context_get_options(stream_context_get_default());
        $options = $this->context;

        if ($this->supportsHead) {
            $options['http']['method'] = 'HEAD';
        }

        stream_context_set_default($options);

        $headers = get_headers($this->buildUrl($path), 1);

        stream_context_set_default($defaults);
        if ($headers === false || strpos($headers[0], ' 200') === false) {
            return false;
        }

        return array_change_key_case($headers);
    }

    /**
     * Parses the timestamp out of headers.
     *
     * @param array $headers
     *
     * @return int|false
     */
    protected function parseTimestamp(array $headers)
    {
        if (isset($headers['last-modified'])) {
            return strtotime($headers['last-modified']);
        }

        return false;
    }


    /**
     * Parses metadata out of response headers.
     *
     * @param string $path
     * @param array  $headers
     *
     * @return array
     */
    protected function parseMetadata($path, array $headers)
    {
        $metadata = [
            'path' => $path,
            'mimetype' => $this->parseMimeType($path, $headers),
        ];

        if (false !== $timestamp = $this->parseTimestamp($headers)) {
            $metadata['timestamp'] = $timestamp;
        }

        if (isset($headers['content-length']) && is_numeric($headers['content-length'])) {
            $metadata['size'] = (int) $headers['content-length'];
        }

        return $metadata;
    }

    /**
     * Parses the mimetype out of response headers.
     *
     * @param string $path
     * @param array  $headers
     *
     * @return string
     */
    protected function parseMimeType($path, array $headers)
    {
        if (isset($headers['content-type'])) {
            list($mimetype) = explode(';', $headers['content-type'], 2);

            return trim($mimetype);
        }

        // Remove any query strings or fragments.
        list($path) = explode('#', $path, 2);
        list($path) = explode('?', $path, 2);

        return MimeType::detectByFilename($path);
    }
}
