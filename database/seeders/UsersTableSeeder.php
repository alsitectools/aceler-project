<?php

namespace Database\Seeders;

use App\Models\Languages;
use App\Models\projectType;
use App\Models\Stage;
use App\Models\TaskType;
use Illuminate\Database\Seeder;
use App\Models\Workspace;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        self::seedWorkspaces();
        $this->command->info('Workspaces inicializada con datos!');

        self::seedStages();
        $this->command->info('Stages inicializada con datos!');

        self::seedProjectType();
        $this->command->info('Tipo de proyectos inicializada con datos!');

        self::seedTaskType();
        $this->command->info('Tipo de tareas inicializada con datos!');
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
    function seedStages()
    {
        Stage::truncate();

        foreach ($this->stages as $name => $order) {
            $stage = new Stage;
            $stage->name = $name;
            $stage->order = $order;
            $stage->save();
        }
    }

    function seedTaskType()
    {
        TaskType::truncate();
        $objProjectTypes = ProjectType::all();


        foreach ($this->nameTasks as $index => $tasks) {
            $projectType = $objProjectTypes[$index];

            foreach ($tasks as $name) {
                $taskType = new TaskType;
                $taskType->name = $name;
                $taskType->project_type = $projectType->id;
                $taskType->save();
            }
        }
    }

    function seedWorkspaces()
    {
        foreach ($this->workspaces as $workspace) {
            Workspace::create($workspace);
        }
    }

    function seedLanguages()
    {
        foreach ($this->languages as $lang) {
            Languages::create($lang);
        }
    }

    private $workspaces = [
        ['name' => 'Santiago', 'slug' => 'ac', 'created_by' => 1, 'lang' => 'es', 'currency' => 'CLP', 'interval_time' => 10, 'country' => 'Chile', 'is_active' => 1],
        ['name' => 'Concepción', 'slug' => 'xc', 'created_by' => 1, 'lang' => 'es', 'currency' => 'CLP', 'interval_time' => 10, 'country' => 'Chile', 'is_active' => 1],
        ['name' => 'Antofagasta', 'slug' => 'xa', 'created_by' => 1, 'lang' => 'es', 'currency' => 'CLP', 'interval_time' => 10, 'country' => 'Chile', 'is_active' => 1],
        ['name' => 'Catalunya', 'slug' => 'en', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Alicante', 'slug' => 'al', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Zaragoza', 'slug' => 'za', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Tenerife', 'slug' => 'tf', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Valencia', 'slug' => 'va', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Baleares', 'slug' => 'ib', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Asturias', 'slug' => 'mi', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Malaga', 'slug' => 'ms', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Sevilla', 'slug' => 'se', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Pais Vasco', 'slug' => 'pv', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Madrid', 'slug' => 'md', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Las Palmas', 'slug' => 'lp', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'Aragón y Navarra', 'slug' => 'ar', 'created_by' => 1, 'lang' => 'es', 'currency' => '€', 'interval_time' => 10, 'country' => 'España', 'is_active' => 1],
        ['name' => 'India', 'slug' => 'in', 'created_by' => 1, 'lang' => 'en', 'currency' => 'INR', 'interval_time' => 10, 'country' => 'India', 'is_active' => 1],
        ['name' => 'Italia', 'slug' => 'it', 'created_by' => 1, 'lang' => 'en', 'currency' => '€', 'interval_time' => 10, 'country' => 'Italia', 'is_active' => 1],
        ['name' => 'México', 'slug' => 'mx', 'created_by' => 1, 'lang' => 'es', 'currency' => '$', 'interval_time' => 10, 'country' => 'México', 'is_active' => 1],
        ['name' => 'UAE', 'slug' => 'ae', 'created_by' => 1, 'lang' => 'en', 'currency' => 'Dhs', 'interval_time' => 10, 'country' => 'UAE', 'is_active' => 1],
        ['name' => 'Panamá', 'slug' => 'pa', 'created_by' => 1, 'lang' => 'es', 'currency' => '$', 'interval_time' => 10, 'country' => 'Panamá', 'is_active' => 1],
        ['name' => 'Paraguay', 'slug' => 'py', 'created_by' => 1, 'lang' => 'es', 'currency' => '$', 'interval_time' => 10, 'country' => 'Paraguay', 'is_active' => 1],
        ['name' => 'Perú', 'slug' => 'pe', 'created_by' => 1, 'lang' => 'es', 'currency' => '$', 'interval_time' => 10, 'country' => 'Perú', 'is_active' => 1],
        ['name' => 'Philippines', 'slug' => 'ph', 'created_by' => 1, 'lang' => 'en', 'currency' => '$', 'interval_time' => 10, 'country' => 'Filipinas', 'is_active' => 1],
        ['name' => 'Poznan', 'slug' => 'pz', 'created_by' => 1, 'lang' => 'en', 'currency' => 'zl', 'interval_time' => 10, 'country' => 'Polonia', 'is_active' => 1],
        ['name' => 'Varsovia', 'slug' => 'wa', 'created_by' => 1, 'lang' => 'en', 'currency' => 'zl', 'interval_time' => 10, 'country' => 'Polonia', 'is_active' => 1],
        ['name' => 'Chelmek', 'slug' => 'po', 'created_by' => 1, 'lang' => 'en', 'currency' => 'zl', 'interval_time' => 10, 'country' => 'Polonia', 'is_active' => 1],
        ['name' => 'Portugal', 'slug' => 'pt', 'created_by' => 1, 'lang' => 'en', 'currency' => '€', 'interval_time' => 10, 'country' => 'Portugal', 'is_active' => 1],
        ['name' => 'Rumania', 'slug' => 'ro', 'created_by' => 1, 'lang' => 'en', 'currency' => '', 'interval_time' => 10, 'country' => 'Rumania', 'is_active' => 1],
        ['name' => 'Uruguay', 'slug' => 'uy', 'created_by' => 1, 'lang' => 'es', 'currency' => '$', 'interval_time' => 10, 'country' => 'Uruguay', 'is_active' => 1],
        ['name' => 'Florida', 'slug' => 'fl', 'created_by' => 1, 'lang' => 'en', 'currency' => '$', 'interval_time' => 10, 'country' => 'EEUU', 'is_active' => 1],
        ['name' => 'Texas', 'slug' => 'tx', 'created_by' => 1, 'lang' => 'en', 'currency' => '$', 'interval_time' => 10, 'country' => 'EEUU', 'is_active' => 1],
        ['name' => 'Colombia', 'slug' => 'co', 'created_by' => 1, 'lang' => 'es', 'currency' => '$', 'interval_time' => 10, 'country' => 'Colombia', 'is_active' => 1],
        ['name' => 'Marruecos', 'slug' => 'ma', 'created_by' => 1, 'lang' => 'en', 'currency' => '', 'interval_time' => 10, 'country' => 'Marruecos', 'is_active' => 1],
    ];

    private $stages = [
        ['To Do', '#77b6ea',  1],
        ['In Progress', '#545454', 2],
        ['Review', '#3cb8d9', 3],
        ['Done', '#37b37e', 4],
    ];

    private $typeProjectname = [
        'Obra',
        'Innovación',
        'Desarrollo de producto',
        'Oficina'
    ];

    private $nameTasks = [
        ['Replanteo', 'Cálculo cargas', 'Visita obra', 'Informe cálculo', 'Diseño pieza especial'],
        ['Análisis requisitos',  'Prediseño',  'Diseño', 'Desarrollo y pruebas'],
        ['Análisis requisitos',  'Manuales', 'Diseño'],
        ['Gestión de oficina', 'Formación']
    ];

    private $languages = [
        ['es' => 'Español'],
        ['en' => 'English']
    ];
}
