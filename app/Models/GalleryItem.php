<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasFactory;

    protected $table = 'gallery_items';

    protected $fillable = [
        'titulo',
        'image_url',
        'partido_id',
        'uploaded_by_user_id',
    ];

    public function match()
    {
        return $this->belongsTo(Partido::class, 'partido_id');
    }
}
