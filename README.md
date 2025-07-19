# README

## Visão Geral

Esta API permite gerenciar pedidos de viagem, incluindo criação, listagem, visualização, atualização de status e cancelamento. A API utiliza autenticação baseada em usuários e diferentes níveis de permissão.

## Autenticação

Todos os endpoints requerem autenticação. O usuário autenticado é obtido através de `Auth::user()`.

### Níveis de Permissão
- **Admin**: Acesso completo a todos os pedidos
- **Usuário comum**: Acesso apenas aos próprios pedidos

---

## Endpoints

### 1. Listar Pedidos de Viagem

**GET** `/travel-requests`

Lista todos os pedidos de viagem com filtros opcionais. Apenas para Administradores!

#### Parâmetros de Query (Filtros)

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `status` | string | Filtrar por status do pedido |
| `destination` | string | Filtrar por destino |
| `departure_date_from` | date | Data de partida inicial |
| `departure_date_to` | date | Data de partida final |
| `return_date_from` | date | Data de retorno inicial |
| `return_date_to` | date | Data de retorno final |
| `user_id` | integer | ID do usuário |
| `created_from` | date | Data de criação inicial |
| `created_to` | date | Data de criação final |
| `order_by` | string | Campo para ordenação |
| `order_direction` | string | Direção da ordenação (asc/desc) |
| `per_page` | integer | Itens por página (padrão: 15) |

#### Resposta de Sucesso (200)

```json
{
  "success": true,
  "data": [
    {
      "id": "",
      "user_id": "",
      "destination": "São Paulo",
      "departure_date": "2024-12-15",
      "return_date": "2024-12-20",
      "status": "pending",
      "created_at": "2024-12-01T10:00:00Z",
      "updated_at": "2024-12-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

#### Resposta de Erro (500)

```json
{
  "success": false,
  "message": "Erro ao buscar pedidos de viagem",
  "error": "Mensagem de erro detalhada"
}
```

---

### 2. Criar Pedido de Viagem

**POST** `/travel-requests`

Cria um novo pedido de viagem.

#### Corpo da Requisição

O corpo deve seguir as regras definidas em `StoreTravelRequestRequest`. Exemplo:

```json
{
  "destination": "Rio de Janeiro",
  "departure_date": "2024-12-20",
  "return_date": "2024-12-25",
}
```

#### Resposta de Sucesso (201)

```json
{
  "success": true,
  "message": "Pedido de viagem criado com sucesso",
  "data": {
    "id": 1,
    "user_id": 1,
    "destination": "Rio de Janeiro",
    "departure_date": "2024-12-20",
    "return_date": "2024-12-25",
    "status": "pending",
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@example.com"
    },
    "created_at": "2024-12-01T10:00:00Z",
    "updated_at": "2024-12-01T10:00:00Z"
  }
}
```

#### Resposta de Erro - Dados Inválidos (422)

```json
{
  "success": false,
  "message": "Dados inválidos",
  "error": "Mensagem de erro de validação"
}
```

#### Resposta de Erro - Servidor (500)

```json
{
  "success": false,
  "message": "Erro ao criar pedido de viagem",
  "error": "Mensagem de erro detalhada"
}
```

---

### 3. Visualizar Pedido Específico

**GET** `/travel-requests/{id}`

Retorna os detalhes de um pedido de viagem específico.

#### Parâmetros de URL

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id` | string | ID do pedido de viagem |

#### Regras de Acesso
- **Admin**: Pode visualizar qualquer pedido
- **Usuário comum**: Pode visualizar apenas seus próprios pedidos

#### Resposta de Sucesso (200)

```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": 1,
    "destination": "São Paulo",
    "departure_date": "2024-12-15",
    "return_date": "2024-12-20",
    "status": "approved",
    "created_at": "2024-12-01T10:00:00Z",
    "updated_at": "2024-12-05T14:30:00Z"
  }
}
```

#### Resposta de Erro - Não Encontrado (404)

```json
{
  "success": false,
  "message": "Pedido de viagem não encontrado"
}
```

