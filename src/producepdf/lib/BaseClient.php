<?php

namespace ProducePdf;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use ProducePdf\Exception\RateLimitException;
use ProducePdf\Exception\UnknownApiErrorException;
use ProducePdf\Exception\InvalidArgumentException;

class BaseClient
{
    /** @var string default base URL for ProducePdf's API */
    const DEFAULT_API_BASE = 'https://api.producepdf.com';

    /** @var array<string, mixed> */
    public $config;

    public $defaultParams;

    public $client;

    public function __construct($config = [])
    {
        $this->client = new \GuzzleHttp\Client();

        if (\is_string($config)) {
            $config = ['api_key' => $config];
        } elseif (!\is_array($config)) {
            throw new InvalidArgumentException('$config must be a string or an array');
        }

        $config = \array_merge($this->getDefaultConfig(), $config);
        $this->validateConfig($config);

        $this->config = $config;

        $this->defaultParams = \array_filter($this->config, function ($k) {
            return $k !== 'api_key' && $k !== 'api_base';
        }, ARRAY_FILTER_USE_KEY);


    }

    /**
     * TODO: replace this with a private constant when we drop support for PHP < 5.
     *
     * @return array<string, mixed>
     */
    private function getDefaultConfig()
    {
        return [
            'api_key' => null,
            'api_base' => self::DEFAULT_API_BASE,
        ];
    }


    /**
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private function validateConfig($config)
    {
        // api_key
        $this->assertApiKey($config['api_key']);

        // api_base
        $this->assertApiBase($config['api_base']);
    }

    /**
     * @param $api_key
     * @return void
     */
    public function assertApiKey($api_key): void
    {
        if (null == $api_key && !\is_string($api_key)) {
            throw new InvalidArgumentException('api_key must be a string');
        }

        if ('' === $api_key) {
            $msg = 'api_key cannot be the empty string';

            throw new InvalidArgumentException($msg);
        }

        if (\preg_match('/\s/', $api_key)) {
            $msg = 'api_key cannot contain whitespace';

            throw new InvalidArgumentException($msg);
        }
    }

    /**
     * @param $api_base
     * @return void
     */
    public function assertApiBase($api_base): void
    {
        if (!\is_string($api_base)) {
            throw new InvalidArgumentException('api_base must be a string');
        }
    }

    /**
     * @throws RateLimitException
     * @throws UnknownApiErrorException
     */
    public function generatePdf($params)
    {

        try {
            $method = '';
            // $args = array_merge($params,$this)
            $params['api_key'] = $this->config['api_key'];
            $options = [];

            // post file
            if (array_key_exists('html', $params)) {
                $body = $params['html'];
                $params = \array_filter($params, function ($k) {
                    return $k !== 'html';
                }, ARRAY_FILTER_USE_KEY);
                $method = 'POST';
                $options = ['body' => $body];

            } else {
                // from ulr
                $method = 'GET';
            }
            //$params = array_merge($params, $this->defaultParams);

            foreach ($this->defaultParams as $keys => $values) {
                if (!array_key_exists($keys, $params)) {
                    $params[$keys] = $values;
                }
            }

            $uri = $this->config['api_base'] . '/v1?' . http_build_query($params, '', '&');


            $res = $this->client->request($method, $uri, $options);
            switch ($res->getStatusCode()) {
                case 200:
                    return $res->getBody();
                case 429:
                    throw new RateLimitException('api_base must be a string');
                default:
            }
        } catch (GuzzleException $e) {
            $res = $e->getResponse();
            switch ($res->getStatusCode()) {
                case 429:
                    throw new RateLimitException((string)$res->getBody());
            }

            throw new UnknownApiErrorException((string)$res->getBody());
        } catch (Exception $e) {
            $res = $e->getResponse();
            throw new UnknownApiErrorException((string)$res->getBody());
        }

    }

}
