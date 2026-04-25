# API Kanbam - Documentação para IA Assistente

Base URL: `https://projetos.patropicomunica.com.br`

## Autenticação

Todas as requisições devem incluir o header:

```
X-API-Token: kanbam-api-token-secret-2024
```

**Exemplo de requisição:**
```bash
curl -H "X-API-Token: kanbam-api-token-secret-2024" https://projetos.patropicomunica.com.br/api/clients
```

---

## Endpoints

### 1. Listar Clientes

**Apenas leitura (GET)** - A IA pode apenas listar, sem editar/adicionar/remover.

```
GET /api/clients
```

**Response:**
```json
{
  "clients": [
    {
      "id": 1,
      "name": "Empresa X",
      "tag": "EX",
      "responsible_name": "João Silva",
      "responsible_email": "joao@empresax.com.br",
      "color": "#FF5733",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

---

### 2. Ver Cliente por ID

```
GET /api/clients/:id
```

**Parâmetros:**
- `:id` -	ID do cliente

**Exemplo:** `GET /api/clients/1`

**Response:**
```json
{
  "client": {
    "id": 1,
    "name": "Empresa X",
    "tag": "EX",
    "responsible_name": "João Silva",
    "responsible_email": "joao@empresax.com.br",
    "color": "#FF5733",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

---

### 3. Ver Cliente por Tag

```
GET /api/clients/tag/:tag
```

**Parâmetros:**
- `:tag` - Tag do cliente (ex: "EX")

**Exemplo:** `GET /api/clients/tag/EX`

**Response:**
```json
{
  "client": {
    "id": 1,
    "name": "Empresa X",
    "tag": "EX",
    "responsible_name": "João Silva",
    "responsible_email": "joao@empresax.com.br",
    "color": "#FF5733",
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

---

### 4. Listar Usuários

**Apenas leitura (GET)** - A IA pode apenas listar, sem editar/adicionar/remover.

```
GET /api/users
```

**Response:**
```json
{
  "users": [
    {
      "id": 1,
      "name": "Maria Silva",
      "initials": "MA",
      "color": "#FF5733",
      "is_admin": true
    }
  ]
}
```

---

### 5. Ver Usuário por ID

```
GET /api/users/:id
```

**Parâmetros:**
- `:id` -	ID do usuário

**Exemplo:** `GET /api/users/1`

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Maria Silva",
    "initials": "MA",
    "color": "#FF5733",
    "is_admin": true
  }
}
```

---

### 6. Listar Projetos

**Apenas leitura (GET)** - A IA pode apenas listar, sem editar/adicionar/remover.

```
GET /api/projects
```

**Parâmetros opcionais (query string):**
- `?client_id=1` - Filtrar projetos por cliente
- `?status=active` - Filtrar projetos por status
- `?client_id=1&status=active` - Combinar filtros (ex: todos projetos ativos do cliente)

**Status válidos:**
- `active` - Projeto ativo
- `concluded` - Projeto concluído

**Exemplos de uso:**
```
GET /api/projects?client_id=1
GET /api/projects?status=active
GET /api/projects?client_id=1&status=active
```

**Response:**
```json
{
  "projects": [
    {
      "id": 1,
      "name": "Website Empresa X",
      "status": "active",
      "is_visible_to_client": true,
      "client_id": 1,
      "client_name": "Empresa X",
      "client_tag": "EX"
    }
  ]
}
```

---

### 7. Ver Detalhes de um Projeto

```
GET /api/projects/:id
```

**Parâmetros:**
- `:id` -	ID do projeto

**Exemplo:** `GET /api/projects/1`

**Response:**
```json
{
  "project": {
    "id": 1,
    "name": "Website Empresa X",
    "description": "Site institucional",
    "status": "active",
    "client_id": 1,
    "client_name": "Empresa X",
    "client_tag": "EX",
    "client_color": "#FF5733",
    "is_visible_to_client": true,
    "created_at": "2024-01-15T10:30:00Z"
  }
}
```

---

### 8. Listar Tarefas

**A IA tem controle total** - Pode criar, editar, listar e remover.

```
GET /api/tasks
```

**Parâmetros opcionais (query string):**
- `?project_id=1` - Filtrar tarefas por projeto
- `?status=em desenvolvimento` - Filtrar por status
- `?user_id=1` - Filtrar por usuário

**Response:**
```json
{
  "tasks": [
    {
      "id": 1,
      "project_id": 1,
      "project_name": "Website Empresa X",
      "title": "Criar layout home",
      "description": "Criar proposal inicial",
      "status": "não iniciadas",
      "user_id": 2,
      "user_name": "Maria Silva",
      "user_initials": "MA",
      "user_color": "#FF5733",
      "due_date": "2024-02-01",
      "created_at": "2024-01-15T10:30:00Z"
    }
  ]
}
```

---

### 9. Ver Detalhes de uma Tarefa

```
GET /api/tasks/:id
```

**Parâmetros:**
- `:id` - ID da tarefa

**Exemplo:** `GET /api/tasks/1`

**Response:**
```json
{
  "task": {
    "id": 1,
    "project_id": 1,
    "project_name": "Website Empresa X",
    "title": "Criar layout home",
    "description": "Criar proposal inicial",
    "status": "não iniciadas",
    "user_id": 2,
    "user_name": "Maria Silva",
    "user_initials": "MA",
    "user_color": "#FF5733",
    "due_date": "2024-02-01",
    "created_at": "2024-01-15T10:30:00Z"
  },
  "notes": [
    {
      "id": 1,
      "task_id": 1,
      "user_id": 2,
      "user_name": "Maria",
      "user_initials": "MA",
      "note": "Precisa revisar com o cliente",
      "created_at": "2024-01-16T14:00:00Z"
    }
  ]
}
```

---

### 10. Criar Tarefa

```
POST /api/tasks
```

**Headers:**
```
Content-Type: application/json
X-API-Token: kanbam-api-token-secret-2024
```

**Body:**
```json
{
  "project_id": 1,
  "title": "Título da tarefa",
  "description": "Descrição opcional",
  "status": "não iniciadas",
  "user_id": 2,
  "due_date": "2024-02-01"
}
```

**Campos obrigatórios:**
- `project_id` - ID do projeto
- `title` - Título da tarefa

**Campos opcionais:**
- `description` - Descrição da tarefa
- `status` - Status inicial (padrão: "não iniciadas")
- `user_id` - ID do usuário responsável
- `due_date` - Data de entrega (formato: YYYY-MM-DD)

**Status válidos:**
- `não iniciadas`
- `em desenvolvimento`
- `aprovação`
- `com cliente`
- `ajustes`
- `aprovada`
- `implementada`
- `concluída`
- `cancelada`

**Response (201 Created):**
```json
{
  "task": {
    "id": 5,
    "project_id": 1,
    "title": "Título da tarefa",
    "status": "não iniciadas",
    "created_at": "2024-01-20T10:00:00Z"
  },
  "message": "Task created"
}
```

---

### 11. Editar Tarefa

```
PUT /api/tasks/:id
```

**Parâmetros:**
- `:id` - ID da tarefa

**Headers:**
```
Content-Type: application/json
X-API-Token: kanbam-api-token-secret-2024
```

**Body (envie apenas os campos que deseja alterar):**
```json
{
  "title": "Novo título",
  "description": "Nova descrição",
  "status": "em desenvolvimento",
  "user_id": 3,
  "due_date": "2024-02-15"
}
```

**Response:**
```json
{
  "task": {
    "id": 1,
    "title": "Novo título",
    "status": "em desenvolvimento"
  },
  "message": "Task updated"
}
```

---

### 12. Remover Tarefa

```
DELETE /api/tasks/:id
```

**Parâmetros:**
- `:id` - ID da tarefa

**Response:**
```json
{
  "message": "Task deleted"
}
```

---

### 13. Listar Notas de uma Tarefa

```
GET /api/tasks/:id/notes
```

**Parâmetros:**
- `:id` - ID da tarefa

**Response:**
```json
{
  "notes": [
    {
      "id": 1,
      "task_id": 1,
      "user_id": 2,
      "user_name": "Maria",
      "user_initials": "MA",
      "note": "Precisa revisar com o cliente",
      "created_at": "2024-01-16T14:00:00Z"
    }
  ]
}
```

---

### 14. Criar Nota

```
POST /api/tasks/:id/notes
```

**Parâmetros:**
- `:id` - ID da tarefa

**Headers:**
```
Content-Type: application/json
X-API-Token: kanbam-api-token-secret-2024
```

**Body:**
```json
{
  "note": "Texto da nota",
  "user_id": 1
}
```

**Campos obrigatórios:**
- `note` - Texto da nota (máximo 180 caracteres)

**Campos opcionais:**
- `user_id` - ID do usuário que criou a nota (padrão: 1)

**Response (201 Created):**
```json
{
  "note": {
    "id": 3,
    "task_id": 1,
    "user_id": 1,
    "note": "Texto da nota",
    "created_at": "2024-01-20T10:00:00Z"
  },
  "message": "Note created"
}
```

---

### 15. Remover Nota

```
DELETE /api/notes/:id
```

**Parâmetros:**
- `:id` - ID da nota

**Response:**
```json
{
  "message": "Note deleted"
}
```

---

## Códigos de Erro

| Código | Significado |
|--------|-------------|
| 400 | Bad Request - Dados inválidos |
| 401 | Unauthorized - Token inválido ou ausente |
| 404 | Not Found - Recurso não encontrado |

**Exemplo de erro:**
```json
{
  "error": "Task not found"
}
```

---

## Exemplos de Uso

### Criar uma nova tarefa para o Projeto 1:
```bash
curl -X POST https://projetos.patropicomunica.com.br/api/tasks \
  -H "Content-Type: application/json" \
  -H "X-API-Token: kanbam-api-token-secret-2024" \
  -d '{"project_id": 1, "title": "Criar logo", "due_date": "2024-02-01"}'
```

### Alterar o status de uma tarefa:
```bash
curl -X PUT https://projetos.patropicomunica.com.br/api/tasks/5 \
  -H "Content-Type: application/json" \
  -H "X-API-Token: kanbam-api-token-secret-2024" \
  -d '{"status": "em desenvolvimento"}'
```

### Adicionar uma nota a uma tarefa:
```bash
curl -X POST https://projetos.patropicomunica.com.br/api/tasks/5/notes \
  -H "Content-Type: application/json" \
  -H "X-API-Token: kanbam-api-token-secret-2024" \
  -d '{"note": "Cliente aprova o layout inicial"}'
```

### Ver todas as tarefas de um projeto:
```bash
curl -H "X-API-Token: kanbam-api-token-secret-2024" \
  "https://projetos.patropicomunica.com.br/api/tasks?project_id=1"
```

---

## Notas

- As datas devem ser envias no formato `YYYY-MM-DD` (ex: `2024-02-01`)
- Os campos de data não são obrigatórios
- Ao buscar tarefas com `GET /api/tasks/:id`, as notas são retornadas junto com a tarefa
- Não é possível adicionar/edita/remover clientes ou projetos via API (apenas listar)