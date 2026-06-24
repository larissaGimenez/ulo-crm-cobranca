<?php

namespace Modules\Clients\Services;

use Modules\Clients\Contracts\ClientServiceInterface;
use Modules\Clients\Contracts\DTO\ClientData;
use Modules\Clients\Models\Client;

class ClientService implements ClientServiceInterface
{
    /**
     * Busca ou cria um cliente com base no CPF/CNPJ.
     */
    public function findOrCreateByDocument(string $document, array $data): ClientData
    {
        $client = Client::updateOrCreate(
            ['document' => $document],
            [
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'tags' => $data['tags'] ?? null,
            ]
        );

        return new ClientData(
            id: $client->id,
            document: $client->document,
            name: $client->name,
            email: $client->email,
            tags: $client->tags
        );
    }
}
