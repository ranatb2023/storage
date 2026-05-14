<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileEntry extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'path', 'mime_type', 'size', 'folder_id', 'user_id', 'is_starred'];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
