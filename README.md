# SISTEMA-DOCENTES

Sistema de Gestión Académica y Proyectos Docentes.

![Build Status](https://github.com/tu-usuario/SISTEMA-DOCENTES/actions/workflows/ci.yml/badge.svg)
![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)
![Laravel](https://img.shields.io/badge/laravel-11.x-red)

## Descripción
Plataforma web para la administración de proyectos, innovaciones educativas y evaluaciones de desempeño docente. Incluye roles de seguridad (Admin, Coordinador, Docente) y métricas de calidad integradas (ISO/IEC 25010).

## Requisitos del Sistema
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL o MariaDB
- Extensiones PHP: `pdo_sqlite` (para pruebas), `fileinfo`, `zip`.

## Instalación (Entorno Local)

Siga estos pasos para desplegar el proyecto en un entorno de desarrollo (ej. Laragon/XAMPP):

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/SISTEMA-DOCENTES.git
   cd SISTEMA-DOCENTES
   ```

2. **Instalar dependencias Backend y Frontend**
   ```bash
   composer install
   npm install
   ```

3. **Configurar entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edite el archivo `.env` con sus credenciales de base de datos (`DB_DATABASE`, `DB_USERNAME`, etc.).*

4. **Base de Datos y Seeders**
   ```bash
   php artisan migrate --seed
   ```
   *Esto creará las tablas y usuarios iniciales (Admin, Docentes de prueba).*

5. **Iniciar servidor**
   ```bash
   npm run dev
   # En otra terminal:
   php artisan serve
   ```

## Calidad y Pruebas (Instrucciones)

Este proyecto cuenta con un plan de aseguramiento de calidad automatizado.

### Ejecutar Pruebas Automatizadas
Para verificar la funcionalidad (Login, CRUD Proyectos, Innovaciones, Roles):
```bash
php artisan test
```

### Análisis Estático de Código
Para medir la complejidad y detectar errores sin ejecutar la app (Nivel 5):
```bash
./vendor/bin/phpstan analyse --memory-limit=2G
```

## Integración Continua (CI/CD)
El proyecto incluye un pipeline en `.github/workflows/ci.yml` que ejecuta automáticamente:
1.  Linter (Laravel Pint).
2.  Tests Unitarios y de Integración (PHPUnit).
3.  Análisis Estático (Larastan).

Cualquier Pull Request fallará si no cumple con estos estándares.

## Credenciales de Acceso (Entorno Pruebas)
- **Admin:** `admin@example.com` / `password`
- **Coordinador:** `coordinator@example.com` / `password`
- **Docente:** `teacher@example.com` / `password`
