<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'project_id',
        'related_id',
        'type',
        'data',
        'is_read',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'related_id');
    }

    public function bugReport()
    {
        return $this->belongsTo(BugReport::class, 'related_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Avisos de la campana (tipos 1-5). El texto se guarda en español, p. ej.
    //   4: "<encargo> en <proyecto>. La fecha de entrega prevista es <dd-mm-aaaa>"
    //   5: "<encargo> en el proyecto <proyecto>"
    //   2: "<encargo> en <proyecto>" (a veces también con la fecha)
    //   3: "<encargo>"   1: "<proyecto>"
    const DUE_SEPARATOR = '. La fecha de entrega prevista es ';

    /**
     * Partes del aviso para pintarlo: kind (review|assign|created|finished|project),
     * title (encargo, o proyecto si es de tipo 1), project y due (fecha prevista).
     * Se usa la última « en » porque el nombre del encargo también puede llevarla.
     */
    public function parts(): array
    {
        $data = trim((string) $this->data);
        $type = (string) $this->type;
        $kind = ['1' => 'project', '2' => 'created', '3' => 'finished', '4' => 'assign', '5' => 'review'][$type] ?? 'created';
        $due = '';
        if (($pos = strpos($data, self::DUE_SEPARATOR)) !== false) {
            $due = trim(substr($data, $pos + strlen(self::DUE_SEPARATOR)));
            $data = substr($data, 0, $pos);
        }

        $title = $data;
        $project = '';
        if ($type === '5' && ($pos = strrpos($data, ' en el proyecto ')) !== false) {
            $title = substr($data, 0, $pos);
            $project = substr($data, $pos + strlen(' en el proyecto '));
        } elseif (in_array($type, ['2', '4'], true) && ($pos = strrpos($data, ' en ')) !== false) {
            $title = substr($data, 0, $pos);
            $project = substr($data, $pos + 4);
        }

        return ['kind' => $kind, 'title' => trim($title), 'project' => trim($project), 'due' => $due];
    }

    /**
     * A dónde lleva el aviso: la página del proyecto con el encargo abierto (?milestone=id).
     * Los avisos nuevos guardan el encargo en related_id; en los antiguos se busca por nombre
     * (el más reciente que coincida). Devuelve null si ya no existe.
     */
    public function targetUrl(): ?string
    {
        $parts = $this->parts();
        $milestone = null;
        $project = null;

        if ((int) $this->related_id > 0 && $this->type !== '1') {
            $milestone = Milestone::select('id', 'project_id')->find((int) $this->related_id);
        }

        if (!$milestone && $parts['kind'] !== 'project' && $parts['title'] !== '') {
            $query = Milestone::select('milestones.id', 'milestones.project_id')
                ->join('projects', 'projects.id', '=', 'milestones.project_id')
                ->where('milestones.title', $parts['title'])
                ->orderByDesc('milestones.id');
            if ($parts['project'] !== '') {
                $query->where('projects.name', $parts['project']);
            } elseif ((int) $this->workspace_id > 0) {
                $query->where('projects.workspace', (int) $this->workspace_id);
            }
            $milestone = $query->first();
        }

        if ($milestone) {
            $project = Project::select('id', 'workspace')->find((int) $milestone->project_id);
        } elseif ($parts['kind'] === 'project' && $parts['title'] !== '') {
            $project = Project::select('id', 'workspace')
                ->where('name', $parts['title'])
                ->when((int) $this->workspace_id > 0, fn ($q) => $q->where('workspace', (int) $this->workspace_id))
                ->orderByDesc('id')
                ->first();
        }

        if (!$project) {
            return null;
        }
        $workspace = Workspace::select('id', 'slug')->find((int) $project->workspace);
        if (!$workspace) {
            return null;
        }

        $url = route('projects.show', [$workspace->slug, $project->id]);
        return $milestone ? $url . '?milestone=' . (int) $milestone->id : $url;
    }


    public function toHtml(){

        $data = json_decode($this->data);

        $link = '#';
        $icon = 'fa fa-bell';
        $icon_color = 'bg-primary';
        $text = '';

        if($this->type == 'task_assign'){
            $project = Project::find($data->project_id);
            if($project){
                $link = route('projects.task.board',[$this->workspace_id,$data->project_id]);
                $text = __('New task assign')." <b>".$data->title."</b> ".__('in project')." <b>".$project->name."</b>";
                $icon = "fa fa-clock-o";
            }else{
                return '';
            }
        }
        elseif($this->type == 'project_assign'){
            $link = route('projects.show',[$this->workspace_id,$data->id]);
            $text = __('New project assign')." <b>".$data->name."</b>";
            $icon = "fa fa-suitcase";
        }
        elseif($this->type == 'bug_assign'){
            $project = Project::find($data->project_id);
            if($project){
                $link = route('projects.bug.report',[$this->workspace_id,$data->project_id]);
                $text = __('New bug assign')." <b>".$data->title."</b> ".__('in project')." <b>".$project->name."</b>";
                $icon = "fa fa-bug";
            }
        }

        $user = User::find($this->user_id);
        $name = '';
        if($user && trim($user->name) != '')
            foreach (explode(' ', $user->name) as $word)
                $name .= strtoupper($word[0]);

        $date = $this->created_at->diffForHumans();

        $html = '<a href="'.$link.'" class="list-group-item list-group-item-action p-1">
                    <div class="d-flex align-items-center" data-toggle="tooltip" data-placement="right" data-title="'.$date.'">
                        <div class="notification_icon_size">
                            <span class="avatar bg-primary text-white rounded-circle px-2 py-1">'.$name.'</span>
                        </div>
                        <div class="flex-fill ml-3">
                            <div class="h6 text-sm mb-0">'.$user->name.' <small class="float-right text-muted">'.$date.'</small></div>
                            <p class="text-sm lh-140 mb-0">
                                '.$text.'
                            </p>
                        </div>
                    </div>
                </a>';

        return $html;
    }
}
