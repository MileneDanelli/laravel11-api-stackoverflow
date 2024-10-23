# Laravel 11 API Stack Overflow

Este es un proyecto de una API con Laravel 11 que interactúa con la API pública de Stack Overflow. Permite obtener datos sobre las preguntas de los foros de StackOverflow.

## Requisitos Previos

Antes de comenzar, necesitarás tener instalado el Docker.

## Configuración del Proyecto

### 1. Clona el Repositorio

Clona el repositorio en tu máquina local:

```bash
git clone https://github.com/MileneDanelli/laravel11-api-stackoverflow.git
cd laravel11-api-stackoverflow

### 3. Ejecuta las migraciones

./vendor/bin/sail artisan migrate

### 2. Inicia los contenedores

./vendor/bin/sail up

### 4. Ejemplo de Solicitud

http://localhost/api/stackoverflow/questions?tagged=vuejs&fromdate=2024-01-01&todate=2024-01-02