#### Resposta de Erro - Acesso Negado (403)

```json
{
  "success": false,
  "message": "Acesso negado"
}
```

---

### 4. Atualizar Status do Pedido

**PUT** `/travel-requests/{id}/status`

Atualiza o status de um pedido de viagem.

#### Parâmetros de URL

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id` | string | ID do pedido de viagem |

#### Corpo da Requisição

```json
{
  "status": "approved"
}
```

Os valores válidos para `status` devem seguir as regras definidas em `UpdateTravelRequestRequest`.

#### Resposta de Sucesso (200)

```json
{
  "success": true,
  "message": "Status do pedido atualizado com sucesso",
  "data": {
    "id": 1,
    "status": "approved",
    "updated_at": "2024-12-05T14:30:00Z",
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@example.com"
    }
  }
}
```

#### Resposta de Erro (400)

```json
{
  "success": false,
  "message": "Erro ao atualizar status do pedido",
  "error": "Mensagem de erro detalhada"
}
```

---

### 5. Cancelar Pedido Aprovado

**PUT** `/travel-requests/{id}/cancel-approved`

Cancela um pedido de viagem que foi previamente aprovado.

#### Parâmetros de URL

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id` | string | ID do pedido de viagem |

#### Resposta de Sucesso (200)

```json
{
  "success": true,
  "message": "Pedido aprovado cancelado com sucesso",
  "data": {
    "id": 1,
    "status": "cancelled",
    "updated_at": "2024-12-05T16:45:00Z",
    "user": {
      "id": 1,
      "name": "João Silva",
      "email": "joao@example.com"
    }
  }
}
```

#### Resposta de Erro (400)

```json
{
  "success": false,
  "message": "Erro ao cancelar pedido aprovado",
  "error": "Mensagem de erro detalhada"
}
```

---

### 6. Meus Pedidos

**GET** `/my-travel-requests`

Lista os pedidos de viagem do usuário autenticado.

#### Parâmetros de Query

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `status` | string | Filtrar por status |
| `per_page` | integer | Itens por página (padrão: 15) |

#### Resposta de Sucesso (200)

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "destination": "São Paulo",
      "departure_date": "2024-12-15",
      "return_date": "2024-12-20",
      "status": "pending",
      "created_at": "2024-12-01T10:00:00Z",
      "updated_at": "2024-12-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 15,
    "total": 23
  }
}
```

---

## Códigos de Status HTTP

| Código | Descrição |
|--------|-----------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 400 | Requisição inválida |
| 403 | Acesso negado |
| 404 | Não encontrado |
| 422 | Dados de entrada inválidos |
| 500 | Erro interno do servidor |

---

## Estrutura Padrão de Resposta

### Sucesso
```json
{
  "success": true,
  "message": "Mensagem de sucesso (opcional)",
  "data": {}, // Dados da resposta
  "meta": {} // Metadados para paginação (quando aplicável)
}
```

### Erro
```json
{
  "success": false,
  "message": "Mensagem de erro",
  "error": "Detalhes técnicos do erro (opcional)"
}
```

---

## Observações Técnicas

1. **Injeção de Dependência**: O controller utiliza injeção de dependência do `TravelRequestService`
2. **DTOs**: Utiliza `TravelRequestDTO` para transferência de dados
3. **Form Requests**: Utiliza `StoreTravelRequestRequest` e `UpdateTravelRequestRequest` para validação
4. **Relacionamentos**: Carrega automaticamente o relacionamento `user` em algumas operações
5. **Paginação**: Utiliza paginação padrão do Laravel com metadados de navegação
6. **Tratamento de Exceções**: Implementa tratamento robusto de exceções com diferentes tipos de erro

---

## Dependências

- **TravelRequestService**: Serviço que contém a lógica de negócio
- **TravelRequestDTO**: Objeto de transferência de dados
- **StoreTravelRequestRequest**: Validação para criação de pedidos
- **UpdateTravelRequestRequest**: Validação para atualização de status
- **Laravel Auth**: Sistema de autenticação do Laravel