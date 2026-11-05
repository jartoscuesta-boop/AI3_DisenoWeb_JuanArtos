# Sistema de Control de Accesos - Módulo Web Full-Stack

**Asignatura / Proyecto:** Desarrollo Web y Bases de Datos  
**Descripción:** Módulo web full-stack desarrollado en PHP nativo con arquitectura de tres capas, conexión segura mediante PDO, uso estricto de sentencias preparadas (prevención de Inyección SQL), saneamiento contra XSS y control de versiones mediante Git.

---

## 📑 Índice de Contenidos
1. [Sección 1: Justificación Técnica](#sección-1-justificación-técnica)
2. [Sección 2: Arquitectura del Sistema](#sección-2-arquitectura-del-sistema)
3. [Sección 3: Script SQL y Evidencias de Ejecución](#sección-3-script-sql-y-evidencias-de-ejecución)
4. [Guía de Despliegue Local](#guía-de-despliegue-local)
5. [Estructura del Proyecto](#estructura-del-proyecto)

---

## Sección 1: Justificación Técnica

### 1. Análisis Comparativo: PDO vs. MySQLi
Para el desarrollo de este módulo web, se evaluaron las dos opciones nativas principales que ofrece PHP para interactuar con bases de datos relacionales: **PDO (PHP Data Objects)** y **MySQLi (MySQL Improved)**. Tras analizar sus características técnicas, se seleccionó **PDO** por las siguientes razones:
* **Portabilidad y Abstracción:** PDO permite conectarse a más de 12 sistemas de bases de datos diferentes (como PostgreSQL, SQLite, Oracle o MySQL) modificando únicamente la cadena de conexión (DSN). MySQLi está atado exclusivamente a MySQL y MariaDB, lo que limita la flexibilidad ante futuras migraciones tecnológicas.
* **Soporte de Parámetros con Nombre:** Mientras que MySQLi maneja principalmente parámetros posicionales (`?`) que pueden volverse confusos en consultas complejas, PDO permite el uso de **parámetros con nombre** (ej. `:nombre`, `:cedula`), incrementando la legibilidad y el mantenimiento del código.
* **Gestión de Errores Robusta:** PDO permite configurar el manejo de excepciones mediante bloques `try-catch` (`PDO::ERRMODE_EXCEPTION`), facilitando una depuración limpia sin exponer información crítica del servidor en la interfaz del usuario.

### 2. Medidas de Seguridad Implementadas
* **Sentencias Preparadas (Prepared Statements):** Defensa estructural contra la **Inyección SQL**. Al utilizar `prepare()` y `execute()`, el motor de base de datos compila la estructura SQL por separado de los datos del usuario, tratando cualquier intento de inyección maliciosa como una cadena de texto literal y nunca como código ejecutable.
* **Desactivación de Emulación de Preparadas:** Se configuró `PDO::ATTR_EMULATE_PREPARES => false`, forzando al motor MySQL a procesar las sentencias preparadas de manera nativa y cerrando brechas ante ataques avanzados de tipos de datos.
* **Validación de Entradas:** Se implementaron validaciones de cliente (atributos HTML5 como `required`, `maxlength` y `pattern`) y de servidor (`trim()`, verificación de campos vacíos y control de longitud).
* **Mitigación de XSS (Cross-Site Scripting):** En los módulos de visualización, cada celda extraída de la base de datos pasa por la función `htmlspecialchars()`, convirtiendo caracteres especiales en entidades HTML inofensivas y evitando la ejecución de scripts maliciosos en el navegador.

---

## Sección 2: Arquitectura del Sistema

El sistema opera bajo un modelo estructurado de **tres capas (Three-Tier Architecture)**:

1. **Capa de Presentación (Frontend / Cliente):** 
   Compuesta por las interfaces HTML5 y CSS3 externas (`style/style.css`). Se divide en dos páginas independientes: el módulo de registro (`index.php`) y el módulo de consulta (`consulta.php`), dotado de un buscador interactivo y una tabla responsiva.
2. **Capa de Lógica de Negocio y Servidor (Backend / PHP):** 
   Scripts en PHP ejecutados en Apache (XAMPP). Reciben las peticiones HTTP (`GET`/`POST`), filtran las entradas, gestionan la conexión segura mediante PDO y ejecutan las consultas preparadas.
3. **Capa de Persistencia (Base de Datos / MySQL):** 
   Gestionada por MySQL / MariaDB, almacena los registros de forma permanente en la tabla `visitantes` aplicando restricciones de integridad.

### Flujo de Datos
* **Registro (`index.php`):** El usuario envía el formulario por `POST` -> El servidor valida los campos -> Se establece conexión PDO -> Se ejecuta una sentencia SQL preparada de inserción -> Se almacena en MySQL -> Se retorna una alerta de éxito visual.
* **Consulta (`consulta.php`):** El usuario ingresa un filtro de búsqueda por `GET` -> El backend prepara una consulta con cláusula `LIKE` (o general si está vacío) -> Se extraen los registros -> Se sanitizan con `htmlspecialchars()` -> Se renderiza la tabla responsiva en pantalla.

---

## Sección 3: Script SQL y Evidencias de Ejecución

### Script de Creación de Base de Datos y Tabla
Ejecuta el siguiente código en tu gestor de base de datos (phpMyAdmin, DBeaver o Workbench):

```sql
CREATE DATABASE IF NOT EXISTS control_accesos;
USE control_accesos;

CREATE TABLE IF NOT EXISTS visitantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) NOT NULL,
    motivo_visita TEXT NOT NULL,
    persona_a_visitar VARCHAR(100) NOT NULL,
    hora_ingreso DATETIME DEFAULT CURRENT_TIMESTAMP
);