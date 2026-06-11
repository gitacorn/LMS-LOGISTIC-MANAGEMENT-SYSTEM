<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use Illuminate\Database\Eloquent\SoftDeletes;


class Post extends Model
{
    use HasFactory,SoftDeletes;
    public $table = 'posts';
    const CREATED_AT = 'created_at';
    
    const UPDATED_AT = 'updated_at';
    
    
    
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
    	'post_name',
    	'state_id',
    ];
    
    public function postSate(){
    	return $this->belongsTo( State::class , 'state_id' , 'id')->select(['state_name']);
    }
}
