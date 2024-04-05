<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 'certificados';
    public $timestamps=false;
    protected $primaryKey = 'id';
	public $incrementing = false;
	public $keyType = 'string';


    public function institucion()
    {
        return $this->belongsTo('App\Modelos\Institucion', 'institucion_id', 'id');
    }


    public function periodo()
    {
        return $this->belongsTo('App\Modelos\Estado', 'periodo_id', 'id');
    }

    public function procedencia()
    {
        return $this->belongsTo('App\Modelos\Estado', 'procedente_id', 'id');
    }

    public function scopePeriodo($query,$periodo_id){
        if(trim($periodo_id) != 'TODO'){
            $query->where('periodo_id','=',$periodo_id);
        }
    }

    public function scopeProcedencia($query,$procedencia_id){
        if(trim($procedencia_id) != 'TODO'){
            $query->where('procedente_id','=',$procedencia_id);
        }
    }

}
