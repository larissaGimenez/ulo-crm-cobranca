<?php

namespace Modules\Clients\Contracts;

use Modules\Clients\Contracts\DTO\ClientData;

interface ClientServiceInterface
{
    /**
     * Busca ou cria um cliente com base no CPF/CNPJ.
     *
     * @param string $document
     * @param array{name: string, email?: string|null, tags?: string|null} $data
     * @return ClientData
     */
    public function findOrCreateByDocument(string $document, array $data): ClientData;
}
