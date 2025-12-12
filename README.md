# 🥟 CoxinhaDelivery - Sistema de Delivery de Coxinhas

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)
![API](https://img.shields.io/badge/API-REST-brightgreen?style=for-the-badge)

> **Sistema completo de delivery especializado em coxinhas e salgados**  
> Backend robusto, pronto para mobile e web, com gestão completa de pedidos e estoque.

## 🎯 **Features Principais**

### 📦 **Gestão de Produtos**
- ✅ Catálogo com 5 categorias (coxinhas, salgados, bebidas, veganos, sobremesas)
- ✅ Controle de estoque em tempo real
- ✅ Disponibilidade automática (estoque > 0)

### 🛒 **Fluxo de Pedidos**
- ✅ Criação de pedidos com validação de estoque
- ✅ Geração automática de número único (CX202512120001)
- ✅ Acompanhamento em tempo real do status
- ✅ Cálculo automático de valores

### 🔄 **Status Inteligentes**

pendente → confirmado → em_preparo → pronto → entregue
text

- ✅ Transições controladas
- ✅ Cancelamento com devolução de estoque
- ✅ Histórico completo

### 📱 **API REST Pronta para Mobile**
- ✅ Endpoints JSON otimizados
- ✅ CORS configurado
- ✅ Stateless (sem sessões)
- ✅ Validações robustas
- ✅ Error handling padronizado

## 🏗️ **Tecnologias**

| Camada | Tecnologia |
|--------|------------|
| **Backend** | Laravel 10.x |
| **Banco de Dados** | MySQL 8.0 |
| **Cache** | Redis |
| **Busca** | Meilisearch |
| **Email** | Mailpit |
| **Container** | Docker + Laravel Sail |
| **API** | RESTful JSON |

## 🚀 **Instalação Rápida**

```bash
# 1. Clonar repositório
git clone https://github.com/seu-usuario/coxinhadelivery.git
cd coxinhadelivery

# 2. Subir ambiente Docker
./vendor/bin/sail up -d

# 3. Instalar dependências
./vendor/bin/sail composer install

# 4. Configurar ambiente
cp .env.example .env
./vendor/bin/sail artisan key:generate

# 5. Banco de dados
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed --class=ProdutosSeeder

📚 API Endpoints
Produtos
http

GET    /api/produtos          # Listar todos (com filtros)
GET    /api/produtos/{id}     # Detalhes do produto
GET    /api/produtos/categorias # Listar categorias

Pedidos (Clientes)
http

POST   /api/pedidos           # Criar novo pedido
GET    /api/pedidos/{codigo}  # Acompanhar pedido

Pedidos (Admin)
http

GET    /api/admin/pedidos     # Listar todos (com filtros)
PUT    /api/admin/pedidos/{id}/status # Atualizar status
GET    /api/admin/pedidos/{id} # Detalhes completos

🎨 Exemplo de Uso
Criar Pedido:
bash

curl -X POST http://localhost/api/pedidos \
  -H "Content-Type: application/json" \
  -d '{
    "cliente_nome": "João Silva",
    "cliente_telefone": "11999999999",
    "tipo": "entrega",
    "itens": [
      {"produto_id": 1, "quantidade": 2},
      {"produto_id": 2, "quantidade": 1}
    ]
  }'

Response:
json

{
  "success": true,
  "message": "Pedido criado com sucesso",
  "data": {
    "pedido_id": 1,
    "numero_pedido": "CX202512120001",
    "valor_total": "17.50",
    "status": "pendente"
  }
}

🗺️ Arquitetura
text

┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   Frontend      │────▶│   API REST      │────▶│   Banco de      │
│   (Mobile/Web)  │◀────│   Laravel       │◀────│   Dados MySQL   │
└─────────────────┘     └─────────────────┘     └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │   Serviços      │
                       │   (Redis,       │
                       │    Meilisearch) │
                       └─────────────────┘

📊 Modelo de Dados
🔧 Variáveis de Ambiente
env

APP_NAME=CoxinhaDelivery
APP_ENV=local
APP_KEY=
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=coxinhadelivery
DB_USERNAME=sail
DB_PASSWORD=password

🤝 Contribuindo

    Fork o projeto

    Crie sua Feature Branch (git checkout -b feature/AmazingFeature)

    Commit suas mudanças (git commit -m 'Add: AmazingFeature')

    Push para a Branch (git push origin feature/AmazingFeature)

    Abra um Pull Request

📄 Licença

Distribuído sob a licença MIT. Veja LICENSE para mais informações.
👥 Autores

    Mark - Desenvolvimento Backend - Seu GitHub

🙏 Agradecimentos

    Laravel - O framework PHP para artesãos web

    Laravel Sail - Docker para Laravel

    Todos os testadores de coxinha! 🥟

