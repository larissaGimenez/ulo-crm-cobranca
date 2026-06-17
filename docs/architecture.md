# Arquitetura do Sistema: Monólito Modular

Este documento descreve as diretrizes arquiteturais do sistema **ULO CRM Cobrança**. O projeto é estruturado como um **Monólito Modular**, combinando a simplicidade de implantação de um monólito com a separação de conceitos e facilidade de manutenção de uma arquitetura modular.

---

## 1. Visão Geral

No monólito modular, cada grande domínio de negócio (ex: `Clients`, `Billing`, `Deals`) é tratado como um módulo independente localizado na pasta `/modules`. 

A principal regra de design é o **baixo acoplamento**. Os módulos se comportam como "caixas-pretas". Se o módulo `Billing` precisa de informações de `Clients`, ele não pode acessar diretamente o Model `Client` ou a classe de serviço interna do módulo `Clients`. Ele deve fazer isso obrigatoriamente através de **Contratos (Interfaces)**.

---

## 2. Estrutura de Pasta de um Módulo

Cada módulo na pasta `/modules` segue esta anatomia:

```
modules/
└── <NomeDoModulo>/
    ├── Contracts/            # Interfaces públicas e DTOs (ponto de entrada do módulo)
    │   ├── ClientServiceInterface.php
    │   └── DTO/
    │       └── ClientData.php
    ├── Database/             # Migrations, Seeders e Factories do módulo
    ├── Http/                 # Controllers, Requests e Resources
    ├── Models/               # Models Eloquent (Privados do módulo)
    ├── Providers/            # Service Providers do Módulo (Mapeamento de contratos e registros)
    ├── Routes/               # Definições de rotas locais
    └── Services/             # Implementação prática da lógica e dos Contratos
```

---

## 3. Diretrizes e Regras de Implementação

### 3.1. Comunicação Entre Módulos
1. **Interfaces Públicas:** Apenas o conteúdo da pasta `Contracts/` é considerado a API pública do módulo.
2. **Sem Importações Diretas:** É proibido importar classes das pastas `Models/`, `Services/` ou `Http/` de outros módulos.
3. **DTOs (Data Transfer Objects):** Para enviar ou receber dados complexos entre módulos, utilize DTOs definidos na pasta `Contracts/DTO/`. Isso impede que Models do Eloquent vazem para fora de seu próprio módulo.

### 3.2. Banco de Dados e Relacionamentos
* **Sem Joins ou Relacionamentos Eloquent diretos entre módulos:** Não crie métodos como `$this->belongsTo(Client::class)` no model do módulo `Billing`.
* **Como referenciar:** Guarde o campo `client_id` (inteiro/UUID) na tabela do banco de dados. Quando precisar dos dados do cliente, chame o contrato exposto pelo módulo `Clients`:
  ```php
  $clientData = $this->clientService->findById($billing->client_id);
  ```

---

## 4. Exemplos Práticos de Implementação

### Passo 1: Definir o Contrato (no Módulo Origem)
No arquivo `modules/Clients/Contracts/ClientServiceInterface.php`:
```php
<?php

namespace Modules\Clients\Contracts;

use Modules\Clients\Contracts\DTO\ClientData;

interface ClientServiceInterface
{
    public function findById(int $id): ?ClientData;
}
```

E o DTO correspondente em `modules/Clients/Contracts/DTO/ClientData.php`:
```php
<?php

namespace Modules\Clients\Contracts\DTO;

class ClientData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}
```

### Passo 2: Implementar o Serviço (no Módulo Origem)
No arquivo `modules/Clients/Services/ClientService.php`:
```php
<?php

namespace Modules\Clients\Services;

use Modules\Clients\Contracts\ClientServiceInterface;
use Modules\Clients\Contracts\DTO\ClientData;
use Modules\Clients\Models\Client;

class ClientService implements ClientServiceInterface
{
    public function findById(int $id): ?ClientData
    {
        $client = Client::find($id);

        if (!$client) {
            return null;
        }

        return new ClientData(
            id: $client->id,
            name: $client->name,
            email: $client->email
        );
    }
}
```

### Passo 3: Registrar o Vínculo no Service Provider (do Módulo Origem)
No arquivo `modules/Clients/Providers/ClientsModuleServiceProvider.php`:
```php
<?php

namespace Modules\Clients\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Clients\Contracts\ClientServiceInterface;
use Modules\Clients\Services\ClientService;

class ClientsModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Vincula a Interface pública com a nossa implementação interna
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
```

### Passo 4: Consumir o Contrato em outro Módulo
No módulo `Billing`, injetamos o contrato via construtor:
```php
<?php

namespace Modules\Billing\Services;

use Modules\Clients\Contracts\ClientServiceInterface;

class BillingService
{
    public function __construct(
        protected ClientServiceInterface $clientService
    ) {}

    public function processInvoice(int $clientId, float $amount)
    {
        // Consome as informações do cliente de forma totalmente desacoplada
        $client = $this->clientService->findById($clientId);

        if (!$client) {
            throw new \Exception("Cliente não encontrado.");
        }

        // Processa a cobrança...
        $emailDestinatario = $client->email;
    }
}
```
