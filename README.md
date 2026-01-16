# 🥟 CoxinhaDelivery - Sistema de Delivery

![Tests](https://github.com/yuremarketing/coxinhadelivery/actions/workflows/laravel-tests.yml/badge.svg)
![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-005C84?style=for-the-badge&logo=mysql&logoColor=white)

> **Sistema especializado em delivery com foco em performance e integridade de dados.**
> Backend robusto em Laravel 11, com automação de estoque e pipeline de testes (CI/CD).

## 🎯 Diferenciais Técnicos
- **Estoque Inteligente**: Baixa automática via Model Events.
- **Identificação Única**: Pedidos padronizados (Ex: CX202601160001).
- **CI/CD Pipeline**: GitHub Actions configurado para testes automatizados.

## 🏗️ Tecnologias
- **Framework**: Laravel 11.x (PHP 8.3)
- **Banco de Dados**: MySQL 8.0
- **Build Tool**: Vite 7.x
- **Ambiente**: Docker (Laravel Sail)

## 🚀 Instalação Rápida
1. git clone https://github.com/yuremarketing/coxinhadelivery.git
2. ./vendor/bin/sail up -d
3. ./vendor/bin/sail composer install
4. ./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
5. cp .env.example .env && ./vendor/bin/sail artisan key:generate
6. ./vendor/bin/sail artisan migrate:fresh --seed
7. ./vendor/bin/sail artisan test --filter Unit

---
**Desenvolvido por Yure (Mark) - 2026**
