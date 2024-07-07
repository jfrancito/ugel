<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use App\Modelos\Conei;
use App\Modelos\Certificado;
use App\Modelos\DetalleCertificado;
use App\Modelos\Estado;
use App\Modelos\Institucion;

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

        $arrayestado            =   array('CEES00000001','CEES00000005','CEES00000007','CEES00000008');
        $comboestado   			=   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione estado','TODO','CERTIFICADO_ESTADO',$arrayestado);
        $selectestado  			=   'TODO';

        $combodistrito   		=   $this->gn_generacion_combo_institucion_distrito('Seleccione distrito','TODO');
        $selectdistrito  		=   'TODO';

        $arraydata   			=   $this->gn_array_certificados($selectperiodo,$selectperiodofin,$selectprocedencia,$selectestado);
        $listadatos     		=   $this->con_lista_certificados_xfiltro($arraydata);

        $listacertificadoperiod =   $this->con_lista_certificados_xperiodo($arraydata);
        $listacertificadoproced =   $this->con_lista_certificados_xprocedencia($arraydata);
        $listacertificadoestado =   $this->con_lista_certificados_xestado($arraydata);
        $listacertificadodistrito =   $this->con_lista_certificados_xdistrito($arraydata);


		$funcion 				= 	$this;
		
		return View::make('reportes/listacertificadoinstitucion',
						 [
						 	'listadatos' 			=> $listadatos,
						 	'listacertificadoperiod'=> $listacertificadoperiod,
						 	'listacertificadoproced'=> $listacertificadoproced,
						 	'listacertificadoestado'=> $listacertificadoestado,
						 	'listacertificadodistrito'=> $listacertificadodistrito,
						 	'comboperiodo'			=> $comboperiodo,
						 	'selectperiodo'			=> $selectperiodo,
						 	'comboperiodofin'		=> $comboperiodofin,
						 	'selectperiodofin'		=> $selectperiodofin,

						 	'comboprocedencia'		=> $comboprocedencia,
						 	'selectprocedencia'	 	=> $selectprocedencia,

						 	'combodistrito'		=> $combodistrito,
						 	'selectdistrito'	 	=> $selectdistrito,

						 	'comboestado'			=> $comboestado,
						 	'selectestado'	 		=> $selectestado,

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
		$estado_id 				=   $request['estado_id'];
		//dd($selectperiodofin);

        $comboperiodo       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','TODO','APAFA_CONEI_PERIODO');
        $selectperiodo      	=   $periodo_id;
        $comboprocedencia   	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','TODO','APAFA_CONEI');
        $selectprocedencia  	=   $procedencia_id;


        $arraydata   			=   $this->gn_array_certificados($periodo_id,$selectperiodofin,$selectprocedencia,$estado_id);
        $listadatos     		=   $this->con_lista_certificados_xfiltro($arraydata);
        $listacertificadoperiod =   $this->con_lista_certificados_xperiodo($arraydata);
        $listacertificadoproced =   $this->con_lista_certificados_xprocedencia($arraydata);
        $listacertificadoestado =   $this->con_lista_certificados_xestado($arraydata);


        $listacertificadodistrito =   $this->con_lista_certificados_xdistrito($arraydata);

        //dd($listacertificadoperiod);
		$funcion 				= 	$this;
		
		return View::make('reportes/ajax/alistacertificadoinstitucion',
						 [

						 	'listadatos' 			=> $listadatos,
						 	'listacertificadoperiod'=> $listacertificadoperiod,
						 	'listacertificadoproced'=> $listacertificadoproced,
						 	'listacertificadoestado'=> $listacertificadoestado,
						 	'listacertificadodistrito'=> $listacertificadodistrito,
						 	'funcion'				=> $funcion,	
						 	'idopcion'				=> $idopcion,		 	
						 	'ajax' 					=> true,						 	
						 ]);
	}

	public function actionAjaxListarInstitucionSinCertificado(Request $request)
	{

		$periodo_id 			=   $request['periodo_id'];
		$procedencia_id 		=   $request['procedencia_id'];
		$idopcion 				=   $request['idopcion'];
        $periodo 				=	Estado::where('id','=',$periodo_id)->first();
        $procedencia 			=	Estado::where('id','=',$procedencia_id)->first();
        $selectestado  			=   'CEES00000001';

		$listainstituciones  	=   Institucion::where('id','<>','1CIX00000001')->get();
        $listaiscertificado   	=   $this->gn_instituciones_sin_certificados($periodo_id,$procedencia_id,$selectestado);
        $listacertificadodistrito =   $this->con_lista_sin_certificados_xdistrito($periodo_id,$procedencia_id,$selectestado);
        //dd($listacertificadoperiod);
		$funcion 				= 	$this;
		
		return View::make('reportes/ajax/alistasincertificadoinstitucion',
						 [

						 	'periodo' 				=> $periodo,
						 	'procedencia'			=> $procedencia,
						 	'listainstituciones'	=> $listainstituciones,
						 	'listaiscertificado'	=> $listaiscertificado,
						 	'listacertificadodistrito'	=> $listacertificadodistrito,
						 	'funcion'				=> $funcion,	
						 	'idopcion'				=> $idopcion,		 	
						 	'ajax' 					=> true,						 	
						 ]);
	}


	public function actionListarSinCertificadosInstituciones($idopcion)
	{

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion,'Ver');
	    if($validarurl <> 'true'){return $validarurl;}
	    /******************************************************/
	    View::share('titulo','Reporte de instituciones sin certificados');
	    $sel_periodo 			=	'';
	    $sel_periodo_fin 		=	'';
	    $anio  					=   $this->anio;

        $comboperiodo       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','','','APAFA_CONEI_PERIODO');
        $comboperiodofin       	=   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo Fin','','APAFA_CONEI_PERIODO');

        $periodo 				=	Estado::where('nombre','=',$this->anio)->first();
        $selectperiodo      	=   $periodo->id;
        $selectperiodofin      	=   '';

        $comboprocedencia   	=   $this->gn_generacion_combo_tabla('estados','id','nombre','','','APAFA_CONEI');
        $selectprocedencia  	=   'APCN00000001';
        $procedencia 			=	Estado::where('id','=',$selectprocedencia)->first();


        $arrayestado            =   array('CEES00000001','CEES00000005','CEES00000007','CEES00000008');
        $comboestado   			=   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','','','CERTIFICADO_ESTADO',$arrayestado);
        $selectestado  			=   'CEES00000001';
        $estado 				=	Estado::where('id','=',$selectestado)->first();


        $combodistrito   		=   $this->gn_generacion_combo_institucion_distrito('Seleccione distrito','TODO');
        $selectdistrito  		=   'TODO';

		$listainstituciones  	=   Institucion::where('id','<>','1CIX00000001')->get();
        $listaiscertificado   	=   $this->gn_instituciones_sin_certificados($selectperiodo,$selectprocedencia,$selectestado);

        $listacertificadodistrito =   $this->con_lista_sin_certificados_xdistrito($selectperiodo,$selectprocedencia,$selectestado);
		$funcion 				= 	$this;
		
		return View::make('reportes/listacertificadosininstitucion',
						 [

						 	'comboperiodo'			=> $comboperiodo,
						 	'selectperiodo'			=> $selectperiodo,
						 	'comboperiodofin'		=> $comboperiodofin,
						 	'selectperiodofin'		=> $selectperiodofin,
						 	'comboprocedencia'		=> $comboprocedencia,
						 	'selectprocedencia'	 	=> $selectprocedencia,
						 	'listainstituciones'	=> $listainstituciones,
						 	'listaiscertificado'	=> $listaiscertificado,
						 	'listacertificadodistrito'	=> $listacertificadodistrito,

						 	'periodo'	 			=> $periodo,
						 	'procedencia'			=> $procedencia,
						 	'estado'				=> $estado,

						 	'combodistrito'			=> $combodistrito,
						 	'selectdistrito'	 	=> $selectdistrito,
						 	'comboestado'			=> $comboestado,
						 	'selectestado'	 		=> $selectestado,

						 	'idopcion' 				=> $idopcion,
						 	'funcion' 				=> $funcion,						 	
						 ]);
	}





}
