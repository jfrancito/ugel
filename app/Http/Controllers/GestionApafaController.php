<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Requerimiento;
use App\Modelos\Institucion;
use App\Modelos\Director;
use App\Modelos\Archivo;
use App\Modelos\Apafa;
use App\Modelos\Estado;
use App\Modelos\OtroIntegranteApafa;
use App\Modelos\DetalleCertificado;
use App\Modelos\DocumentosAsociado;
use App\Modelos\Certificado;



use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\ApafaConeiTraits;
use Hashids;
use SplFileInfo;

class GestionApafaController extends Controller
{
    use GeneralesTraits;
    use ApafaConeiTraits;


    public function actionListarApafa($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista Requerimiento APAFA');

        $user_id        =   Session::get('usuario')->id;
        $listadatos     =   $this->con_lista_apafa();
        $funcion        =   $this;

        return View::make('requerimiento/listapafa',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }


    public function actionAgregarApafa($idopcion,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Requerimiento APAFA');
        if($_POST)
        {

            $institucion_id                             =   Session::get('institucion')->id;;
            $periodo_id                                 =   $request['periodo_id'];
            $periodofin_id                              =   $request['periodofin_r_id'];

            $institucion                                =   Institucion::where('id','=',$institucion_id)->first();
            $periodo                                    =   Estado::where('id','=',$periodo_id)->first();
            $periodofin                                 =   Estado::where('id','=',$periodofin_id)->first();

            if(count($periodofin)>0){
                $nombreperiodog                         =   $periodo->nombre . '-' .$periodofin->nombre;
            }else{
                $nombreperiodog                         =   $periodo->nombre;
            }


            $idrequerimiento                            =   $this->funciones->getCreateIdMaestra('apafas');
            $codigo                                     =   $this->funciones->generar_codigo('apafas',8);

            $cabecera                                   =   new Apafa;
            $cabecera->id                               =   $idrequerimiento;
            $cabecera->codigo                           =   $codigo;
            $cabecera->institucion_id                   =   $institucion_id;
            $cabecera->institucion_nombre               =   $institucion->nombre;
            $cabecera->estado_id                        =   'CEES00000005';
            $cabecera->estado_nombre                    =   'EN PROCESO';
            $cabecera->periodo_nombre                   =   $nombreperiodog;
            $cabecera->fecha_crea                       =   $this->fechaactual;
            $cabecera->usuario_crea                     =   Session::get('usuario')->id;
            $cabecera->save();

            $usuario                                    =   User::where('id',Session::get('usuario')->id)->first();
            

            //OTRO REPRESENTANTE
            $array_detalle_producto_request             =   json_decode($request['array_detalle_producto'],true);
            foreach($array_detalle_producto_request as $item => $row) {

                $idoi                                   =   $this->funciones->getCreateIdMaestra('otrointegranteapafas');
                $oi                                     =   new OtroIntegranteApafa;
                $oi->id                                 =   $idoi;
                $oi->conei_id                           =   $idrequerimiento;
                $oi->representante_id                   =   $row['representante_id'];
                $oi->representante_nombre               =   $row['representante_txt']; 
                $oi->tipo_documento_id                  =   $row['tdg'];
                $oi->tipo_documento_nombre              =   $row['tdgtexto']; 
                $oi->documento                          =   $row['documentog'];
                $oi->nombres                            =   $row['nombresg'];
                $oi->nivel_id                           =   $row['codigo_modular_id'];
                $oi->nivel_nombre                       =   $row['niveltexto'];
                $oi->cargo                              =   $row['dcargoni'];
                $oi->ind_unico                          =   0;
                $oi->tipo                               =   'CONSEJO_DIRECTIVO';
                $oi->fecha_crea                         =   $this->fechaactual;
                $oi->usuario_crea                       =   Session::get('usuario')->id;
                $oi->save();

            }


            //OTRO REPRESENTANTE
            $array_detalle_producto_request             =   json_decode($request['array_detalle_vigilancia'],true);
            foreach($array_detalle_producto_request as $item => $row) {

                $idoi                                   =   $this->funciones->getCreateIdMaestra('otrointegranteapafas');
                $oi                                     =   new OtroIntegranteApafa;
                $oi->id                                 =   $idoi;
                $oi->conei_id                           =   $idrequerimiento;
                $oi->representante_id                   =   $row['representante_id'];
                $oi->representante_nombre               =   $row['representante_txt']; 
                $oi->tipo_documento_id                  =   $row['tdg'];
                $oi->tipo_documento_nombre              =   $row['tdgtexto']; 
                $oi->documento                          =   $row['documentog'];
                $oi->nombres                            =   $row['nombresg'];
                $oi->nivel_id                           =   $row['codigo_modular_id'];
                $oi->nivel_nombre                       =   $row['niveltexto'];
                $oi->cargo                              =   $row['dcargoni'];
                $oi->ind_unico                          =   0;
                $oi->tipo                               =   'CONSEJO_VIGILANCIA';
                $oi->fecha_crea                         =   $this->fechaactual;
                $oi->usuario_crea                       =   Session::get('usuario')->id;
                $oi->save();

            }





            $tarchivos                                  =  DocumentosAsociado::where('activo','=','1')->where('id','=','APCN00000001')->get();

            foreach($tarchivos as $index=>$item){
                //01
                $files                                      =   $request[$item->cod_archivo];
                if(!is_null($files)){
                    foreach($files as $file){

                        $listadetalledoc            =   Archivo::where('referencia_id','=',$idrequerimiento)
                                                        ->get();

                        $rutafile                   =   storage_path('app/').$this->pathFilesApafa.$codigo.'/';
                        $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);
                        $numero                     =   count($listadetalledoc)+1;
                        $nombre                     =   $codigo.'-'.$numero.'-'.$file->getClientOriginalName();

                        $rutadondeguardar           =   $this->pathFilesApafa.$codigo.'/';
                        $urlmedio                   =   'app/'.$rutadondeguardar.$nombre;

                        $nombreoriginal             =   $file->getClientOriginalName();
                        $info                       =   new SplFileInfo($nombreoriginal);
                        $extension                  =   $info->getExtension();

                        copy($file->getRealPath(),$rutafile.$nombre);
                        $idarchivo                  =   $this->funciones->getCreateIdMaestra('archivos');

                        $dcontrol                   =   new Archivo;
                        $dcontrol->id               =   $idarchivo;
                        $dcontrol->size             =   filesize($file);
                        $dcontrol->extension        =   $extension;
                        $dcontrol->lote             =   $codigo;
                        $dcontrol->referencia_id    =   $idrequerimiento;
                        $dcontrol->nombre_archivo   =   $nombre;
                        $dcontrol->url_archivo      =   $urlmedio;
                        $dcontrol->area_id          =   '';
                        $dcontrol->area_nombre      =   '';
                        $dcontrol->periodo_id       =   '';
                        $dcontrol->periodo_nombre   =   '';
                        $dcontrol->codigo_doc       =   $item->cod_archivo;
                        $dcontrol->nombre_doc       =   $item->nombre_archivo;
                        $dcontrol->usuario_nombre   =   $usuario->nombre;
                        $dcontrol->tipo_archivo     =   'requerimiento_apafa';
                        $dcontrol->fecha_crea       =   $this->fechaactual;
                        $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                        $dcontrol->save();
                    }
                }

            }


            //AGREGAR CERTIFICADO
            $procedencia_id                 =  'APCN00000001';
            $procedencia                    =   Estado::where('id','=',$procedencia_id)->first();
            $idcertificado                  =   $this->funciones->getCreateIdMaestra('certificados');
            $codigo                         =   $this->funciones->generar_codigo('certificados',8);

            $cabecera                       =   new Certificado();
            $cabecera->id                   =   $idcertificado;
            $cabecera->codigo               =   $codigo;
            $cabecera->institucion_id       =   $institucion_id;
            $cabecera->institucion_codigo   =   $institucion->codigo;
            $cabecera->institucion_nombre   =   $institucion->nombre;
            $cabecera->institucion_nivel    =   $institucion->nivel;
            $cabecera->periodo_nombre       =   $nombreperiodog;
            $cabecera->procedente_id        =   $procedencia_id;
            $cabecera->procedente_nombre    =   $procedencia->nombre;
            $cabecera->estado_id            =   'CEES00000005';
            $cabecera->estado_nombre        =   'EN PROCESO';
            $cabecera->referencia_id        =   $idrequerimiento;

            $cabecera->fecha_crea           =   $this->fechaactual;
            $cabecera->usuario_crea         =   Session::get('usuario')->id;
            $cabecera->save();


            $iddetcertificado               =   $this->funciones->getCreateIdMaestra('detallecertificados');
            //primer periodo
            $cabeceradet                       =   new DetalleCertificado();
            $cabeceradet->id                   =   $iddetcertificado;
            $cabeceradet->codigo               =   $codigo;
            $cabeceradet->institucion_id       =   $institucion_id;
            $cabeceradet->institucion_codigo   =   $institucion->codigo;
            $cabeceradet->institucion_nombre   =   $institucion->nombre;
            $cabeceradet->institucion_nivel    =   $institucion->nivel;
            $cabeceradet->periodo_id           =   $periodo->id;    
            $cabeceradet->periodo_nombre       =   $periodo->nombre;
            $cabeceradet->procedente_id        =   $procedencia_id;
            $cabeceradet->certificado_id       =   $idcertificado;
            $cabeceradet->procedente_nombre    =   $procedencia->nombre;
            $cabeceradet->inicio_fin           =   'I';
            $cabeceradet->referencia_id        =   $idrequerimiento;
            $cabeceradet->estado_id            =   'CEES00000005';
            $cabeceradet->estado_nombre        =   'EN PROCESO';
            $cabeceradet->fecha_crea           =   $this->fechaactual;
            $cabeceradet->usuario_crea         =   Session::get('usuario')->id;
            $cabeceradet->save();


            if(count($periodofin)>0){

                $iddetcertificado               =   $this->funciones->getCreateIdMaestra('detallecertificados');
                //primer periodo
                $cabeceradet                       =   new DetalleCertificado();
                $cabeceradet->id                   =   $iddetcertificado;
                $cabeceradet->codigo               =   $codigo;
                $cabeceradet->institucion_id       =   $institucion_id;
                $cabeceradet->institucion_codigo   =   $institucion->codigo;
                $cabeceradet->institucion_nombre   =   $institucion->nombre;
                $cabeceradet->institucion_nivel    =   $institucion->nivel;
                $cabeceradet->periodo_id           =   $periodofin->id;    
                $cabeceradet->periodo_nombre       =   $periodofin->nombre;
                $cabeceradet->procedente_id        =   $procedencia_id;
                $cabeceradet->certificado_id       =   $idcertificado;
                $cabeceradet->procedente_nombre    =   $procedencia->nombre;
                $cabeceradet->inicio_fin           =   'F';
                $cabeceradet->referencia_id        =   $idrequerimiento;
                $cabeceradet->estado_id            =   'CEES00000005';
                $cabeceradet->estado_nombre        =   'EN PROCESO';
                $cabeceradet->fecha_crea           =   $this->fechaactual;
                $cabeceradet->usuario_crea         =   Session::get('usuario')->id;
                $cabeceradet->save();  
                
            }


            //dd("exitoxo");

            return Redirect::to('/gestion-apafa/'.$idopcion)->with('bienhecho', 'Requerimiento '.$codigo.' registrado con exito');

        }else{


            $institucion_id             =   Session::get('usuario')->institucion_id;
            $institucion                =   Institucion::where('id','=',$institucion_id)->first();
            $director                   =   Director::where('institucion_id','=',$institucion_id)->where('activo','=','1')->first();
            $combotd                    =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
            $selecttd                   =   'TIDO00000001';


            //AUTOLLENAR EL DIRECTOR
            $director_i_tipodocumento_id        = 'TIDO00000001';
            $director_i_tipodocumento_nombre    = 'DNI';
            $director_i_representante_id        = 'ESRP00000001';
            $director_i_representante_nombre    = 'DIRECTOR';
            $director_i_documento               =  $director->dni;
            $director_i_nombres                 =  $director->nombres;
            $array_detalle_producto             =   array();
            $arraynuevo                         =   array();

            $disabled                   =   false;



            $procedencia_id             =   'APCN00000001';
            $array_periodos             =   DetalleCertificado::where('institucion_id','=',$institucion_id)
                                            ->where('procedente_id','=',$procedencia_id)
                                            ->where('activo','=',1)
                                            ->whereIn('estado_id',['CEES00000001','CEES00000005','CEES00000008'])
                                            ->pluck('periodo_id')                                   
                                            ->toArray();

            $comboperiodo               =   $this->gn_generacion_combo_tabla_not_array('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO',$array_periodos);
            $selectperiodo              =   '';
            $comboperiodofin            =   $this->gn_generacion_combo_tabla_not_array('estados','id','nombre','Seleccione periodo fin','','APAFA_CONEI_PERIODO',$array_periodos);
            $selectperiodofin           =   '';
            $ind                        =   0;
            $checked                    =   false;
            $mensaje                    =   'SELECCIONE PERIODOS';
            $color                      =   '';

            $tarchivos                  =   DocumentosAsociado::where('activo','=','1')->where('id','=','APCN00000001')->get();
            $arrayrepresentante         =   $this->array_representante_obligatrio_apafa(Session::get('institucion')->tipo_institucion);

            $robligatorios              =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA')
                                            ->whereIn('id',$arrayrepresentante)
                                            ->get();   

            $lrepresentantes            =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA')->get();
            $array_detalle_vigilancia   =   array();


            $arrayrepresentantevi       =   $this->array_representante_obligatrio_apafa_vigilancia(Session::get('institucion')->tipo_institucion);
            $robligatoriosvi             =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA_VIGILANCIA')
                                            ->whereIn('id',$arrayrepresentantevi)
                                            ->get();   

            //dd($robligatoriosvi);                             

            return View::make('requerimiento.agregarapafa',
                        [
                            'array_detalle_producto'                =>  $array_detalle_producto,
                            'array_detalle_vigilancia'              =>  $array_detalle_vigilancia,


                            'robligatorios'                         =>  $robligatorios,
                            'robligatoriosvi'                       =>  $robligatoriosvi,

                            'idopcion'                              =>  $idopcion,
                            'institucion'                           =>  $institucion,
                            'director'                              =>  $director,
                            'combotd'                               =>  $combotd,
                            'comboperiodo'                          =>  $comboperiodo,
                            'selectperiodo'                         =>  $selectperiodo,
                            'comboperiodofin'                       =>  $comboperiodofin,
                            'selectperiodofin'                      =>  $selectperiodofin,
                            'ind'                                   =>  $ind,
                            'selecttd'                              =>  $selecttd,
                            'disabled'                              =>  $disabled,
                            'checked'                               =>  $checked,
                            'mensaje'                               =>  $mensaje,
                            'color'                                 =>  $color,

                            'procedencia_id'                        =>  $procedencia_id,
                            'tarchivos'                             =>  $tarchivos,
                            'lrepresentantes'                       =>  $lrepresentantes,
                            'arrayrepresentante'                    =>  $arrayrepresentante,

                            'director_i_tipodocumento_id'           =>  $director_i_tipodocumento_id,
                            'director_i_tipodocumento_nombre'           =>  $director_i_tipodocumento_nombre,

                            'director_i_representante_id'           =>  $director_i_representante_id,
                            'director_i_representante_nombre'           =>  $director_i_representante_nombre,

                            'director_i_documento'                  =>  $director_i_documento,
                            'director_i_nombres'                    =>  $director_i_nombres,
                        ]);
        }
    }


