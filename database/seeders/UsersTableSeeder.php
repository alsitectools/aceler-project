<?php

namespace Database\Seeders;

use App\Models\MasterObra;
use App\Models\projectType;
use App\Models\TaskType;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserWorkspace;
use App\Models\UserProject;
use App\Models\Workspace;
use App\Models\Utility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // self::seedWorkspace();
        // $this->command->info('Tabla de workspace inicializada con datos!');

        // self::seedUsers();
        // $this->command->info('Tabla de usuarios inicializada con datos!');
        // User::defaultEmail();
        // User::seed_languages();

        // self::seedUserWorkspace();
        // $this->command->info('Tabla de user_workspace inicializada con datos!');

        self::seedProjectType();
        $this->command->info('Tabla de tipo de proyectos inicializada con datos!');

        self::seedMO();
        $this->command->info('Tabla de Master Obras inicializada con datos!');

        self::seedTaskType();
        $this->command->info('Tabla de tipo de tareas inicializada con datos!');
    }

    function seedProjectType()
    {
        ProjectType::truncate();

        foreach ($this->typeProjectname as $name) {
            $projectType = new ProjectType;
            $projectType->name = $name;
            $projectType->save();
        }
    }
    function seedMO()
    {
        MasterObra::truncate();

        foreach ($this->masterObras as $ref_mo => $name) {
            $MO = new MasterObra;
            $MO->ref_mo = $ref_mo;
            $MO->name = $name;
            $MO->save();
        }
    }

    function seedTaskType()
    {
        TaskType::truncate();
        $objProjectTypes = ProjectType::all();

        // Iterar sobre los tipos de proyecto y sus tareas correspondientes
        foreach ($this->nameTasks as $index => $tasks) {
            $projectType = $objProjectTypes[$index];

            foreach ($tasks as $name) {
                $taskType = new TaskType;
                $taskType->name = $name;

                // Asignar el tipo de proyecto a la tarea
                $taskType->project_type = $projectType->id;
                $taskType->save();
            }
        }
    }

    private $typeProjectname = [
        'Obra',
        'Innovación',
        'Desarrollo de producto',
        'Oficina'
    ];

    private $nameTasks = [
        [
            'Replanteo',
            'Cálculo cargas',
            'Visita obra',
            'Informe cálculo',
            'Diseño pieza especial',
        ],
        [
            'Análisis requisitos',
            'Prediseño',
            'Diseño',
            'Desarrollo y pruebas',
        ],
        [
            'Análisis requisitos',
            'Manuales',
            'Diseño',
        ],
        [
            'Gestión de oficina',
            'Formación',
        ]
    ];

    private $prueba = [
        'ES0' => [
            'TF' => 'Tenerife',
            'LP' => 'Las palmas',
            'AL' => 'Alicante',
            'VA' => 'Valencia',
            'BA' => 'Baleares',
            'AR' => 'Aragón',
            'MI' => 'Asturias',
            'GA' => 'Galicia',
            'EN' => 'Cataluña',
            'PV' => 'País Vasco'
        ],
        'ES1' => [
            'MD' => 'Madrid',
            'SE' => 'Sevilla',
            'MS' => 'Málaga'
        ]
    ];


    // private $workspaces = array(
    //     array(
    //         'company' => 'ES0',
    //         'branch' => array(
    //             'Tenerife' => 'TF',
    //             'Las palmas' => 'LP',
    //             'Alicante' => 'AL',
    //             'Valencia' => 'VA',
    //             'Baleares' => 'BA',
    //             'Aragón' => 'AR',
    //             'Asturias' => 'MI',
    //             'Galicia' => 'GA',
    //             'Cataluña' => 'EN',
    //             'País Vasco' => 'PV',
    //         ),
    //     ),
    //     array(
    //         'company' => 'ES1',
    //         'branch' => array(
    //             'Centro' => 'MD',
    //             'Sevilla' => 'SE',
    //             'Málaga' => 'MS'
    //         ),
    //     ),
    // );

    private $users = array(
        array(
            'name' => 'admin',
            'email' => 'admin@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'admin',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Marc Forte',
            'email' => 'mforte@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'user',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Alex Pardo',
            'email' => 'alexp@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'user',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Manuel Gascón',
            'email' => 'mgascon@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'client',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'David Poch',
            'email' => 'dpoch@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'user',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Didac Ribo Ruiz',
            'email' => 'dribo@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'user',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Marc Allepuz',
            'email' => 'mallepuz@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'user',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Karla Cubias',
            'email' => 'kcubias@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'user',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
        array(
            'name' => 'Xichen Zhao',
            'email' => 'xzhao@alsina.com',
            'password' => 'Alsina2024',
            'type' => 'client',
            'currant_workspace' => 'ES',
            'lang' => 'es'
        ),
    );


    private $masterObras = array(
        '2500062456' => 'Su Almacen (pasasores Y Arandelas)',
        '2500062457' => 'Puente En Jarandilla',
        '2500062458' => 'Edar Robledillo De La Vera',
        '2500062459' => 'Obra Deta Carcel Sevilla 1',
        '2500062460' => 'Su Almacen (barandilla)',
        '2500062461' => 'Emasesa (la Motilla)',
        '2500062462' => '(est.) Const. Pasos Fauna A 483-a494',
        '2500062463' => 'Su Almacen Plza. Vista Florida, 16',
        '2500062464' => 'Su Almacen',
        '2500062465' => 'Edif.112 Emergencias/alcala De Guadaira',
        '2500062466' => 'Muro En Cavega/sanlucar De Bda.',
        '2500062467' => '8mk Merida Iii Milenium Ute',
        '2500062468' => 'Viv. En Osuna',
        '2500062469' => 'Tanatorio Servisa',
        '2500062470' => 'Planta En Alvarado',
        '2500062471' => 'Cuartos De Manipulacion De Pescado',
        '2500062472' => 'Obra En Jerez',
        '2500062473' => '(std)reforma Estadio Bahia Sur',
        '2500062474' => 'Uvi-devolucion Mat.Liquidado',
        '2500062475' => 'Parking En Badajoz',
        '2500062476' => 'Parking En Badajoz',
        '2500062477' => 'Parking En Badajoz',
        '2500062478' => 'Hospital',
        '2500062479' => 'Parking En Badajoz',
        '2500062480' => 'Conduccion Edar Rota A Costa Ballena',
        '2500062481' => 'Nave En P.I. Ilipa Magna',
        '2500062482' => 'Su Almacen / Ecomecano',
        '2500062483' => 'Viviendas P.O.',
        '2500062484' => 'Edar En Almendralejo',
        '2500062485' => 'Reforma En Parque',
        '2500062486' => 'Piscina Publica',
        '2500062487' => 'Su Almacen (alum.Pilares.Muro)',
        '2500062488' => 'Oficina Comarcal Agraria',
        '2500062489' => 'Su Almacen/utrera',
        '2500062490' => 'Soporte Cuchara En Siderurgia',
        '2500062491' => 'Bancada \'aguas Teñidas\'',
        '2500062492' => 'Acondicionamiento Carretera A-495',
        '2500062493' => 'Su Almacen / Desencofrante',
        '2500062494' => 'Parque Urbano En Poblado El Trobal',
        '2500062495' => 'Linea Alta Velocidad Las Cabezas-lebrija',
        '2500062496' => 'Linea De Alta Velocidad Lebrija-el Cuervo',
        '2500062497' => '10 Vvdas. De Promocion Publica',
        '2500062498' => 'Cuarto De Manip.Del Pto.De Bonanza',
        '2500062499' => '12 V.P.O. En Gargaligas',
        '2500062500' => '23 Viviendas En Coria',
        '2500062501' => '8mk Merida Iii Milenium Ute',
        '2500062502' => 'Sede Caja Badajoz',
        '2500062503' => 'Su Almacen (tapones)',
        '2500062504' => 'Acondicionamiento En Alcala',
        '2500062505' => '500 Viv En Gibraltar',
        '2500062506' => 'Conexion Linea 1 Del Metro De Sevilla',
        '2500062507' => 'Su Almacen',
        '2500062508' => 'Viv Con Fcc'
    );
}
