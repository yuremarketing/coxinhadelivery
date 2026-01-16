#!/bin/bash

OUTPUT="contexto_frontend.txt"

# Limpa arquivo anterior
> "$OUTPUT"

echo "🔍 Iniciando auditoria da estrutura FRONTEND..."

# Função para adicionar arquivo ao relatório
add_file() {
    if [ -f "$1" ]; then
        echo "=========================================" >> "$OUTPUT"
        echo "ARQUIVO: $1" >> "$OUTPUT"
        echo "=========================================" >> "$OUTPUT"
        cat "$1" >> "$OUTPUT"
        echo -e "\n\n" >> "$OUTPUT"
        echo "✅ Lido: $1"
    else
        echo "⚠️ Não encontrado (ignorando): $1"
    fi
}

# 1. ARQUIVOS DE CONFIGURAÇÃO NA RAIZ
echo "--- Lendo configurações ---"
add_file "package.json"
add_file "vite.config.js"
add_file "webpack.mix.js"
add_file "tailwind.config.js"
add_file "postcss.config.js"

# 2. ARQUIVOS DENTRO DE RESOURCES (JS, JSX, CSS, BLADE)
echo "--- Varrendo pasta resources ---"
if [ -d "resources" ]; then
    find resources -type f \
        \( -name "*.js" -o -name "*.jsx" -o -name "*.ts" -o -name "*.tsx" -o -name "*.css" -o -name "*.blade.php" \) \
        -print0 | while IFS= read -r -d '' file; do
        
        echo "=========================================" >> "$OUTPUT"
        echo "ARQUIVO: $file" >> "$OUTPUT"
        echo "=========================================" >> "$OUTPUT"
        cat "$file" >> "$OUTPUT"
        echo -e "\n\n" >> "$OUTPUT"
        echo "📄 Adicionado: $file"
    done
else
    echo "❌ Pasta 'resources' não encontrada!"
fi

echo "---------------------------------------------------"
echo "🏁 Auditoria concluída! O arquivo '$OUTPUT' foi gerado."
