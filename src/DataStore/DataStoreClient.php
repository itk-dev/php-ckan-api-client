<?php

/*
 * This file is part of itk-dev/php-ckan-api-client.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace ItkDev\CKAN\API\Client\DataStore;

use ItkDev\CKAN\API\Client\Client;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * API client extended with DataStore API endpoints.
 *
 * @see https://docs.ckan.org/en/latest/maintaining/datastore.html#the-datastore-api
 */
class DataStoreClient extends Client
{
    public function dataStoreShow(string $id): ResponseInterface
    {
        return $this->get('action/datastore_search', [
            'query' => ['resource_id' => $id],
        ]);
    }

    public function dataStoreInfo(string $id): ResponseInterface
    {
        return $this->post('action/datastore_info', [
            'json' => ['id' => $id],
        ]);
    }

    public function dataStoreCreate(?string $packageId, array $options): ResponseInterface
    {
        if (null !== $packageId && !isset($options['resource']['package_id'])) {
            $options['resource']['package_id'] = $packageId;
        }

        return $this->post('action/datastore_create', [
            'json' => $options,
        ]);
    }

    public function dataStoreUpsert(string $resourceId, array $options = []): ResponseInterface
    {
        return $this->post('action/datastore_upsert', [
            'json' => $options + ['resource_id' => $resourceId],
        ]);
    }

    public function dataStoreInsert(string $resourceId, array $options = []): ResponseInterface
    {
        return $this->dataStoreUpsert($options + ['method' => 'insert']);
    }

    public function dataStoreDelete(string $resourceId): ResponseInterface
    {
        return $this->post('action/datastore_delete', [
            'json' => ['resource_id' => $resourceId],
        ]);
    }

    public function dataStoreSearch(string $resourceId, array $query = []): ResponseInterface
    {
        return $this->get('action/datastore_search', ['query' => $query + ['resource_id' => $resourceId]]);
    }

    public function dataStoreSearchSql(): ResponseInterface
    {
        throw new \RuntimeException(__METHOD__.' not implemented.');
    }
}
