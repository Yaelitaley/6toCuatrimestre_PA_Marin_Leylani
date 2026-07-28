# Sistema Gestor de Alumnos con API REST

Proyecto para la Tarea 5 (Segundo Parcial) — ITES René Descartes.

Stack: **PHP (APIs REST) + MySQL + JavaScript (Fetch API) + HTML/CSS**.

## Instalación

1. Instala un servidor con PHP y MySQL (XAMPP, WAMP, Laragon, etc.).
2. Copia toda esta carpeta (`gestor_alumnos_api`) dentro de tu carpeta de proyectos
   (por ejemplo `htdocs` en XAMPP).
3. Importa la base de datos: abre phpMyAdmin (o el cliente MySQL de tu preferencia)
   y ejecuta el script `gestor_alumnos.sql`. Esto crea la base `gestor_alumnos`
   con sus tablas y datos de ejemplo.
4. Revisa las credenciales en `config/db.php` (por defecto usuario `root` sin
   contraseña, típico de XAMPP). Ajusta `$user` y `$pass` si tu entorno es distinto.
5. Levanta Apache/MySQL y abre en el navegador:
   `http://localhost/gestor_alumnos_api/index.html`

## Estructura del proyecto

```
gestor_alumnos_api/
├── config/
│   ├── db.php          -> Conexión PDO a MySQL
│   └── helpers.php      -> Respuestas JSON, headers CORS, validaciones
├── api/
│   ├── carreras.php     -> GET todas / GET por id
│   ├── cuatrimestres.php-> GET todos
│   ├── materias.php     -> GET todas / buscar nombre / filtrar carrera / cuatrimestre
│   ├── alumnos.php      -> GET, POST, PUT, PATCH, DELETE
│   └── inscripciones.php-> GET, POST, DELETE
├── css/style.css
├── js/app.js
├── index.html
├── gestor_alumnos.sql
└── README.md
```

## Endpoints

### `api/carreras.php`
| Método | Parámetros | Descripción |
|---|---|---|
| GET | — | Obtener todas las carreras |
| GET | `?id=1` | Obtener una carrera por ID |

### `api/cuatrimestres.php`
| Método | Descripción |
|---|---|
| GET | Obtener todos los cuatrimestres |

### `api/materias.php`
| Método | Parámetros | Descripción |
|---|---|---|
| GET | — | Todas las materias |
| GET | `?nombre=texto` | Buscar por nombre |
| GET | `?id_carrera=1` | Filtrar por carrera |
| GET | `?id_cuatrimestre=1` | Filtrar por cuatrimestre |

Los filtros de materias se pueden combinar en una sola petición.

### `api/alumnos.php`
| Método | Parámetros / Body | Descripción |
|---|---|---|
| GET | — | Todos los alumnos |
| GET | `?id=1` | Un alumno por ID |
| GET | `?matricula=A24` | Buscar por matrícula |
| GET | `?nombre=texto` | Buscar por nombre/apellidos |
| GET | `?id_carrera=1` | Filtrar por carrera |
| POST | JSON body | Registrar alumno |
| PUT | `?id=1` + JSON body | Actualizar completamente |
| PATCH | `?id=1` + JSON body (`estatus` y/o `cuatrimestre_actual`) | Actualización parcial |
| DELETE | `?id=1` | Eliminar alumno (y sus inscripciones) |

Body esperado para POST/PUT:
```json
{
  "matricula": "A240011",
  "nombre": "Juan",
  "apellido_paterno": "Pérez",
  "apellido_materno": "Gómez",
  "correo": "juan@correo.com",
  "telefono": "9810000000",
  "fecha_nacimiento": "2004-01-01",
  "id_carrera": 1,
  "cuatrimestre_actual": 3,
  "estatus": "Activo"
}
```

### `api/inscripciones.php`
| Método | Parámetros / Body | Descripción |
|---|---|---|
| GET | `?id_alumno=1` | Materias inscritas por un alumno |
| GET | `?id_materia=1` | Alumnos inscritos en una materia |
| POST | JSON body | Registrar inscripción |
| DELETE | `?id=1` | Eliminar inscripción |

Body esperado para POST:
```json
{
  "id_alumno": 1,
  "id_materia": 5,
  "fecha_inscripcion": "2026-07-26",
  "periodo": "2026-3",
  "calificacion": 90
}
```

## Formato de respuesta

Todas las APIs regresan JSON con la misma estructura:

```json
{
  "success": true,
  "message": "Descripción del resultado.",
  "data": { }
}
```

En caso de error, `success` es `false` y se incluye `message` con el detalle.

## Funcionalidades del frontend

- Listado, búsqueda por nombre/matrícula y filtro por carrera de alumnos.
- Alta, edición completa y edición rápida (estatus/cuatrimestre) de alumnos.
- Eliminación de alumnos (con eliminación en cascada de sus inscripciones).
- Listado y filtros de materias (nombre, carrera, cuatrimestre).
- Consulta de materias inscritas por alumno.
- Alta y eliminación de inscripciones.
- Todo mediante Fetch API/async-await, sin recargar la página.
