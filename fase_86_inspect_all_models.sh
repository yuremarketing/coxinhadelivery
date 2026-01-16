#!/bin/bash

echo "🔍 [Coxinha Delivery] - Fase 86: Inspeção Geral de Models (Pós-Refatoração)"
echo "----------------------------------------------------------------------"

# Lista todos os arquivos na pasta Models, remove a extensão .php
MODELS=$(ls app/Models | sed 's/\.php//g')

for MODEL in $MODELS
do
    echo -e "\n📊 ESTRUTURA DO MODEL: $MODEL"
    # Executa o show do model dentro do container
    docker compose exec laravel.test php artisan model:show $MODEL
    echo "----------------------------------------------------------------------"
done

echo "✅ Inspeção concluída!"
