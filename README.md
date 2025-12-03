<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
## -------------------------------------------------------------------------------------------------------------------------------------------------------------
## -------------------------------------------------------------------------------------------------------------------------------------------------------------
## SUBIDAS A SERVIDOR
 ## Fichero .env
 Se sube directamente a servidor por lo que si se hace un clon del proyecto este fichero no existe.
 
  
 ## Produccion: git pull origin staging
 Si da dice que hay ficheros sin subir, hacer git reset --hard y vuelves hacer gut pull orignin stagin
 ## Staging: git pull

## IMPORTANTE!
Cuando se hace un clon del proyecto, no se crean algunas carpetas ya que estan en el gitignore
hay algunas que no dan error pero sino existe la de sessions da error 500** storage/framework/sessions

## TABLAS QUE HAN DE TENER VALORES SIEMPRE
   ## Siempre han de tener valores, sino da error 500
        -Workspaces (Delegaciones/grupos)
        -Languages (Minimo ES)
        -Stages (Estados de Milestoneboard)
        -TaskType
        -ProjectType
   ## Tambien hay que asegurarse que cuando se cree un usuario debe existir en la tabla
        -UserWorkspaces

## LARAVEL POR CONVENCION TIENE RUTAS QUE VAN A STORAGE PERO
Actualmente en project las funciones estan creadas para ir a una ruta especifica que se deberia configurar asi: 
  ## Para eliminar el enlace existente, usa el siguiente comando:
 	rm -rf public/storage
  ## staging
    /home/stagingacelerproject/public_html/storage /home/stagingacelerproject/public_html/public/storage
  ## produccion 
     ln -s /home/acelerproject/public_html/storage /home/acelerproject/public_html/public/storage

Esto creará un enlace simbólico desde public/storage a la carpeta storage en la raíz de tu proyecto.
Para asegurarte de que el enlace simbólico se ha creado correctamente, usa el siguiente comando:

	 ls -l public/storage  

## Problemas con rutas
Mirar fichero config/filesystems.php yaa que este fichero esta configurado para ir a la ruta la carpeta raiz /home/acelerproject/public_html/storage o staging

Solucion step by step en servidor:
1. cd /home/stagingacelerproject/public_html/public
2. rm storage
3. ln -s /home/stagingacelerproject/public_html/storage storage
4. ls -l (mostrará por pantalla algo como storage -> /home/stagingacelerproject/public_html/storage)


## VISTAS Y FUNCIONES 
Si hay alguna ruta que no aparezca aqui, busca en web.php 

   ## Vista Index proyectos
    Vista: resources/views/projects/index.blade.php
    Funcion desde ProjectController: public function index($slug){}

   ## creacion de Proyectos
    Vista: resources/views/projects/create.blade.php
    Funcion que crea proyecto desde ProjectController:  public function store($slug, Request $request){}
    La vista gestiona las lamadas a bbdd con el script public/assets/js/create_project.js

   ## vista proyecto
    Vista: resources/views/projects/show.blade.php
    Función desde ProjectController: public function show($slug, $projectID){}

   ## Creación de encargos desde proyecto o desde milestoneboard
    Vista: resources/views/projects/milestone.blade.php
    Funcion que crea el encargo desde  desde ProjectController:
    public function milestoneStore($slug, $projectID, Request $request){}
    La vista gestiona las lamadas a bbdd con el script public/assets/js/create_project.js

   ## Creacion de tareas
    Funcion en ProjectControler que devuelve esta vista: public function taskCreate($slug){}
    Vista: resources/views/projects/taskCreate.blade.php
    Funcion en ProjectController que crea la tarea: public function taskStore(Request $request, $slug)

   ## Vista de timesheet (Hoja de Horas) desde proyecto o desde sidebar
    Vista: resources/views/projects/timesheet.blade.php
    Funcion que muestra Hoja de horas (Tareas desde sidebar):    
    public function timesheet($slug){}

    Esta vista simplemente gestiona si existen o no timesheet(horas imputadas en Tareas), si existen hace una llamada ajax a la public function filterTimesheetTableView(Request $request, $slug) en ProjectController que a su vez gestiona el html en la funcion del modelo project public static function getProjectAssignedTimesheetHTML(){}.
   ## Vista de filterTimesheetTableView: resources/views/projects/timesheet-week.blade.php

## FORMULARIOS DE TIMESHEET CREATE Y EDIT
Los formularios de creacion de horas y editar abren desde timeshete.blade.php, se muestran segun el url que se asigna en la funcion del modelo de Project.php:
private static function processTaskTimesheets(){}
   
   ## Formulario de Añadir horas timesheet-create
    Funcion que devuelve la vista  timesheet-create: public function projectTimesheetCreate(){}
    Vista: resources/views/projects/timesheet-create.blade.php
    Funcion en ProjectControler que crea las horas public function timesheetStore($slug, Request $request){}

   ## Formulario de Editar horas timesheet-edit
   Funcion que devuelve la vista de timesheet-edit: public function projectTimesheetEdit(Request $request, $slug, $timesheet_id, $project_id){}
   Vista: resources/views/projects/timesheet-edit.blade.php
   Funcion en ProjectControler que actualiza el timesheet-edit: public function projectTimesheetUpdate(){}

