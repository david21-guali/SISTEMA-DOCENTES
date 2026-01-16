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

4. **Base de Datos y Almacenamiento**
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```
   *Esto creará las tablas, usuarios iniciales y el enlace simbólico para archivos cargados.*

5. **Iniciar servidor**
   ```bash
   npm run dev
   # En otra terminal:
   php artisan serve
   ```

## Arquitectura de Seguridad y Calidad

Este proyecto implementa un modelo de seguridad robusto y escalable:

- **Políticas de Laravel (Policies):** El control de acceso a Proyectos, Tareas y Reuniones está centralizado en clases Policy, garantizando una gestión de permisos granular.
- **Autorización en FormRequests:** La seguridad se valida antes de la lógica del controlador, bloqueando usuarios no autorizados antes de procesar cualquier entrada.
- **Observadores (Observers):** Lógica automatizada para notificaciones y recalibración de métricas (ej. progreso de proyectos) gatillada por eventos del modelo.

## Calidad y Pruebas

### Ejecutar Pruebas Automatizadas
Para verificar funcionalidad y seguridad (Roles, Políticas, CRUD):
```bash
php artisan test
```

### Análisis Estático de Código (PHPStan)
El proyecto mantiene un estricto estándar de calidad (Nivel 7):
```bash
vendor\bin\phpstan analyze
```

## Integración Continua (CI/CD)
El proyecto incluye un pipeline en `.github/workflows/ci.yml` que ejecuta:
1.  Linter (Laravel Pint).
2.  Análisis Estático (Larastan/PHPStan Nivel 7).
3.  Pruebas de Seguridad y Funcionalidad.

Cualquier Pull Request fallará si no cumple con estos estándares.

## Credenciales de Acceso (Entorno Pruebas)
- **Admin:** `admin@example.com` / `password`
- **Coordinador:** `coordinator@example.com` / `password`
- **Docente:** `teacher@example.com` / `password`
