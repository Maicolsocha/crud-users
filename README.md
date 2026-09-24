# CRUD de Usuarios

Aplicación web para gestionar usuarios mediante las operaciones de creación, consulta, actualización y eliminación (CRUD).

## Tecnologías utilizadas

- Backend: PHP 8.3 y PDO.
- Base de datos: MySQL 8.
- Frontend: HTML, CSS, JavaScript y Vite.
- Comunicación: API REST con respuestas JSON.

## Requisitos

Para ejecutar el proyecto es necesario tener instalados:

- PHP 8.3 o una versión compatible, con la extensión `pdo_mysql`.
- MySQL 8 o una versión compatible.
- Node.js y npm.

Se puede utilizar Laragon para ejecutar PHP y MySQL en Windows.

## Instalación

### 1. Preparar la base de datos

Iniciar MySQL y ejecutar el archivo `database/init.sql` utilizando HeidiSQL u otro cliente de MySQL.

El script crea la base de datos `crud_usuarios` y la tabla `users`.

### 2. Configurar la conexión

Dentro de la carpeta `backend`, crear una copia de `config.example.php` y guardarla con el nombre `config.local.php`.

Modificar los datos de conexión según la configuración local de MySQL.

### 3. Instalar las dependencias

Abrir una terminal en la raíz del proyecto y ejecutar:

```bash
npm install
```

### 4. Iniciar la aplicación

Desde la raíz del proyecto, ejecutar:

```bash
npm run dev
```

Este comando inicia el servidor PHP y el servidor de desarrollo Vite.

Abrir en el navegador la dirección local indicada por Vite, normalmente:

http://localhost:5173/

## Funcionalidades

- Registrar usuarios mediante nombre y correo electrónico.
- Consultar los usuarios registrados.
- Actualizar los datos de un usuario.
- Eliminar usuarios con confirmación.
- Validar campos obligatorios y formato de correo.
- Impedir el registro de correos duplicados.
- Mostrar mensajes de éxito y error.

## API REST

| Método | Ruta | Descripción |
|---|---|---|
| GET | /api/health | Comprobar el funcionamiento de la API. |
| GET | /api/users | Consultar todos los usuarios. |
| POST | /api/users | Registrar un usuario. |
| PUT | /api/users/{id} | Actualizar un usuario. |
| DELETE | /api/users/{id} | Eliminar un usuario. |
