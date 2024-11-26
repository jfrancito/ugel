<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\Egreso;




use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait EgresoTraits
{

	private function con_lista_egresos() {

		$listadatos 	= 	Egreso::orderby('id','desc')
							->where('institucion_id','=',Session::get('institucion')->id)
							->get();
	 	return  $listadatos;

	}


}