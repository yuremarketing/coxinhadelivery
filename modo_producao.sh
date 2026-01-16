#!/bin/bash

echo "🚀 Iniciando transformação para Ambiente de Staging..."

# 1. Detectar IP da Rede Local (pega o primeiro IP disponível)
MEU_IP=$(hostname -I | awk '{print $1}')
echo "📍 IP Local Detectado: $MEU_IP"

# 2. Backup de Segurança
cp .env .env.backup_dev
echo "💾 Backup salvo em: .env.backup_dev (Se der ruim, volte aqui)"

# 3. Alterar .env para Produção (Usando SED para substituir texto)
# Muda para production
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
# Desliga o Debug
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
# Atualiza a URL para o IP da rede (para o celular aceitar)
sed -i "s|APP_URL=http://localhost|APP_URL=http://$MEU_IP|" .env

echo "⚙️  Arquivo .env configurado para Produção."

# 4. Compilar Front-end (Minificar CSS e JS)
echo "📦 Compilando arquivos (Build)... Aguarde."
./vendor/bin/sail npm run build

# 5. Otimizar Laravel (Cache de rotas e config)
echo "⚡ Otimizando performance do Back-end..."
./vendor/bin/sail artisan optimize:clear
./vendor/bin/sail artisan optimize

echo ""
echo "✅ AMBIENTE PRONTO!"
echo "------------------------------------------------"
echo "📱 No celular (mesmo Wifi), acesse: http://$MEU_IP"
echo "------------------------------------------------"
