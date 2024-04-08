<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;


    /**
     * table
     *
     * @var string
     */
    protected $table = 'catalogs';

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'cat_name',
        'cat_description',
        'cat_keyword',
        'parent_id',
    ];

    /**
     * hidden
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'parent_id',
        // 'pivot',
    ];



    public function children()
    {
        return $this->hasMany(Catalog::class, 'parent_id')->with('status')->with('children')->where(function ($query) {
            if (isset(request()->query()['data'])) $query->where('status_id', 1);
        });
    }
}
