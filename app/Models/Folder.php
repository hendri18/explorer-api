<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';

    public function subFolder()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'id');
    }

    public function subFolders()
    {
        return $this->subFolder()->with('subFolders', 'files');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'folder_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