    public function actionListaTablaOIApafa(Request $request)
    {

        $tdg                                =   $request['tdg'];
        $tdgtexto                           =   $request['tdgtexto'];
        $documentog                         =   $request['documentog'];
        $nombresg                           =   $request['nombresg'];
        $dcargoni                           =   $request['dcargoni'];
        $representante_id                   =   $request['representante_id'];
        $representante_txt                  =   $request['representante_txt'];

        $codigo_modular_id                  =   $request['codigo_modular_id'];
        $niveltexto                         =   $request['niveltexto'];

        $array_detalle_producto_request     =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto             =   array();

        $fila                               =   count($array_detalle_producto_request) + 1;

        $arraynuevo                         =   array(
                                                    "fila"          => $fila,
                                                    "tdg"           => $tdg,
                                                    "tdgtexto"      => $tdgtexto,
                                                    "documentog"    => $documentog,
                                                    "nombresg"      => $nombresg,
                                                    "dcargoni"      => $dcargoni,
                                                    "representante_id"      => $representante_id,
                                                    "representante_txt"      => $representante_txt,
                                                    "codigo_modular_id"      => $codigo_modular_id,
                                                    "niveltexto"      => $niveltexto

                                                );

        array_push($array_detalle_producto,$arraynuevo);

        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }

