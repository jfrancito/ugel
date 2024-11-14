<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\Ingreso;




use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait IngresoTraits
{

	private function con_lista_ingresos() {

		$listadatos 	= 	Ingreso::orderby('id','desc')->get();
	 	return  $listadatos;

	}


}