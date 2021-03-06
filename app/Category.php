<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['id', 'parent_id', 'name', 'cover', 'sort', 'status'];

    protected $table = 'categories';

    protected $hidden = [];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $softDelete = true;

    public function child()
    {
        return $this->hasMany('App\Category', 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\Category', 'id', 'parent_id');
    }

    public function posts()
    {
        return $this->belongsTo('App\Post');
    }
}
