<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'packets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];
    
    /**
     * Get the test that owns the packet.
     */
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    /**
     * Get the questions for the packet.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}