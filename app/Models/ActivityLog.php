<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'project_id',
        'log_type',
        'remark'
    ];

    public function getRemark()
    {
        $remark = json_decode($this->remark, true);
        if (is_array($remark)) {

            $user = $this->user;
            $user_name = e($user ? $user->name : '');

            if ($this->log_type == 'Upload File') {
                return  $user_name . ' ' . __('Upload new file') . ' <b>' . e($remark['file_name'] ?? '') . '</b>';
            } elseif ($this->log_type == 'Create Timesheet') {
                return $user_name . " " . __('Create new Timesheet');
            } elseif ($this->log_type == 'Create Bug') {
                return $user_name . ' ' . __('Create new Bug') . " <b>" . e($remark['title'] ?? '') . "</b>";
            } elseif ($this->log_type == 'Move Bug') {
                return $user_name . " " . __('Move Bug') . " <b>" . e($remark['title'] ?? '') . "</b> " . __('from') . " " . __(ucwords(e($remark['old_status'] ?? ''))) . " " . __('to') . " " . __(ucwords(e($remark['new_status'] ?? '')));
            } elseif ($this->log_type == 'Invite User') {
                $inviteUser = User::find($remark['user_id']);
                return $user_name . ' ' . __('Invite new User') . ' <b>' . e(($inviteUser) ? $inviteUser->name : '') . '</b>';
            } elseif ($this->log_type == 'Share with Client') {
                $inviteClient = User::find($remark['client_id']);
                return $user_name . ' ' . __('Share Project with Client') . ' <b>' . e(($inviteClient) ? $inviteClient->name : '') . '</b>';
            } elseif ($this->log_type == 'Create Task') {
                return $user_name . ' ' . __('Create new Task') . " <b>" . e($remark['title'] ?? '') . "</b>";
            } elseif ($this->log_type == 'Move') {
                return $user_name . " " . __('Move Task') . " <b>" . e($remark['title'] ?? '') . "</b> " . __('from') . " " . __(ucwords(e($remark['old_status'] ?? ''))) . " " . __('to') . " " . __(ucwords(e($remark['new_status'] ?? '')));
            } elseif ($this->log_type == 'Create Milestone') {
                return $user_name . " " . __('Create new Milestone') . " <b>" . e($remark['title'] ?? '') . "</b>";
            } elseif ($this->log_type == 'has delete a file') {
                return $user_name . " " . __('has delete a file') . " <b>" . e($remark['file_name'] ?? '') . "</b>";
            } elseif ($this->log_type == 'has created a new project') {
                return $user_name . " " . __('has created a new project') . " <b>" . e($remark['projectName'] ?? '') . "</b>";
            } elseif ($this->log_type == 'has updated a milestone') {
                return $user_name . " " . __('has updated a milestone') . " <b>" . e($remark['milestoneTitle'] ?? '') . "</b>";
            } elseif ($this->log_type == 'has updated the milestone status to') {
                return $user_name . " " . __('has updated the milestone status to') . " <b>" . e($remark['milestoneStatus'] ?? '') . "</b>";
            } elseif ($this->log_type == 'has deleted a milestone') {
                return $user_name . " " . __('has deleted a milestone') . " <b>" . e($remark['milestoneTitle'] ?? '') . "</b>";
            } elseif ($this->log_type == 'has uploaded a review file') {
                return $user_name . " " . __('has uploaded a drawing file in') . " <b>" . e($remark['milestoneTitle'] ?? '') . "</b>";
            }
        } else {
            return e($this->remark);
        }
    }


    public static function logType($logtype)
    {

        if ($logtype == 'Upload File') {
            return   __('Upload new file');
        } elseif ($logtype == 'Create Timesheet') {
            return  __('Create new Timesheet');
        } elseif ($logtype == 'has uploaded a review file') {
            return  __('has uploaded a review file');
        } elseif ($logtype == 'Create Bug') {
            return  __('Create new Bug');
        } elseif ($logtype == 'Move Bug') {
            return __('Move Bug');
        } elseif ($logtype == 'Invite User') {
            return  __('Invite new User');
        } elseif ($logtype == 'Share with Client') {
            return  __('Share Project with Client');
        } elseif ($logtype == 'Create Task') {
            return  __('Create new Task');
        } elseif ($logtype == 'Move') {
            return __('Move Task');
        } elseif ($logtype == 'Create Milestone') {
            return __('Create new Milestone');
        }
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
