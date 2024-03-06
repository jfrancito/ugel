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

        $i_tipodocumento_director                   =   $request['i_tipodocumento_director'];
        $i_dni_director                             =   $request['i_dni_director'];
        $i_nombre_director                          =   $request['i_nombre_director'];

        $i_tipodocumento_subdirector                =   $request['i_tipodocumento_subdirector'];
        $i_dni_subdirector                          =   $request['i_dni_subdirector'];
        $i_nombre_subdirector                       =   $request['i_nombre_subdirector'];

        $i_tipodocumento_representantedocente       =   $request['i_tipodocumento_representantedocente'];
        $i_dni_representantedocente                 =   $request['i_dni_representantedocente'];
        $i_nombre_representantedocente              =   $request['i_nombre_representantedocente'];

        $i_tipodocumento_representanteadministrativo=   $request['i_tipodocumento_representanteadministrativo'];
        $i_dni_representanteadministrativo          =   $request['i_dni_representanteadministrativo'];
        $i_nombre_representanteadministrativo       =   $request['i_nombre_representanteadministrativo'];

        $i_tipodocumento_representanteapafa         =   $request['i_tipodocumento_representanteapafa'];
        $i_dni_representanteapafa                   =   $request['i_dni_representanteapafa'];
        $i_nombre_representanteapafa                =   $request['i_nombre_representanteapafa'];

        $i_tipodocumento_representanteestudiante    =   $request['i_tipodocumento_representanteestudiante'];
        $i_dni_representanteestudiante              =   $request['i_dni_representanteestudiante'];
        $i_nombre_representanteestudiante           =   $request['i_nombre_representanteestudiante'];

        $i_tipodocumento_representanteexalumno      =   $request['i_tipodocumento_representanteexalumno'];
        $i_dni_representanteexalumno                =   $request['i_dni_representanteexalumno'];
        $i_nombre_representanteexalumno             =   $request['i_nombre_representanteexalumno'];

        $i_tipodocumento_otrorepresentatecomunidad  =   $request['i_tipodocumento_otrorepresentatecomunidad'];
        $i_dni_otrorepresentatecomunidad            =   $request['i_dni_otrorepresentatecomunidad'];
        $i_nombre_otrorepresentatecomunidad         =   $request['i_nombre_otrorepresentatecomunidad'];

        $periodo                                    =   $request['periodo'];
        $array_detalle_producto_request      =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto              =   array();
        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }

        $funcion                                    =   $this;
        //dd($array_detalle_producto);

        return View::make('requerimiento//modal/ajax/alistacertificado',
                         [
                            'array_detalle_producto'                        =>  $array_detalle_producto,

                            'i_tipodocumento_director'                      =>  $i_tipodocumento_director,
                            'i_dni_director'                                =>  $i_dni_director,
                            'i_nombre_director'                             =>  $i_nombre_director,

                            'i_tipodocumento_subdirector'                   =>  $i_tipodocumento_subdirector,
                            'i_dni_subdirector'                             =>  $i_dni_subdirector,
                            'i_nombre_subdirector'                          =>  $i_nombre_subdirector,

                            'i_tipodocumento_representantedocente'          =>  $i_tipodocumento_representantedocente,
                            'i_dni_representantedocente'                    =>  $i_dni_representantedocente,
                            'i_nombre_representantedocente'                 =>  $i_nombre_representantedocente,

                            'i_tipodocumento_representanteadministrativo'   =>  $i_tipodocumento_representanteadministrativo,
                            'i_dni_representanteadministrativo'             =>  $i_dni_representanteadministrativo,
                            'i_nombre_representanteadministrativo'          =>  $i_nombre_representanteadministrativo,

                            'i_tipodocumento_representanteapafa'            =>  $i_tipodocumento_representanteapafa,
                            'i_dni_representanteapafa'                      =>  $i_dni_representanteapafa,
                            'i_nombre_representanteapafa'                   =>  $i_nombre_representanteapafa,

                            'i_tipodocumento_representanteestudiante'       =>  $i_tipodocumento_representanteestudiante,
                            'i_dni_representanteestudiante'                 =>  $i_dni_representanteestudiante,
                            'i_nombre_representanteestudiante'              =>  $i_nombre_representanteestudiante,

                            'i_tipodocumento_representanteexalumno'         =>  $i_tipodocumento_representanteexalumno,
                            'i_dni_representanteexalumno'                   =>  $i_dni_representanteexalumno,
                            'i_nombre_representanteexalumno'                =>  $i_nombre_representanteexalumno,
                            
                            'i_tipodocumento_otrorepresentatecomunidad'     =>  $i_tipodocumento_otrorepresentatecomunidad,
                            'i_dni_otrorepresentatecomunidad'               =>  $i_dni_otrorepresentatecomunidad,
                            'i_nombre_otrorepresentatecomunidad'            =>  $i_nombre_otrorepresentatecomunidad,

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
        $array_detalle_producto_request     =   json_decode($request['array_detalle_producto'],true);
        $array_detalle_producto             =   array();

        $fila                               =   count($array_detalle_producto_request) + 1;


        $arraynuevo                         =   array(
                                                    "fila"          => $fila,
                                                    "tdg"           => $tdg,
                                                    "tdgtexto"      => $tdgtexto,
                                                    "documentog"    => $documentog,
                                                    "nombresg"      => $nombresg,
                                                    "dcargoni"      => $dcargoni
                                                );

        array_push($array_detalle_producto,$arraynuevo);

        if(count($array_detalle_producto_request)>0){
            foreach ($array_detalle_producto_request as $key => $item) {
                array_push($array_detalle_producto,$item);
            }
        }

        $funcion                =   $this;

        return View::make('requerimiento/ajax/alistaoiconei',
                         [
                            'array_detalle_producto'    =>  $array_detalle_producto,
                            'funcion'                   =>  $funcion,
                            'ajax'                      =>  true
                         ]);
    }



    public function actionModalRegistro(Request $request)
    {

        $data_td                =   $request['data_td'];
        $data_dni               =   $request['data_dni'];
        $data_nombre            =   $request['data_nombre'];
        $data_titulo            =   $request['data_titulo'];
        $data_nombre_visible    =   $request['data_nombre_visible'];



        $funcion       =   $this;
        $combotd       =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd      =   'TIDO00000001';


        return View::make('requerimiento/modal/ajax/amregistro',
                         [
                            'data_td'           =>  $data_td,
                            'data_dni'          =>  $data_dni,
                            'data_nombre'       =>  $data_nombre,
                            'data_titulo'       =>  $data_titulo,
                            'data_nombre_visible'       =>  $data_nombre_visible,
                            
                            'combotd'           =>  $combotd,
                            'selecttd'          =>  $selecttd,
                            'funcion'           =>  $funcion,
                            'ajax'              =>  true
                         ]);
    }


    public function actionModalRegistroOI(Request $request)
    {

        $funcion       =   $this;
        $combotd       =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
        $selecttd      =   'TIDO00000001';

        return View::make('requerimiento/modal/ajax/amregistrooi',
                         [
                            'combotd'           =>  $combotd,
                            'selecttd'          =>  $selecttd,
                            'funcion'           =>  $funcion,
                            'ajax'              =>  true
                         ]);
    }




    public function actionAgregarConei($idopcion,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Requerimiento APAFA');
        if($_POST)
        {


            $institucion_id                             =   $request['institucion_id'];
            $director_id                                =   $request['director_id'];   

            $nombre_director                            =   $request['nombre_director'];
            $telefono_director                          =   $request['telefono_director'];
            $correo_director                            =   $request['correo_director'];

            $i_tipodocumento_director                   =   $request['i_tipodocumento_director'];
            $i_dni_director                             =   $request['i_dni_director'];
            $i_nombre_director                          =   $request['i_nombre_director'];

            $i_tipodocumento_subdirector                =   $request['i_tipodocumento_subdirector'];
            $i_dni_subdirector                          =   $request['i_dni_subdirector'];
            $i_nombre_subdirector                       =   $request['i_nombre_subdirector'];

            $i_tipodocumento_representantedocente       =   $request['i_tipodocumento_representantedocente'];
            $i_dni_representantedocente                 =   $request['i_dni_representantedocente'];
            $i_nombre_representantedocente              =   $request['i_nombre_representantedocente'];

            $i_tipodocumento_representanteadministrativo=   $request['i_tipodocumento_representanteadministrativo'];
            $i_dni_representanteadministrativo          =   $request['i_dni_representanteadministrativo'];
            $i_nombre_representanteadministrativo       =   $request['i_nombre_representanteadministrativo'];

            $i_tipodocumento_representanteapafa         =   $request['i_tipodocumento_representanteapafa'];
            $i_dni_representanteapafa                   =   $request['i_dni_representanteapafa'];
            $i_nombre_representanteapafa                =   $request['i_nombre_representanteapafa'];

            $i_tipodocumento_representanteestudiante    =   $request['i_tipodocumento_representanteestudiante'];
            $i_dni_representanteestudiante              =   $request['i_dni_representanteestudiante'];
            $i_nombre_representanteestudiante           =   $request['i_nombre_representanteestudiante'];
            $i_tipodocumento_representanteexalumno      =   $request['i_tipodocumento_representanteexalumno'];
            $i_dni_representanteexalumno                =   $request['i_dni_representanteexalumno'];
            $i_nombre_representanteexalumno             =   $request['i_nombre_representanteexalumno'];
            $i_tipodocumento_otrorepresentatecomunidad  =   $request['i_tipodocumento_otrorepresentatecomunidad'];
            $i_dni_otrorepresentatecomunidad            =   $request['i_dni_otrorepresentatecomunidad'];
            $i_nombre_otrorepresentatecomunidad         =   $request['i_nombre_otrorepresentatecomunidad'];


            $idrequerimiento                            =   $this->funciones->getCreateIdMaestra('coneis');
            $codigo                                     =   $this->funciones->generar_codigo('coneis',8);

            $cabecera                                   =   new Conei;
            $cabecera->id                               =   $idrequerimiento;
            $cabecera->codigo                           =   $codigo;
            $cabecera->institucion_id                   =   $institucion_id;

            $cabecera->director_id                      =   $director_id;            
            $cabecera->nombres_director_institucion     =   $nombre_director;
            $cabecera->telefono_director_institucion    =   $telefono_director;
            $cabecera->correo_director_institucion      =   $correo_director;


            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_director);
            $cabecera->tipo_documento_director_id       =   $i_tipodocumento_director;
            $cabecera->tipo_documento_director_nombre   =   $tipo_documento_nombre;
            $cabecera->documento_director               =   $i_dni_director;
            $cabecera->nombres_director                 =   $i_nombre_director;

            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_subdirector);
            $cabecera->tipo_documento_subdirector_id    =   $i_tipodocumento_subdirector;
            $cabecera->tipo_documento_subdirector_nombre=   $tipo_documento_nombre;
            $cabecera->documento_subdirector            =   $i_dni_subdirector;
            $cabecera->nombres_subdirector              =   $i_nombre_subdirector;

            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_representantedocente);
            $cabecera->tipo_documento_redoc_id          =   $i_tipodocumento_representantedocente;
            $cabecera->tipo_documento_redoc_nombre      =   $tipo_documento_nombre;
            $cabecera->documento_redoc                  =   $i_dni_representantedocente;
            $cabecera->nombres_redoc                    =   $i_nombre_representantedocente;


            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_representanteadministrativo);
            $cabecera->tipo_documento_readm_id          =   $i_tipodocumento_representanteadministrativo;
            $cabecera->tipo_documento_readm_nombre      =   $tipo_documento_nombre;
            $cabecera->documento_readm                  =   $i_dni_representanteadministrativo;
            $cabecera->nombres_readm                    =   $i_nombre_representanteadministrativo;


            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_representanteapafa);
            $cabecera->tipo_documento_reapf_id          =   $i_tipodocumento_representanteapafa;
            $cabecera->tipo_documento_reapf_nombre      =   $tipo_documento_nombre;
            $cabecera->documento_reapf                  =   $i_dni_representanteapafa;
            $cabecera->nombres_reapf                    =   $i_nombre_representanteapafa;

            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_representanteestudiante);
            $cabecera->tipo_documento_reest_id          =   $i_tipodocumento_representanteestudiante;
            $cabecera->tipo_documento_reest_nombre      =   $tipo_documento_nombre;
            $cabecera->documento_reest                  =   $i_dni_representanteestudiante;
            $cabecera->nombres_reest                    =   $i_nombre_representanteestudiante;

            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_representanteexalumno);
            $cabecera->tipo_documento_rexal_id          =   $i_tipodocumento_representanteexalumno;
            $cabecera->tipo_documento_rexal_nombre      =   $tipo_documento_nombre;
            $cabecera->documento_rexal                  =   $i_dni_representanteexalumno;
            $cabecera->nombres_rexal                    =   $i_nombre_representanteexalumno;

            $tipo_documento_nombre                      =   $this->funciones->estado_nombre($i_tipodocumento_otrorepresentatecomunidad);
            $cabecera->tipo_documento_reorc_id          =   $i_tipodocumento_otrorepresentatecomunidad;
            $cabecera->tipo_documento_reorc_nombre      =   $tipo_documento_nombre;
            $cabecera->documento_reorc                  =   $i_dni_otrorepresentatecomunidad;
            $cabecera->nombres_reorc                    =   $i_nombre_otrorepresentatecomunidad;
            $cabecera->estado_id                        =   'ESRE00000001';
            $cabecera->estado_nombre                    =   'GENERADO';
            $cabecera->fecha_crea                       =   $this->fechaactual;
            $cabecera->usuario_crea                     =   Session::get('usuario')->id;
            $cabecera->save();

            $usuario                                    =   User::where('id',Session::get('usuario')->id)->first();
            
            //01
            $files                                      =   $request['upload'];
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
                    $dcontrol->usuario_nombre   =   $usuario->nombre;
                    $dcontrol->tipo_archivo     =   'requerimiento_conei';
                    $dcontrol->fecha_crea       =   $this->fechaactual;
                    $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                    $dcontrol->save();
                }
            }

            //02
            $filesapafa                                 =   $request['uploadapafa'];
            if(!is_null($filesapafa)){
                foreach($filesapafa as $file){

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
                    $dcontrol->usuario_nombre   =   $usuario->nombre;
                    $dcontrol->tipo_archivo     =   'requerimiento_conei';
                    $dcontrol->fecha_crea       =   $this->fechaactual;
                    $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                    $dcontrol->save();
                }
            }


            //03
            $files03                                 =   $request['upload03'];
            if(!is_null($files03)){
                foreach($files03 as $file){

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
                    $dcontrol->usuario_nombre   =   $usuario->nombre;
                    $dcontrol->tipo_archivo     =   'requerimiento_conei';
                    $dcontrol->fecha_crea       =   $this->fechaactual;
                    $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                    $dcontrol->save();
                }
            }


            //04
            $files04                                 =   $request['upload04'];
            if(!is_null($files04)){
                foreach($files04 as $file){

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
                    $dcontrol->usuario_nombre   =   $usuario->nombre;
                    $dcontrol->tipo_archivo     =   'requerimiento_conei';
                    $dcontrol->fecha_crea       =   $this->fechaactual;
                    $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                    $dcontrol->save();
                }
            }



            return Redirect::to('/gestion-conei/'.$idopcion)->with('bienhecho', 'Requerimiento '.$codigo.' registrado con exito');

        }else{


            $institucion_id =   Session::get('usuario')->institucion_id;
            $institucion    =   Institucion::where('id','=',$institucion_id)->first();
            $director       =   Director::where('institucion_id','=',$institucion_id)->where('activo','=','1')->first();
            $combotd        =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
            $selecttd       =   'TIDO00000001';
            $array_detalle_producto = array();
            $comboperiodo   =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione Periodo','','APAFA_CONEI_PERIODO');


            return View::make('requerimiento.agregarconei',
                        [

                            'array_detalle_producto'                => $array_detalle_producto,
                            'idopcion'          =>  $idopcion,
                            'institucion'       =>  $institucion,
                            'director'          =>  $director,
                            'combotd'           =>  $combotd,
                            'comboperiodo'      =>  $comboperiodo,
                            'selecttd'          =>  $selecttd,
                        ]);
        }
    }




}
