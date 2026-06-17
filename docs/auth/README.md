# Módulo de Autenticação (Auth)

Este documento descreve a lógica e a arquitetura do sistema de autenticação do **ULO CRM Cobrança**.

---

## 1. Funcionamento Geral
A autenticação do sistema foi modularizada e reside integralmente no diretório `modules/Auth`. Ela é baseada em:
* **Laravel Fortify**: Gerencia as ações de login, registro, redefinição de senha, autenticação em duas etapas (2FA) e chaves de acesso (Passkeys).
* **Livewire + Flux UI**: Toda a interface de usuário utiliza componentes Livewire estilizados de forma moderna com o framework **Flux UI** e **daisyUI**.

---

## 2. Estrutura do Módulo
* **Actions**: Ações executadas pelo Fortify, localizadas em `modules/Auth/Actions/Fortify`.
* **Concerns**: Traits de utilidades e validações de dados (como validações de perfil e senhas).
* **Livewire/Settings**: Componentes da página de configurações da conta do usuário.
* **Resources/views**: Pasta contendo os templates blade sob o namespace `auth::`.
* **Routes**: Gerencia as rotas específicas em `modules/Auth/Routes/web.php`.
* **Providers**: `AuthModuleServiceProvider` que inicializa o Fortify, as rotas, o mapeamento de views e os aliases de componentes do Livewire.