        $array_detalle_producto     =   $this->ordernar_array($array_detalle_producto);

        $arrayrepresentante         =   $this->array_representante_obligatrio_apafa(Session::get('institucion')->tipo_institucion);

        $robligatorios              =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA')
                                        ->whereIn('id',$arrayrepresentante)
                                        ->get();   

        $funcion                    =   $this;

        return View::make('requerimiento/ajax/alistaoiapafa',
                         [
                            'array_detalle_producto'    =>  $array_detalle_producto,
                            'robligatorios'             =>  $robligatorios,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }


    public function actionListaTablaOIApafaVi(Request $request)
    {

        $tdg                                =   $request['tdg'];
        $tdgtexto                           =   $request['tdgtexto'];
        $documentog                         =   $request['documentog'];
        $nombresg                           =   $request['nombresg'];
        $dcargoni                           =   $request['dcargoni'];
        $representante_id                   =   $request['representante_id'];
        $representante_txt                  =   $request['representante_txt'];

        $codigo_modular_id                  =   $request['codigo_modular_id'];
        $niveltexto                         =   $request['niveltexto'];

        $array_detalle_producto_request     =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto             =   array();

        $fila                               =   count($array_detalle_producto_request) + 1;

        $arraynuevo                         =   array(
                                                    "fila"          => $fila,
                                                    "tdg"           => $tdg,
                                                    "tdgtexto"      => $tdgtexto,
                                                    "documentog"    => $documentog,
                                                    "nombresg"      => $nombresg,
                                                    "dcargoni"      => $dcargoni,
                                                    "representante_id"      => $representante_id,
                                                    "representante_txt"      => $representante_txt,
                                                    "codigo_modular_id"      => $codigo_modular_id,
                                                    "niveltexto"      => $niveltexto

                                                );

        array_push($array_detalle_producto,$arraynuevo);

        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }

        $array_detalle_producto     =   $this->ordernar_array($array_detalle_producto);

        $arrayrepresentante         =   $this->array_representante_obligatrio_apafa(Session::get('institucion')->tipo_institucion);

        $robligatoriosvi              =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA_VIGILANCIA')
                                        ->whereIn('id',$arrayrepresentante)
                                        ->get();   

        $funcion                    =   $this;

        return View::make('requerimiento/ajax/alistaoiapafavi',
                         [
                            'array_detalle_vigilancia'    =>  $array_detalle_producto,
                            'robligatoriosvi'             =>  $robligatoriosvi,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }

    public function actionEliminarFilaTablaOIApafa(Request $request)
    {

        $fila                                =   $request['fila'];
        $array_detalle_producto_request      =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto              =   array();

        //eliminar la fila del array
        foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['fila'] == $fila) {
                unset($array_detalle_producto_request[$key]);
            }
        }

        $cont = 1;
        foreach ($array_detalle_producto_request as $key => $item) {
            $array_detalle_producto_request[$key]['fila'] = $cont;
            $cont = $cont +1;
        }

        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }
        $array_detalle_producto     =   $this->ordernar_array($array_detalle_producto);
        $funcion                =   $this;

