#!/bin/bash

# Nome do arquivo de saída
OUTPUT="projeto_laravel_completo.txt"

# Limpa o arquivo se já existir
> "$OUTPUT"

echo "Gerando contexto do projeto Laravel..."

# Encontra arquivos relevantes, ignorando pastas pesadas e arquivos binários
find . -type f \
    -not -path '*/vendor/*' \
    -not -path '*/node_modules/*' \
    -not -path '*/.git/*' \
    -not -path '*/storage/*' \
    -not -path '*/public/*' \
    -not -name 'package-lock.json' \
    -not -name 'composer.lock' \
    -not -name '*.log' \
    -not -name '*.png' \
    -not -name '*.jpg' \
    -not -name '*.jpeg' \
    -not -name '*.ico' \
    -not -name '*.zip' \
    \( -name "*.php" -o -name "*.json" -o -name "*.js" -o -name "*.yml" -o -name "*.xml" \) \
    -print0 | while IFS= read -r -d '' file; do
    
    echo "Adicionando: $file"
    
    # Cabeçalho para eu saber qual arquivo é qual
    echo "=========================================" >> "$OUTPUT"
    echo "ARQUIVO: $file" >> "$OUTPUT"
    echo "=========================================" >> "$OUTPUT"
    
    # Conteúdo do arquivo
    cat "$file" >> "$OUTPUT"
    
    # Quebra de linha
    echo -e "\n\n" >> "$OUTPUT"
done

echo "Concluído! O arquivo '$OUTPUT' foi criado com sucesso."
