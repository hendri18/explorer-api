<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $appends = ['file_url'];

    protected $hidden = ['folder'];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function getFileUrlAttribute()
    {
        return url('storage/'.$this->id.'/'.$this->folder->user->id);
    }
}
