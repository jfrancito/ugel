<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\Requerimiento;
use App\Modelos\Conei;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait ApafaConeiTraits
{

	private function con_lista_requerimiento() {
		$listadatos 	= 	Requerimiento::get();
	 	return  $listadatos;
	}


	private function con_lista_conei() {
		$listadatos 	= 	Conei::where('institucion_id','=',Session::get('institucion')->id)
							->get();
	 	return  $listadatos;
	}

	private function array_documentos_cargar() {

		$listadatos 	= 	Conei::where('director_id','=',Session::get('direccion')->id)
							->get();
	 	return  $listadatos;

	 	
	}

	private function array_representante_obligatrio($tipo_colegio) {


		if('M'=='M'){
			$array = array('ESRP00000001','ESRP00000002','ESRP00000003','ESRP00000004','ESRP00000005','ESRP00000006');
		}else{
			$array = array('ESRP00000001','ESRP00000005','ESRP00000006');
		}
		return $array;
	
	}



}