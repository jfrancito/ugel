<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;


use App\Traits\GeneralesTraits;
use App\Traits\ReporteTraits;
use App\Traits\CertificadoTraits;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use ZipArchive;
use Maatwebsite\Excel\Facades\Excel;


class ReporteCertificadoController extends Controller
{

	use GeneralesTraits;
	use ReporteTraits;
    use CertificadoTraits;

	public function actionListarCertificadosInstituciones($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Reporte de certificados de instituciones');
	    $sel_periodo 			=	'';
	    $sel_periodo_fin 		=	'';

	    $anio  					=   $this->anio;


        $comboperiodo       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo Inicial','','APAFA_CONEI_PERIODO');
        $comboperiodofin       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo Fin','','APAFA_CONEI_PERIODO');



        $selectperiodo      	=   '';
        $selectperiodofin      	=   '';

        // $selectperiodo      	=   'ACPE00000006';
        // $selectperiodofin      	=   'ACPE00000008';


        $comboprocedencia   	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','TODO','APAFA_CONEI');
        $selectprocedencia  	=   'TODO';

        $arraydata   			=   $this->gn_array_certificados($selectperiodo,$selectperiodofin,$selectprocedencia);


        $listadatos     		=   $this->con_lista_certificados_xfiltro($arraydata);



        //dd($listadatos);

        $listacertificadoperiod =   $this->con_lista_certificados_xperiodo($arraydata);
        $listacertificadoproced =   $this->con_lista_certificados_xprocedencia($arraydata);


        // $listadatos     		=   array();
        // $listacertificadoperiod =   array();
        // $listacertificadoproced =   array();

		$funcion 				= 	$this;
		
		return View::make('reportes/listacertificadoinstitucion',
						 [
						 	'listadatos' 			=> $listadatos,
						 	'listacertificadoperiod'=> $listacertificadoperiod,
						 	'listacertificadoproced'=> $listacertificadoproced,
						 	'comboperiodo'			=> $comboperiodo,
						 	'selectperiodo'			=> $selectperiodo,
						 	'comboperiodofin'		=> $comboperiodofin,
						 	'selectperiodofin'		=> $selectperiodofin,


						 	'comboprocedencia'		=> $comboprocedencia,
						 	'selectprocedencia'	 	=> $selectprocedencia,

						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}


	public function actionAjaxListarInstitucionCertificado(Request $request)
	{

		$periodo_id 			=   $request['periodo_id'];
		$procedencia_id 		=   $request['procedencia_id'];
		$idopcion 				=   $request['idopcion'];
		$selectperiodofin 		=   $request['periodofin_id'];

		//dd($selectperiodofin);



        $comboperiodo       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','TODO','APAFA_CONEI_PERIODO');
        $selectperiodo      	=   $periodo_id;
        $comboprocedencia   	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','TODO','APAFA_CONEI');
        $selectprocedencia  	=   $procedencia_id;


        $arraydata   			=   $this->gn_array_certificados($periodo_id,$selectperiodofin,$selectprocedencia);
        $listadatos     		=   $this->con_lista_certificados_xfiltro($arraydata);
        $listacertificadoperiod =   $this->con_lista_certificados_xperiodo($arraydata);
        $listacertificadoproced =   $this->con_lista_certificados_xprocedencia($arraydata);

        //dd($listacertificadoperiod);
		$funcion 				= 	$this;
		
		return View::make('reportes/ajax/alistacertificadoinstitucion',
						 [

						 	'listadatos' 			=> $listadatos,
						 	'listacertificadoperiod'=> $listacertificadoperiod,
						 	'listacertificadoproced'=> $listacertificadoproced,
						 	'funcion'				=> $funcion,	
						 	'idopcion'				=> $idopcion,		 	
						 	'ajax' 					=> true,						 	
						 ]);
	}

}
