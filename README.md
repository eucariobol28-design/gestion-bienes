# Sistema de Gestión de Bienes

Aplicación web para el registro y control de bienes de la Aldea Universitaria, desarrollada en PHP con arquitectura MVC.

## Características

- Autenticación de usuarios con roles (admin/operador)
- CRUD completo de bienes
- Dashboard con estadísticas
- Protección CSRF
- Interfaz responsiva con Bootstrap 5
- Base de datos SQLite (fácil migración a MySQL)

## Requisitos

- PHP 8.0 o superior
- Extensión PDO habilitada
- Servidor web (Apache/Nginx) o PHP embebido

## Instalación

1. Clona el repositorio:
   ```bash
   git clone <url-del-repo>
   cd app-web-gestionde-bienes
   ```

2. Instala dependencias (si usas Composer):
   ```bash
   composer install
   ```

3. Ejecuta la migración para crear la base de datos:
   ```bash
   php app/migrate.php
   ```

4. Configura el servidor web para apuntar a `public/index.php` o usa PHP embebido:
   ```bash
   php -S localhost:8000 -t public
   ```

5. Accede a `http://localhost:8000` y loguéate con:
   - Email: admin@aldea.local
   - Contraseña: Admin1234!

## Estructura del proyecto

```
app/
├── bootstrap.php          # Autoloader y configuración inicial
├── Config/
│   └── config.php         # Configuración de la aplicación
├── Controllers/           # Controladores MVC
├── Core/                  # Clases núcleo (Database, Router, Auth, etc.)
├── Models/                # Modelos de datos
└── Views/                 # Vistas y layouts
public/
├── index.php              # Punto de entrada
├── assets/                # CSS, JS, imágenes
└── .htaccess              # Reglas de reescritura
storage/                   # Base de datos SQLite
```

## Uso

- **Dashboard**: Vista general con estadísticas.
- **Bienes**: Lista, crear, editar y eliminar bienes.
- **Autenticación**: Login/logout con roles.

## Desarrollo

- Ejecuta tests: `composer test`
- Linting: `composer lint`

## Contribución

1. Crea una rama para tu feature.
2. Escribe tests para cambios.
3. Envía un pull request.

## Licencia

[MIT License](LICENSE)</content>
<parameter name="filePath">/home/infocentro/Escritorio/app-web-gestionde-bienes/README.md# app-gestion-bienes
# gestion-bienes
# gestion-bienes
