<?php

namespace App\Models;

use App\Models\Concerns\KolomAudit;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use KolomAudit;

    protected $fillable = ['user_id', 'role_id', 'program_studi_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }
}
