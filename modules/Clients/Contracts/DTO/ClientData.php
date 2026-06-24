<?php

namespace Modules\Clients\Contracts\DTO;

class ClientData
{
    public function __construct(
        public int $id,
        public string $document,
        public string $name,
        public ?string $email,
        public ?string $tags
    ) {}
}
