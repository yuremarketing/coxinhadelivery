#!/bin/bash
echo "=========================================="
echo "🔍 DIAGNÓSTICO DO SISTEMA - COXINHA DELIVERY"
echo "=========================================="

echo ""
echo "📂 1. ESTRUTURA DE PASTAS (VIEWS E CONTROLLERS)"
echo "------------------------------------------"
find resources/views -name "*.php"
echo "------------------------------------------"
find app/Http/Controllers -name "*.php"

echo ""
echo "🛣️ 2. ARQUIVO DE ROTAS (WEB.PHP)"
echo "------------------------------------------"
cat routes/web.php

echo ""
echo "🧠 3. CONTROLLER DA COZINHA (ADMIN)"
echo "------------------------------------------"
# Tenta ler o arquivo, se não existir avisa
if [ -f app/Http/Controllers/Admin/PedidoController.php ]; then
    cat app/Http/Controllers/Admin/PedidoController.php
else
    echo "❌ Arquivo Admin/PedidoController.php não encontrado!"
fi

echo ""
echo "📱 4. CONTROLLER DO PEDIDO (API/BALCÃO)"
echo "------------------------------------------"
if [ -f app/Http/Controllers/PedidoController.php ]; then
    cat app/Http/Controllers/PedidoController.php
else
    echo "❌ Arquivo PedidoController.php não encontrado!"
fi

echo ""
echo "📦 5. MODEL PEDIDO"
echo "------------------------------------------"
cat app/Models/Pedido.php

echo ""
echo "=========================================="
echo "FIM DO RELATÓRIO"
