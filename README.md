# Sistema de Gestión de Mascotas (Pet Manager) CON BASE DE DATOS

Maudiel López Dávila, estudiante de 12-4 en el Colegio Técnico Profesional Alajuelita de Desarrollo de Software. 
#
Prueba técnica para un sistema de gestión de mascotas en una veterinaria pequeña. Básicamente, empecé con algo simple siguiendo la guía de la prueba, pero lo fui mejorando hasta llegar a la versión 3.0, donde decidí escalarlo para que funcione con una base de datos real (MySQL). En las versiones anteriores usaba sesiones de PHP y localStorage para simular el almacenamiento, pero quise hacerlo más profesional. La prueba no pedía BD, pero lo agregué para mostrar que puedo manejar cosas más avanzadas como conexiones PDO y quewrys seguras.

El foco de la prueba era OOP, lógica, validaciones y documentación, y lo cubrí todo: clases bien modeladas con getters/setters, relaciones uno-a-muchos, formularios con validaciones en JS y PHP, manejo de errores, y exportar a CSV y un login. Todo en PHP puro, sin frameworks, para mantenerlo básico pero sólido.

*Para probar la versión 2.3 que es portatil y rápida, puedes usar este link de despliegue: [http://petmanager.zeabur.app/](http://petmanager.zeabur.app/) – pero nota que el despliegue es la versión 2.3, sin embargo la ambás versiones mantienen la mayoría de funcionalidades.*

*Sin embargo para probar esta versión si necesitas instalar la base de datos de manera local en tu ordenador.*

## Funcionalidades

- **Registro y Gestión:** Agrega mascotas (con nombre, especie, raza, fecha de nacimiento, color), dueños (nombre, teléfono, email, dirección) y visitas médicas (fecha, diagnóstico, tratamiento, motivo). Puedes asociar dueños a mascotas (uno o más) y visitas a mascotas específicas.
- **Listados y Búsquedas:** Páginas dedicadas para listar mascotas, dueños y visitas en tablas. Buscador en tiempo real con JS para filtrar, y una página de detalles para mascotas que muestra dueños asociados y historial de visitas.
- **Edición y Eliminación:** Edita cualquier entrada con forms prellenados. Elimina con confirmación JS (y en BD es un borrado lógico, no físico, para no perder datos por accidente).
- **Autenticación:** Registro de usuarios con validaciones (usuario único, pass hashed con password_hash), login seguro, y logout. Todo protegido: no accedes sin loguearte.
- **Exportar:** Descarga un CSV de las mascotas con sus datos básicos.
- **Validaciones y UX:** Chequeos en vivo con JS (regex para nombres, teléfonos, etc.), mensajes de error/exito con notificaciones flotantes, y diseño responsive con CSS.

## Instalación

Solo necesitas un server local con PHP y MySQL. Usé XAMPP porque es lo que recomiendan en la guía de la prueba.

1. **Descarga el Repo:** Clona o descarga este repo desde GitHub (o el zip que entrego).
2. **Configura XAMPP:** Instala XAMPP si no lo tienes (descárgalo de apachefriends.org). Arranca Apache y MySQL desde el panel.
3. **Crea la BD:** Abre phpMyAdmin (http://localhost/phpmyadmin), crea una base llamada `sistema_mascotas`. Importa el script `sql/db.sql` para crear las tablas (mascotas, duenos, visitas_medicas, usuarios, y la intermedia mascota_dueno para relaciones muchos-a-muchos).
4. **Configura la Conexión:** En `config/conexion_bd.php`, ajusta si es necesario el servidor ('localhost'), usuario ('root'), pass (vacío por default), y charset ('utf8mb4'). Usé singleton para la conexión, para que sea eficiente y no se reconecte cada vez.
5. **Coloca los Archivos:** Pon la carpeta del proyecto en `htdocs/` de XAMPP (ej: `htdocs/petmanager`).
6. **Accede:** Abre el navegador y ve a `http://localhost/petmanager/`. Regístrate en `registro_usuario.php`, luego loguea en `iniciar_sesion.php`.
7. **Prueba:** Crea un usuario, agrega mascotas, y juega con todo. Si algo falla, chequea los logs de PHP o MySQL.

Nota: En producción usaría .env para creds de BD, pero aquí lo dejé simple. No hay dependencias extras, solo PHP 7+ y MySQL.

## Cómo Usarlo

- **Home (index.php):** Dashboard con bienvenida, links rápidos para registrar cosas, y un buscador rápido para mascotas.
- **Registrar:**
  - Mascota: Ve a `registrar/registrar_mascota.php` – Llena los campos, asocia dueños existentes si quieres.
  - Dueño: `registrar/registrar_dueno.php` – Opcional asociar a mascota.
  - Visita: `registrar/registrar_visita.php` – Elige una mascota obligatoria.
- **Listas:**
  - Mascotas: `listas/listar_mascotas.php` – Tabla con filtros, botones para edit/elim/detalles.
  - Dueños: `listas/listar_duenos.php`.
  - Visitas: `listas/listar_visitas.php`.
- **Editar/Eliminar:** Desde las listas, edit va a `editar/editar_*.php` (prellena con datos de BD), elim confirma y marca como inactivo en BD.
- **Buscar:** En home o listas, escribe y filtra en vivo. Para detalles completos, usa el buscador por nombre.
- **Exportar:** Botón en la lista de mascotas descarga CSV.
- **Logout:** En el header, cierra sesión y borra datos de sesión.

Si intentas acceder sin login, te redirige a `iniciar_sesion.php`. Todo maneja errores: si duplicas un nombre de usuario, te avisa; si input inválido, JS lo pilla al toque.

## Estructura del Proyecto

Carpeta `mascotas/` (renombrada a petmanager o lo que sea):

- **config/**: `conexion_bd.php` – Maneja la conexión PDO singleton a MySQL.
- **models/**: Clases OOP como pide la prueba.
  - `mascota.php`: Con attrs, métodos para guardar/actualizar/eliminar/buscar en BD, cargar dueños/visitas, y relaciones.
  - `dueno.php`: Similar, con métodos DB.
  - `visitaMedica.php`: Para visitas, ligada a mascotas.
  - `usuario.php`: Para auth, con hash y verify.
- **editar/**: Forms para editar entradas.
- **eliminar/**: Scripts para borrar (lógico).
- **listas/**: Páginas con tablas y filtros.
- **otros/**: Buscador detallado y export CSV.
- **procesar/**: Lógica POST para forms (usando los models para interactuar con BD).
- **registrar/**: Forms para agregar nuevos.
- **sql/**: `db.sql` – Script para crear la BD y tablas.
- Raíz: `index.php` (home), login/registro, CSS/JS, cerrar_sesion.

Todo modular para que sea fácil de leer y expandir.

## Evolución: De v2.3 a v3.0

En la v2.3 (basada en sesiones y localStorage para "persistir" datos temporalmente), todo era más simple pero no real: los datos se perdían al cerrar sesión o navegador. Siguiendo la prueba, no usaba BD porque no lo pedían, solo OOP y lógica. Pero quise aumentar el nivel, así que en v3.0 lo escalé a MySQL para persistencia verdadera. Aquí los cambios clave:

1. **Base de Datos Real:** Agregué MySQL con PDO para queries seguras (contra SQL injection). Usé singleton en `conexion_bd.php` para una sola conexión por request. Tablas: mascotas, duenos, visitas_medicas, usuarios, y mascota_dueno para relaciones muchos-a-muchos (una mascota puede tener varios dueños, y viceversa).
2. **Modelos Mejorados:** Las clases ahora tienen métodos DB como guardar(), actualizar(), eliminar() (borrado lógico con campo 'activo'), obtenerPorId(), obtenerTodas(), buscar(). Cargan relaciones lazy (ej: cargarDuenos() solo cuando necesitas). Quité arrays en sesión, todo va a BD.
3. **Procesos Actualizados:** Los scripts en `procesar/` ahora usan los models para interactuar con BD en vez de sesiones. Ej: procesar_mascota.php crea un objeto Mascota, lo guarda en BD, y asocia dueños via tabla intermedia.
4. **Autenticación Persistente:** Usuarios en BD, no en array de sesión. Cada registro (mascota, etc.) tiene usuario_id para que solo veas/editas lo tuyo.
5. **Eliminación Lógica:** En vez de borrar de verdad, marco 'activo = FALSE' para recovery posible.
6. **Búsquedas y Listas:** Quewrys optimizadas con LIKE para búsquedas, ORDER BY para orden. Filtros JS siguen, pero datos de BD.
7. **Seguridad y Eficiencia:** Prepared statements everywhere, error logging, y cierre de conexión opcional. Quité localStorage porque con BD no hace falta.
8. **Otros Tweaks:** Forms adaptados para IDs de BD (en vez de índices o nombres como keys). Notificaciones via URL params para éxito/error.

Por qué estos cambios: Quería que fuera escalable y listo para un entorno real. En v2.3 era mas un demo, pero v3.0 es un app mini real. Aprendí un montón con PDO y normalización de BD.

Gracias por chequear leer esto. Por estar escalando esto a DB's estoy despierto un domingo a la 1:19 de la madrugada

Bendiciones y agradecimientos a Camilo porque se quedó conmigo en llamada mientras me frustraba con esto.
