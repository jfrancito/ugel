<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Conei extends Model
{
    protected $table = 'coneis';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';

}
