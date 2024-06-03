<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\Requerimiento;
use App\Modelos\Conei;
use App\Modelos\Certificado;
use App\Modelos\DetalleCertificado;
use App\Modelos\Estado;





use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;

trait CertificadoTraits
{

	private function con_lista_certificados() {

		$listadatos 	= 	Certificado::orderby('id','desc')->get();
	 	return  $listadatos;

	}

	private function gn_array_certificados($periodo_id,$periodofin_id,$procedencia_id,$estado_id) {

		$periodoinicio  =   Estado::where('id','=',$periodo_id)->first();
		$periodofin  	=   Estado::where('id','=',$periodofin_id)->first();

		if(count($periodoinicio)>0 && count($periodofin)>0){

		$listaperiodos  =   Estado::where('tipoestado','=','APAFA_CONEI_PERIODO')
							->where('nombre','>=',$periodoinicio->nombre)
							->where('nombre','<=',$periodofin->nombre)
							->orderby('nombre','asc')
							->get();

		}else{
			$listaperiodos = array();
		}




        $arraycert 		=	array();


        foreach ($listaperiodos as $index => $item) 
        {
        	//SOLO EL PRIMER REGISTRO
        	if($index==0){

				$detallecertificado 	= 	DetalleCertificado::where('activo','=','1')
												->Procedencia($procedencia_id)
												->Estado($estado_id)
												->where('estado_id','<>','CEES00000002')
												->select(DB::raw('codigo,certificado_id,min(periodo_id) as periodomin_id'))
												->groupBy('codigo')
												->groupBy('certificado_id')
												->having(DB::raw('min(periodo_id)'), '=', $item->id)
												->get();

				foreach ($detallecertificado as $indexc => $itemc){
					array_push($arraycert, $itemc->certificado_id);
				}								


        	}else{

	        	//SOLO EL FINAL
	        	if($index == count($listaperiodos)-1){

					$detallecertificado 	= 	DetalleCertificado::where('activo','=','1')
													->Procedencia($procedencia_id)
													->Estado($estado_id)
													->where('estado_id','<>','CEES00000002')
													->select(DB::raw('codigo,certificado_id,max(periodo_id) as periodomin_id'))
													->groupBy('codigo')
													->groupBy('certificado_id')
													->having(DB::raw('max(periodo_id)'), '=', $item->id)
													->get();

					foreach ($detallecertificado as $indexc => $itemc){
						array_push($arraycert, $itemc->certificado_id);
					}
	        	}else{

        			//LOS QUE VIENEN EN MEDIO
					$detallecertificado 	= 	DetalleCertificado::where('activo','=','1')
													->Procedencia($procedencia_id)
													->Estado($estado_id)
													->where('estado_id','<>','CEES00000002')
													->select(DB::raw('codigo,certificado_id,min(periodo_id) as periodomin_id'))
													->groupBy('codigo')
													->groupBy('certificado_id')
													->having(DB::raw('min(periodo_id)'), '=', $item->id)
													->get();
					foreach ($detallecertificado as $indexc => $itemc){
						array_push($arraycert, $itemc->certificado_id);
					}	
					$detallecertificado 	= 	DetalleCertificado::where('activo','=','1')
													->Procedencia($procedencia_id)
													->Estado($estado_id)
													->where('estado_id','<>','CEES00000002')
													->select(DB::raw('codigo,certificado_id,max(periodo_id) as periodomin_id'))
													->groupBy('codigo')
													->groupBy('certificado_id')
													->having(DB::raw('max(periodo_id)'), '=', $item->id)
													->get();
					foreach ($detallecertificado as $indexc => $itemc){
						array_push($arraycert, $itemc->certificado_id);
					}


	        	}

        	}



		}
			
	 	return  $arraycert;

	}




	private function con_lista_certificados_xfiltro($arraydata) {

		$listadatos 	= 	Certificado::whereIn('id', $arraydata)
							->where('estado_id','<>','CEES00000002')
							->orderby('fecha_crea','desc')
							->get();
							
	 	return  $listadatos;

	}

	private function con_lista_certificados_xperiodo($arraydata) {

		$listadatos 	= 	Certificado::whereIn('id', $arraydata)
							->where('estado_id','<>','CEES00000002')
							->select(DB::raw('count(periodo_nombre) as cantidad,periodo_nombre'))
							->groupby('periodo_nombre')
							->orderby('periodo_nombre','desc')
							->get();
	 	return  $listadatos;

	}


	private function con_lista_certificados_xprocedencia($arraydata) {

		$listadatos 	= 	Certificado::whereIn('id', $arraydata)
							->where('estado_id','<>','CEES00000002')
							->select(DB::raw('count(procedente_id) as cantidad,procedente_id'))
							->groupby('procedente_id')
							->orderby('procedente_id','desc')
							->get();
	 	return  $listadatos;

	}

	private function con_lista_certificados_xestado($arraydata) {

		$listadatos 	= 	Certificado::whereIn('id', $arraydata)
							->where('estado_id','<>','CEES00000002')
							->select(DB::raw('count(estado_nombre) as cantidad,estado_nombre'))
							->groupby('estado_nombre')
							->orderby('estado_nombre','desc')
							->get();
	 	return  $listadatos;

	}



}