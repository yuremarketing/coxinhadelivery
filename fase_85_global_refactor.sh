#!/bin/bash

echo "🇬🇧 [Coxinha Delivery] - Fase 85: Refatoração Global para Inglês Técnico"

# 1. Refatorar PRODUTO -> PRODUCT
echo "Refatorando Model Produto..."
mv app/Models/Produto.php app/Models/Product.php
sed -i 's/class Produto/class Product/g' app/Models/Product.php
# Mapeia para a tabela existente em português
sed -i '/class Product extends/a \    protected $table = "produtos";' app/Models/Product.php

# 2. Refatorar PEDIDO -> ORDER
echo "Refatorando Model Pedido..."
mv app/Models/Pedido.php app/Models/Order.php
sed -i 's/class Pedido/class Order/g' app/Models/Order.php
sed -i '/class Order extends/a \    protected $table = "pedidos";' app/Models/Order.php

# 3. Refatorar PEDIDOITEM -> ORDERITEM
echo "Refatorando Model PedidoItem..."
mv app/Models/PedidoItem.php app/Models/OrderItem.php
sed -i 's/class PedidoItem/class OrderItem/g' app/Models/OrderItem.php
sed -i '/class OrderItem extends/a \    protected $table = "pedido_items";' app/Models/OrderItem.php

# 4. ATUALIZAÇÃO DE REFERÊNCIAS (Controllers, Routes e Models)
echo "Atualizando referências em todo o projeto..."
# Substitui nos Controllers
find app/Http/Controllers -type f -exec sed -i 's/Produto/Product/g' {} +
find app/Http/Controllers -type f -exec sed -i 's/Pedido/Order/g' {} +
# Substitui nas Rotas
find routes -type f -exec sed -i 's/Produto/Product/g' {} +
find routes -type f -exec sed -i 's/Pedido/Order/g' {} +
# Substitui referências cruzadas nos próprios Models
find app/Models -type f -exec sed -i 's/Produto/Product/g' {} +
find app/Models -type f -exec sed -i 's/Pedido/Order/g' {} +

# 5. LIMPEZA DE CACHE DO LARAVEL
echo "Limpando cache do sistema..."
docker compose exec laravel.test php artisan optimize:clear

echo "------------------------------------------------"
echo "✅ REFATORAÇÃO GLOBAL CONCLUÍDA COM SUCESSO!"
echo "------------------------------------------------"
echo "Novos nomes ativos: Product, Order, OrderItem."
echo "------------------------------------------------"
