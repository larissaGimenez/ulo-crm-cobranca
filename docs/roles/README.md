# Entidade: Perfis (Roles) e Permissões (Permissions)

Este documento detalha o sistema de controle de acesso (ACL) adotado no **ULO CRM Cobrança**.

---

## 1. Funcionamento Geral (ACL)
O controle de acessos é baseado no pacote **Spatie Laravel Permission** integrado no módulo de autenticação e no modelo do usuário (`App\Models\User`).

* **Roles (Perfis)**: Representam as permissões agrupadas dos usuários (ex: `master`, `admin`).
* **Permissions (Permissões)**: Representam os acessos específicos (ex: `view-clients`, `create-deals`).

---

## 2. Regra de Negócio: Perfil Único
* **Restrição de Role Único:** Cada usuário no sistema só pode possuir **um único perfil** por vez.
* **Implementação:** Para atribuir ou alterar o perfil de um usuário, use sempre o método `$user->syncRoles($role)` passando o nome da role desejada. Isso removerá perfis anteriores automaticamente e associará apenas o novo.

---

## 3. Usuário Master (Super-User)
* **Finalidade:** O perfil `master` é reservado para a equipe de desenvolvimento e possui controle total sobre o sistema.
* **Resolução Automática:** Configuramos o `Gate::before` no [AuthModuleServiceProvider](file:///c:/Users/OTM%20Tech/Herd/ulo-crm-cobranca/modules/Auth/Providers/AuthModuleServiceProvider.php) para interceptar checagens e retornar `true` imediatamente para qualquer usuário com o perfil `master`:
  ```php
  Gate::before(function ($user, $ability) {
      return $user->hasRole('master') ? true : null;
  });
  ```
* **Vantagem:** Não é necessário cadastrar manualmente todas as permissões para o usuário `master` no banco de dados.

---

## 4. Uso no Front-End
Para restringir ou exibir elementos no front-end, utilize as diretivas Blade padrões do Laravel:
```html
@can('create-clients')
    <button class="btn btn-primary">Adicionar Cliente</button>
@endcan
```
