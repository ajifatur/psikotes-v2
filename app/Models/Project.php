<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];
    
    /**
     * User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Result.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * The tests that belong to the project.
     */
    public function tests()
    {
        return $this->belongsToMany(Test::class, 'project__test', 'project_id', 'test_id');
    }
}