        $arrayrepresentante         =   $this->array_representante_obligatrio_apafa(Session::get('institucion')->tipo_institucion);

        $robligatorios              =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA')
                                        ->whereIn('id',$arrayrepresentante)
                                        ->get();   

        return View::make('requerimiento/ajax/alistaoiapafa',
                         [
                            'array_detalle_producto'    =>  $array_detalle_producto,
                            'robligatorios'             =>  $robligatorios,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }


    public function actionEliminarFilaTablaOIApafaVi(Request $request)
    {

        $fila                                =   $request['fila'];
        $array_detalle_producto_request      =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto              =   array();

        //eliminar la fila del array
        foreach ($array_detalle_producto_request as $key => $item) {
            if((int)$item['fila'] == $fila) {
                unset($array_detalle_producto_request[$key]);
            }
        }

        $cont = 1;
        foreach ($array_detalle_producto_request as $key => $item) {
            $array_detalle_producto_request[$key]['fila'] = $cont;
            $cont = $cont +1;
        }

        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }
        $array_detalle_producto     =   $this->ordernar_array($array_detalle_producto);
        $funcion                =   $this;

        $arrayrepresentante         =   $this->array_representante_obligatrio_apafa(Session::get('institucion')->tipo_institucion);

        $robligatoriosvi              =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE_APAFA_VIGILANCIA')
                                        ->whereIn('id',$arrayrepresentante)
                                        ->get();   

        return View::make('requerimiento/ajax/alistaoiapafavi',
                         [
                            'array_detalle_vigilancia'    =>  $array_detalle_producto,
                            'robligatoriosvi'             =>  $robligatoriosvi,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }

    public function actionModalConfirmarRegistroApafa(Request $request)
    {

        $fila                                       =   $request['fila'];
        $institucion_id                             =   $request['institucion_id'];
        $director_id                                =   $request['director_id'];
        $institucion                                =   Institucion::where('id','=',$institucion_id)->first();
        $director                                   =   Director::where('id','=',$director_id)->where('activo','=','1')->first();

        $periodo_id                                 =   $request['periodo_id'];
        $periodofin_id                              =   $request['periodofin_id'];
        $periodoinicio                              =   Estado::where('id','=',$periodo_id)->first();
        $periodofin                                 =   Estado::where('id','=',$periodofin_id)->first();

        $array_detalle_producto_request             =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto                     =   array();
        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }
        $array_detalle_producto     =   $this->ordernar_array($array_detalle_producto);


        $array_detalle_vigilancia_request           =   json_decode($request['array_detalle_vigilancia'],true);
        $array_detalle_vigilancia                     =   array();
        if(count($array_detalle_vigilancia_request)>0){
            foreach ($array_detalle_vigilancia_request as $key => $item) {
                array_push($array_detalle_vigilancia,$item);
            }
        }
        $array_detalle_vigilancia     =   $this->ordernar_array($array_detalle_vigilancia);


        //DD($array_detalle_producto);

        //$data_o                                     =   $request['data_o'];
        $funcion                                    =   $this;

        //dd($institucion);

        return View::make('requerimiento/modal/ajax/alistacertificadoapafa',
                         [
                            'array_detalle_producto'                        =>  $array_detalle_producto,
                             'array_detalle_vigilancia'                        =>  $array_detalle_vigilancia,
                            //'data_o'                                        =>  $data_o,
                            'institucion'                                   =>  $institucion,
                            'director'                                      =>  $director,

                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }


    public function actionModalRegistroOIApafa(Request $request)
    {

        $funcion                =   $this;
        $combotd                =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd               =   'TIDO00000001';
        $representante_sel_id   =   $request['representante_sel_id'];


        $arraynotr              =   array($representante_sel_id);
        $arraynotr              =   array('ESRP00000001');
        $comboor                =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','','','ESTADO_REPRESENTANTE_APAFA',$arraynotr);
        $comboor                =   $this->gn_generacion_combo_tabla_not_array('estados','id','nombre','','','ESTADO_REPRESENTANTE_APAFA',$arraynotr);
        $selector               =   'ESRP00000002';
        $combonivel             =   $this->gn_generacion_combo_niveles(Session::get('institucion')->codigo);
        $selectnivel            =   '';

        return View::make('requerimiento/modal/ajax/amregistrooiapafa',
                         [
                            'combotd'           =>  $combotd,
                            'selecttd'          =>  $selecttd,

                            'combonivel'        =>  $combonivel,
                            'selectnivel'       =>  $selectnivel,

                            'comboor'           =>  $comboor,
                            'selector'          =>  $selector,
                            'representante_sel_id' =>  $representante_sel_id,

                            'funcion'           =>  $funcion,
                            'ajax'              =>  true
                         ]);
    }


    public function actionModalRegistroOIApafaVi(Request $request)
    {

        $funcion                =   $this;
        $combotd                =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd               =   'TIDO00000001';
        $representante_sel_id   =   $request['representante_sel_id'];


        $arraynotr              =   array($representante_sel_id);
        $arraynotr              =   array('ESRP00000001');
        $comboor                =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','','','ESTADO_REPRESENTANTE_APAFA_VIGILANCIA',$arraynotr);
        $comboor                =   $this->gn_generacion_combo_tabla_not_array('estados','id','nombre','','','ESTADO_REPRESENTANTE_APAFA_VIGILANCIA',$arraynotr);
        $selector               =   'ESRP00000002';
        $combonivel             =   $this->gn_generacion_combo_niveles(Session::get('institucion')->codigo);
        $selectnivel            =   '';

        return View::make('requerimiento/modal/ajax/amregistrooiapafavi',
                         [
                            'combotd'           =>  $combotd,
                            'selecttd'          =>  $selecttd,

                            'combonivel'        =>  $combonivel,
                            'selectnivel'       =>  $selectnivel,

                            'comboor'           =>  $comboor,
                            'selector'          =>  $selector,
                            'representante_sel_id' =>  $representante_sel_id,

                            'funcion'           =>  $funcion,
                            'ajax'              =>  true
                         ]);
    }


}
