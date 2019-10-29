<?php

/*
 * This file is part of itk-dev/php-ckan-api-client.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\CKAN\API\Client;

use ItkDev\CKAN\Client\Exception\InvalidConfigurationException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Core API client.
 */
class Client
{
    protected $apiVersion = 3;

    protected $apiUrl;
    protected $apiKey;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * Client constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
        $this->apiUrl = rtrim($this->require('api_url'), '/').'/'.$this->apiVersion.'/';
        $this->apiKey = $this->require('api_key');
    }

    /**
     * @param string $id
     *
     * @return ResponseInterface
     */
    public function resourceShow(string $id): ResponseInterface
    {
        return $this->get('action/resource_show', [
            'query' => ['id' => $id],
        ]);
    }

    public function resourceUpdate(string $id, array $values): ResponseInterface
    {
        return $this->post('action/resource_update', [
            'json' => $values + ['id' => $id],
        ]);
    }

    public function resourceDelete(string $id): ResponseInterface
    {
        return $this->post('action/resource_delete', [
            'json' => ['id' => $id],
        ]);
    }

    /**
     * Require a configuration value.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function require(string $key)
    {
        if (!\array_key_exists($key, $this->configuration)) {
            throw new InvalidConfigurationException(sprintf('Missing configuration for key: %s', $key));
        }
        if (!isset($this->configuration[$key])) {
            throw new InvalidConfigurationException(sprintf('Missing value for key: %s', $key));
        }

        return $this->configuration[$key];
    }

    protected function get(string $path, array $options): ResponseInterface
    {
        return $this->request('GET', $path, $options);
    }

    protected function post(string $path, array $options): ResponseInterface
    {
        return $this->request('POST', $path, $options);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $options
     *
     * @return ResponseInterface
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function request(string $method, string $path, array $options): ResponseInterface
    {
        return $this->client()->request($method, $path, $options);
    }

    /**
     * @var HttpClientInterface
     */
    private $client;

    protected function client(): HttpClientInterface
    {
        if (null === $this->client) {
            $this->client = HttpClient::create([
                'headers' => [
                    'Authorization' => $this->apiKey,
                ],
                'base_uri' => $this->apiUrl,
            ]);
        }

        return $this->client;
    }
}
