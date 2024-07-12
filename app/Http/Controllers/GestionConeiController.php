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
use App\Modelos\Conei;
use App\Modelos\Estado;
use App\Modelos\OtroIntegranteConei;
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

class GestionConeiController extends Controller
{
    use GeneralesTraits;
    use ApafaConeiTraits;


    public function actionDescargarArchivosRequerimiento($idopcion,$idregistro,$idarchivo)
    {

        $registro_id = $this->funciones->decodificarmaestra($idarchivo);
        $user_id    = Session::get('usuario')->id;

        View::share('titulo','Eliminar Archivos del Requerimiento');

        try{
            // DB::beginTransaction();
            $archivo                =   Archivo::where('id','=',$registro_id)->first();
            $storagePath            = storage_path('app\\'.$this->pathFiles.$archivo->lote.'\\'.$archivo->nombre_archivo);
            if(is_file($storagePath))
            {       
                    // return Response::download($rutaArchivo);
                    return response()->download($storagePath);
            }
            
            // DB::commit();
        }catch(\Exception $ex){
            // DB::rollback(); 
            $sw =   1;
            $mensaje  = $this->ge_getMensajeError($ex);
            dd('archivo no encontrado');

        }
        
    }


    public function actionDetalleConei($idopcion, $idconei, Request $request) {

        View::share('titulo','DETALLE CONEI');
        $idconei    =   $this->funciones->decodificarmaestra($idconei);
        $conei      =    Conei::where('id','=',$idconei)
                        ->first();
        $institucion=   Institucion::where('id','=',$conei->institucion_id)->first();
        $listaoic   =   OtroIntegranteConei::where('conei_id','=',$idconei)->orderby('representante_nombre','asc')->get();
        $larchivos  =   Archivo::where('referencia_id','=',$idconei)->where('tipo_archivo','=','requerimiento_conei')->get();
        $funcion    =   $this;


        return View::make('requerimiento/verdetalleconei',
                         [
                            'conei'             =>  $conei,
                            'institucion'       =>  $institucion,
                            'listaoic'          =>  $listaoic,
                            'larchivos'         =>  $larchivos,
                            'unidad'            =>  $this->unidadmb,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }



    public function actionListarConei($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista Requerimiento CONEI');

        $user_id        =   Session::get('usuario')->id;
        $listadatos     =   $this->con_lista_conei();
        $funcion        =   $this;

        return View::make('requerimiento/listaconei',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }
    public function actionEliminarFilaTablaOI(Request $request)
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

        return View::make('requerimiento/ajax/alistaoiconei',
                         [
                            'array_detalle_producto'    =>  $array_detalle_producto,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }
    public function actionModalConfirmarRegistro(Request $request)
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
        //DD($array_detalle_producto);

        $data_o                                     =   $request['data_o'];
        $funcion                                    =   $this;

        //dd($data_o);

        return View::make('requerimiento/modal/ajax/alistacertificado',
                         [
                            'array_detalle_producto'                        =>  $array_detalle_producto,
                            'data_o'                                        =>  $data_o,
                            'institucion'                                   =>  $institucion,
                            'director'                                      =>  $director,

                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }

    public function actionListaTablaOI(Request $request)
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

        $funcion                    =   $this;

        return View::make('requerimiento/ajax/alistaoiconei',
                         [
                            'array_detalle_producto'    =>  $array_detalle_producto,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }


    public function actionModalRegistroOI(Request $request)
    {

        $funcion                =   $this;
        $combotd                =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd               =   'TIDO00000001';
        $representante_sel_id   =   $request['representante_sel_id'];


        $arraynotr     =   array($representante_sel_id);
        $comboor       =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','','','ESTADO_REPRESENTANTE',$arraynotr);
        $selector      =   $representante_sel_id;

        $combonivel             =   $this->gn_generacion_combo_niveles(Session::get('institucion')->codigo);
        $selectnivel            =   '';


        return View::make('requerimiento/modal/ajax/amregistrooi',
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


    public function actionModalRegistro(Request $request)
    {

        $data_td_id             =   $request['data_td_id'];
        $data_td_no             =   $request['data_td_no'];
        $data_docu              =   $request['data_docu'];
        $data_nombre            =   $request['data_nombre'];
        $data_cod_modular       =   $request['data_cod_modular'];
        $data_nivel             =   $request['data_nivel'];

        $data_nombre_visible    =   $request['data_nombre_visible'];
        $data_titulo            =   $request['data_titulo'];

        $data_rp_id_val         =   $request['data_rp_id_val'];//SUB DIRECTOR // DOCENTE
        $data_rp_no_val         =   $request['data_rp_no_val'];
        $data_rp_id             =   $request['data_rp_id'];
        $data_rp_no             =   $request['data_rp_no'];

        $combonivel             =   $this->gn_generacion_combo_niveles(Session::get('institucion')->codigo);
        $selectnivel            =   '';

        $funcion                =   $this;
        $combotd                =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd               =   'TIDO00000001';

        return View::make('requerimiento/modal/ajax/amregistro',
                         [
                            'data_td_id'            =>  $data_td_id,
                            'data_td_no'            =>  $data_td_no,
                            'data_docu'             =>  $data_docu,
                            'data_nombre'           =>  $data_nombre,

                            'data_cod_modular'      =>  $data_cod_modular,
                            'data_nivel'            =>  $data_nivel,

                            'data_nombre_visible'   =>  $data_nombre_visible,
                            'data_titulo'           =>  $data_titulo,                           

                            'data_rp_id_val'        =>  $data_rp_id_val,
                            'data_rp_no_val'        =>  $data_rp_no_val,
                            'data_rp_id'            =>  $data_rp_id,
                            'data_rp_no'            =>  $data_rp_no,  

                            'combotd'               =>  $combotd,
                            'selecttd'              =>  $selecttd,

                            'combonivel'            =>  $combonivel,
                            'selectnivel'           =>  $selectnivel,

                            'funcion'               =>  $funcion,
                            'ajax'                  =>  true
                         ]);
    }


    public function actionModalEditarDirector(Request $request)
    {

        $director_id                =   $request['director_id'];
        $procedencia_id             =   $request['procedencia_id'];


        $director                   =   Director::where('id','=',$director_id)->first();
        $institucion                =   Institucion::where('id','=',$director->institucion_id)->first();
        $funcion                    =   $this;
        $data_titulo                =   'Editar Director';
        $combotd       =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd      =   'TIDO00000001';

        return View::make('requerimiento/modal/ajax/ameditardirector',
                         [
                            'director_id'           =>  $director_id,
                            'procedencia_id'        =>  $procedencia_id,
                            'director'              =>  $director,
                            'institucion'           =>  $institucion,
                            'data_titulo'           =>  $data_titulo,
                            'combotd'               =>  $combotd,
                            'selecttd'              =>  $selecttd,

                            'funcion'               =>  $funcion,
                            'ajax'                  =>  true
                         ]);
    }



    public function actionModalGuardarRegistroDirector(Request $request)
    {

        $dni                        =   $request['dni'];
        $nombres                    =   $request['nombres'];
        $telefono                   =   $request['telefono'];
        $correo                     =   $request['correo'];
        $director_id                =   $request['director_id'];
        $procedencia_id             =   $request['procedencia_id'];

        $director                   =   Director::where('id','=',$director_id)->first();
        $institucion                =   Institucion::where('id','=',$director->institucion_id)->first();

        $director->dni              =   $dni;
        $director->nombres          =   $nombres;
        $director->telefono         =   $telefono;
        $director->correo           =   $correo;
        $director->fecha_mod        =   $this->fechaactual;
        $director->usuario_mod      =   Session::get('usuario')->id;
        $director->save();

        $funcion                    =   $this;

        $item                       =   Estado::where('id','=','ESRP00000001')->first();
        $director                   =   Director::where('id','=',$director_id)->first();

        $director_i_tipodocumento_id = 'TIDO00000001';
        $director_i_tipodocumento_nombre = 'DNI';

        $director_i_representante_id = 'ESRP00000001';
        $director_i_representante_nombre = 'DIRECTOR';


        $director_i_documento        =  $director->dni;
        $director_i_nombres          =  $director->nombres;



        return View::make('requerimiento/modal/ajax/amdirectorform',
                         [
                            'director_id'           =>  $director_id,
                            'procedencia_id'        =>  $procedencia_id,
                            'director'              =>  $director,
                            'institucion'           =>  $institucion,
                            'director_i_tipodocumento_id'       =>  $director_i_tipodocumento_id,
                            'director_i_tipodocumento_nombre'       =>  $director_i_tipodocumento_nombre,
                            'director_i_representante_id'       =>  $director_i_representante_id,
                            'director_i_representante_nombre'       =>  $director_i_representante_nombre,
                            'director_i_documento'              =>  $director_i_documento,
                            'director_i_nombres'                =>  $director_i_nombres,
                            'item'                  =>  $item,
                            'funcion'               =>  $funcion
                         ]);
    }








    public function actionAgregarConei($idopcion,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Requerimiento CONEI');
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


            $idrequerimiento                            =   $this->funciones->getCreateIdMaestra('coneis');
            $codigo                                     =   $this->funciones->generar_codigo('coneis',8);

            $cabecera                                   =   new Conei;
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
            

            //FALTA LOS OBLIGATORIO
            $arrayrepresentante                         =   $this->array_representante_obligatrio(Session::get('institucion')->tipo_institucion);
            $lrepresentantes                            =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE')->whereIn('id',$arrayrepresentante)->get();

            foreach($lrepresentantes as $index=>$item){

                $_i_representante_id                    =   $request[$item->codigo.'_i_representante_id'];
                $_i_representante_nombre                =   $request[$item->codigo.'_i_representante_nombre'];
                $_i_tipodocumento_nombre                =   $request[$item->codigo.'_i_tipodocumento_nombre'];
                $_i_tipodocumento_id                    =   $request[$item->codigo.'_i_tipodocumento_id'];
                $_i_documento                           =   $request[$item->codigo.'_i_documento'];
                $_i_nombres                             =   $request[$item->codigo.'_i_nombres'];
                $_i_codigo_modular                      =   $request[$item->codigo.'_i_codigo_modular'];
                $_i_nivel                               =   $request[$item->codigo.'_i_nivel'];


                $idoi                                   =   $this->funciones->getCreateIdMaestra('otrointegranteconeis');
                $oi                                     =   new OtroIntegranteConei;
                $oi->id                                 =   $idoi;
                $oi->conei_id                           =   $idrequerimiento;
                $oi->representante_id                   =   $_i_representante_id;
                $oi->representante_nombre               =   $_i_representante_nombre; 
                $oi->tipo_documento_id                  =   $_i_tipodocumento_id ;
                $oi->tipo_documento_nombre              =   $_i_tipodocumento_nombre; 
                $oi->documento                          =   $_i_documento;
                $oi->nombres                            =   $_i_nombres;
                $oi->nivel_id                           =   $_i_codigo_modular;
                $oi->nivel_nombre                       =   $_i_nivel; 
                $oi->cargo                              =   '';
                $oi->ind_unico                          =   1;
                $oi->fecha_crea                         =   $this->fechaactual;
                $oi->usuario_crea                       =   Session::get('usuario')->id;
                $oi->save();

            }



            //OTRO REPRESENTANTE
            $array_detalle_producto_request             =   json_decode($request['array_detalle_producto'],true);
            foreach($array_detalle_producto_request as $item => $row) {

                $idoi                                   =   $this->funciones->getCreateIdMaestra('otrointegranteconeis');
                $oi                                     =   new OtroIntegranteConei;
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

                $oi->fecha_crea                         =   $this->fechaactual;
                $oi->usuario_crea                       =   Session::get('usuario')->id;
                $oi->save();

            }

            $tarchivos                                  =  DocumentosAsociado::where('activo','=','1')->where('id','=','APCN00000002')->get();

            foreach($tarchivos as $index=>$item){
                //01
                $files                                      =   $request[$item->cod_archivo];
                if(!is_null($files)){
                    foreach($files as $file){

                        $listadetalledoc            =   Archivo::where('referencia_id','=',$idrequerimiento)
                                                        ->get();

                        $rutafile                   =   storage_path('app/').$this->pathFiles.$codigo.'/';
                        $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);
                        $numero                     =   count($listadetalledoc)+1;
                        $nombre                     =   $codigo.'-'.$numero.'-'.$file->getClientOriginalName();

                        $rutadondeguardar           =   $this->pathFiles.$codigo.'/';
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
                        $dcontrol->tipo_archivo     =   'requerimiento_conei';
                        $dcontrol->fecha_crea       =   $this->fechaactual;
                        $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                        $dcontrol->save();
                    }
                }



            }


            //AGREGAR CERTIFICADO
            $procedencia_id                 =  'APCN00000002';
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
                $cabeceradet->estado_id            =   'CEES00000005';
                $cabeceradet->estado_nombre        =   'EN PROCESO';
                $cabeceradet->fecha_crea           =   $this->fechaactual;
                $cabeceradet->usuario_crea         =   Session::get('usuario')->id;
                $cabeceradet->save();  
                
            }





            //dd("exitoxo");


            return Redirect::to('/gestion-conei/'.$idopcion)->with('bienhecho', 'Requerimiento '.$codigo.' registrado con exito');

        }else{


            $institucion_id             =   Session::get('usuario')->institucion_id;
            $institucion                =   Institucion::where('id','=',$institucion_id)->first();
            $director                   =   Director::where('institucion_id','=',$institucion_id)->where('activo','=','1')->first();
            $combotd                    =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
            $selecttd                   =   'TIDO00000001';
            $array_detalle_producto     =   array();
            $disabled                   =   false;

            $procedencia_id             =   'APCN00000002';
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


            $tarchivos                  =   DocumentosAsociado::where('activo','=','1')->where('id','=','APCN00000002')->get();

            $arrayrepresentante         =   $this->array_representante_obligatrio(Session::get('institucion')->tipo_institucion);
            $lrepresentantes            =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE')->get();


            $director_i_tipodocumento_id = 'TIDO00000001';
            $director_i_tipodocumento_nombre = 'DNI';

            $director_i_representante_id = 'ESRP00000001';
            $director_i_representante_nombre = 'DIRECTOR';

            $director_i_documento        =  $director->dni;
            $director_i_nombres          =  $director->nombres;

            return View::make('requerimiento.agregarconei',
                        [
                            'array_detalle_producto'                =>  $array_detalle_producto,
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




}
