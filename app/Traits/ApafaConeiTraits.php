<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\Requerimiento;
use App\Modelos\Conei;
use App\Modelos\Apafa;

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
		$listadatos 	= 	Conei::join('certificados','certificados.referencia_id','=','coneis.id')
							->select('coneis.*','certificados.id as certificado_id','certificados.archivo_id')
							->where('coneis.institucion_id','=',Session::get('institucion')->id)
							->where('certificados.procedente_id','=','APCN00000002')
							->get();
	 	return  $listadatos;
	}

	private function con_lista_apafa() {
		$listadatos 	= 	Apafa::join('certificados','certificados.referencia_id','=','apafas.id')
							->select('apafas.*','certificados.id as certificado_id','certificados.archivo_id')
							->where('apafas.institucion_id','=',Session::get('institucion')->id)
							->where('certificados.procedente_id','=','APCN00000001')
							->get();
	 	return  $listadatos;
	}


	private function con_lista_conei_admin() {
		$listadatos 	= 	Conei::join('instituciones','instituciones.id','=','coneis.institucion_id')
							->select('coneis.*','instituciones.codigo as codigo_institutcion')
							->get();


	 	return  $listadatos;
	}

	private function con_lista_apafa_admin() {
		$listadatos 	= 	Apafa::join('instituciones','instituciones.id','=','apafas.institucion_id')
							->select('apafas.*','instituciones.codigo as codigo_institutcion')
							->get();
	 	return  $listadatos;
	}


	private function array_documentos_cargar() {

		$listadatos 	= 	Conei::where('director_id','=',Session::get('direccion')->id)
							->get();
	 	return  $listadatos;

	 	
	}


	public function ordernar_array($array_detalle_producto){
	    
		//order array
		$array_representante 		= array();
		foreach ($array_detalle_producto as $clave=>$empleado){
			$array_representante[$clave] 		= $empleado["representante_id"];
		}
		array_multisort($array_representante, SORT_ASC, $array_detalle_producto);


		return $array_detalle_producto;
	}


	private function array_representante_obligatrio($tipo_colegio) {

		$array  = array();

		if($tipo_colegio=='POLIDOCENTE'){
			$array = array('ESRP00000001','ESRP00000002','ESRP00000003','ESRP00000004');
		}else{
			if($tipo_colegio=='UNIDOCENTE'){
				$array = array('ESRP00000001','ESRP00000002','ESRP00000003','ESRP00000004');
			}else{
				if($tipo_colegio=='MULTIGRADO'){
					$array = array('ESRP00000001','ESRP00000002','ESRP00000003','ESRP00000004');
				}
			}
		}
		return $array;
	
	}

	private function array_representante_obligatrio_apafa($tipo_colegio) {

		$array  = array();
		$array = array('ESRA00000001','ESRA00000003','ESRA00000004');

		return $array;
	
	}


	private function array_representante_obligatrio_apafa_vigilancia($tipo_colegio) {

		$array  = array();
		$array = array('ESRV00000001');

		return $array;
	
	}


}