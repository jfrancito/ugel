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
use App\Modelos\Archivo;
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
use setasign\Fpdi\Fpdi;
use iio\libmergepdf\Merger;

class ReporteCertificadoController extends Controller
{

	use GeneralesTraits;
	use ReporteTraits;
    use CertificadoTraits;



	public function actionUnirpdf(Request $request)
	{


		// $inputFile = storage_path('app/requerimiento_conei/00000001/00000001-7-DNI.pdf');

		// // Ruta al archivo PDF de salida
		// $outputFile = storage_path('app/pdf/output.pdf');

		// // Comando Ghostscript
		// $gsCommand = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile $inputFile";

		// dd($gsCommand);


		// exec($gsCommand, $output, $return_var);

		// if ($return_var !== 0) {
		//     echo "Error al convertir el PDF. Código de salida: $return_var";
		//     echo "Salida detallada: " . implode("\n", $output);
		// } else {
		//     echo "PDF convertido exitosamente.";
		// }

		// dd("excelnte");

		$archivos 			=	Archivo::where('tipo_archivo','=','requerimiento_conei')
								->where('referencia_id','=','1CIX00000001')
								//->where('id','<>','1CIX00000380')
								->where('activo','=','1')->get();
	    $merger = new Merger;
	    foreach ($archivos as $file) {
	    	$ruta = str_replace("app/", "", $file->url_archivo);
		    $merger->addFile(Storage::path($ruta));
	    }



	    // Guardar el archivo combinado
	    $combinedPdf = $merger->merge();
	    $outputFile = storage_path('app/pdf/merged_file.pdf');
	    file_put_contents($outputFile, $combinedPdf);
	    return response()->download($outputFile);

		// //dd($archivos);
	    // // Crear una instancia de FPDI
	    // $pdf = new Fpdi();
	    // foreach ($archivos as $file) {
	    //     // Obtener el archivo desde el almacenamiento
	    //     $filePath = storage_path($file->url_archivo);
	        
	    //     // Contar el número de páginas en el PDF
	    //     $pageCount = $pdf->setSourceFile($filePath);
	        
	    //     // Importar cada página del archivo
	    //     for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
	    //         $templateId = $pdf->importPage($pageNo);
	    //         $size = $pdf->getTemplateSize($templateId);

	    //         // Crear una nueva página con las mismas dimensiones
	    //         $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
	            
	    //         // Usar la página importada
	    //         $pdf->useTemplate($templateId);
	    //     }
	    // }

	    // // Guardar el PDF combinado en el almacenamiento
	    // $outputFile = storage_path('app/public/pdf/merged_file.pdf');
	    // $pdf->Output($outputFile, 'F');

	    // return response()->download($outputFile);

	}




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
