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
use App\Modelos\Certificado;

use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\CertificadoTraits;
use Hashids;
use SplFileInfo;

class GestionCertificadoController extends Controller
{
    use GeneralesTraits;
    use CertificadoTraits;


    public function actionAjaxComboPeriodoxInstitucion(Request $request)
    {

        $institucion_id             =   $request['institucion_id'];
        $procedencia_id             =   $request['procedencia_id'];

        $array_periodos             =   Certificado::where('institucion_id','=',$institucion_id)
                                        ->where('procedente_id','=',$procedencia_id)
                                        ->where('activo','=',1)
                                        ->where('estado_id','=','CEES00000001')
                                        ->pluck('periodo_id')                                   
                                        ->toArray();

        $comboperiodo               =   $this->gn_generacion_combo_tabla_not_array('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO',$array_periodos);
        $selectperiodo              =   '';

        return View::make('requerimiento/combo/periodo',
                         [          
                            'comboperiodo'          => $comboperiodo,
                            'selectperiodo'        => $selectperiodo,
                            'ajax'                  => true,                            
                         ]);
    }



    public function actionListarCertificados($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista Certificados');

        $user_id        =   Session::get('usuario')->id;
        $listadatos     =   $this->con_lista_certificados();
        $funcion        =   $this;

        return View::make('requerimiento/listacertificado',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }



    public function actionDescargarArchivosCertificado($idregistro,$idarchivo)
    {

        $registro_id = $this->funciones->decodificarmaestra($idarchivo);
        $user_id    = Session::get('usuario')->id;

        View::share('titulo','Eliminar Archivos del Requerimiento');

        try{
            // DB::beginTransaction();
            $archivo                =   Archivo::where('id','=',$registro_id)->first();
            $storagePath            = storage_path('app\\'.$this->pathFilesCer.$archivo->lote.'\\'.$archivo->nombre_archivo);
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



    public function actionModificarCertificado($idopcion,$idcertificado,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/

        $id_certificado = $this->funciones->decodificarmaestra($idcertificado);
        $certificado    =   Certificado::where('id','=',$id_certificado)->first();


        if($_POST)
        {



            try {
                    DB::beginTransaction();
                    /******************************/

                    //ya existe un certificado en ese periodo
                    $activo                             =   $request['activo'];
                    if($activo==0){
                        $certificado->estado_id         =   'CEES00000003';
                        $certificado->estado_nombre     =   'BAJA';
                    }else{
                        $certificados_activos           =   Certificado::where('institucion_id','=',$certificado->institucion_id)
                                                            ->where('procedente_id','=',$certificado->procedente_id)
                                                            ->where('periodo_id','=',$certificado->periodo_id)
                                                            ->where('activo','=',1)
                                                            ->get();
                        if(count($certificados_activos)>0){
                            return Redirect::back()->withInput()->with('errorbd', 'No puedes activar este certificado porque ya existe uno en este periodo');
                        }else{
                            $certificado->estado_id         =   'CEES00000001';
                            $certificado->estado_nombre     =   'APROBADO';
                        }
                    }

                    $usuario                       =   User::where('id',Session::get('usuario')->id)->first();
                    $certificado->activo           =   $request['activo'];
                    $certificado->fecha_mod        =   $this->fechaactual;
                    $certificado->usuario_mod      =   Session::get('usuario')->id;
                    $certificado->save();

                    $files                      =   $request['certificado'];
                    if(!is_null($files)){
                        foreach($files as $file){

                            $rutafile                   =   storage_path('app/').$this->pathFilesCer.$codigo.'/';
                            $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);
                            $numero                     =   $periodo_id;
                            $nombre                     =   $codigo.'-'.$file->getClientOriginalName();

                            $rutadondeguardar           =   $this->pathFilesCer.$codigo.'/';
                            $urlmedio                   =   'app/'.$rutadondeguardar.$nombre;

                            $nombreoriginal             =   $file->getClientOriginalName();
                            $info                       =   new SplFileInfo($nombreoriginal);
                            $extension                  =   $info->getExtension();
                            copy($file->getRealPath(),$rutafile.$nombre);
                            $idarchivo                  =   $this->funciones->getCreateIdMaestra('archivos');

                            $dcontrol                   =   Archivo::where('referencia_id','=',$certificado->id)->where('tipo_archivo','=','certificado')->where('activo','=',1)->first();
                            $dcontrol->size             =   filesize($file);
                            $dcontrol->extension        =   $extension;
                            $dcontrol->nombre_archivo   =   $nombre;
                            $dcontrol->url_archivo      =   $urlmedio;
                            $dcontrol->area_id          =   '';
                            $dcontrol->area_nombre      =   '';
                            $dcontrol->periodo_id       =   '';
                            $dcontrol->periodo_nombre   =   '';
                            $dcontrol->codigo_doc       =   '';
                            $dcontrol->nombre_doc       =   '';
                            $dcontrol->usuario_nombre   =   $usuario->nombre;
                            $dcontrol->fecha_mod        =   $this->fechaactual;
                            $dcontrol->usuario_mod      =   Session::get('usuario')->id;
                            $dcontrol->save();

                        }
                    }


                    DB::commit();
                
            } catch (Exception $ex) {
                DB::rollback();
                  $msj =$this->ge_getMensajeError($ex);
                return Redirect::to('/gestion-de-registro-certificado/'.$idopcion)->with('errorurl', $msj);
            }
            /******************************/

            return Redirect::to('/gestion-de-registro-certificado/'.$idopcion)->with('bienhecho', 'Registro modificado con exito');



        }else{
                View::share('titulo','Modificar Certificado');

                $datos              =   DB::table('instituciones')
                                        ->where('activo','=',1)
                                        ->where('id','<>','1CIX00000001')
                                        ->select(DB::raw("codigo+' - '+nombre+' - '+nivel as nombres,id"))
                                        ->pluck('nombres','id')
                                        ->toArray();

                $comboinstituciones =   array('' => "Seleccione Institucion") + $datos;
                $selectinstituciones=   $certificado->institucion_id;
                $comboperiodo       =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO');

                $array_periodos     =   Certificado::where('institucion_id','=',$certificado->institucion_id)
                                                ->where('activo','=',1)
                                                ->pluck('periodo_id')                                   
                                                ->toArray();


                $periodo_sel        =   Estado::where('id','=',$certificado->periodo_id)->first();

                $comboperiodo       =   array($periodo_sel->id => $periodo_sel->nombre) + $this->gn_generacion_combo_tabla_not_array('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO',$array_periodos);


                $selectperiodo      =   $certificado->periodo_id;
                $comboprocedencia   =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','','APAFA_CONEI');
                $selectprocedencia  =   $certificado->procedente_id;

                $multimedia         =   Archivo::where('referencia_id','=',$certificado->id)->where('tipo_archivo','=','certificado')->where('activo','=',1)->first();
                $rutafoto           =   !empty($multimedia) ? asset('public/img/00000001-UGEL01.pdf') : asset('public/img/no-foto.png');

                //dd($rutafoto);

                return View::make('requerimiento/modificarcertificado', 
                                [
                                    'certificado'           =>  $certificado,
                                    'idopcion'              =>  $idopcion,
                                    'comboinstituciones'    =>  $comboinstituciones, 
                                    'selectinstituciones'   =>  $selectinstituciones,
                                    'comboperiodo'          =>  $comboperiodo, 
                                    'selectperiodo'         =>  $selectperiodo,
                                    'comboprocedencia'      =>  $comboprocedencia, 
                                    'selectprocedencia'     =>  $selectprocedencia,
                                    'rutafoto'              =>  $rutafoto,
                                    'multimedia'            =>  $multimedia,
                                ]);
        }

    }



    public function actionAgregarCertificado($idopcion,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Certificado');

        if($_POST)
        {

            try {
                    DB::beginTransaction();
                    /******************************/

                    $usuario                                    =   User::where('id',Session::get('usuario')->id)->first();

                    $institucion_id             =   $request['institucion_id'];
                    $periodo_id                 =   $request['periodo_id'];
                    $procedencia_id             =   $request['procedencia_id'];

                    $institucion                =   Institucion::where('id','=',$institucion_id)->first();
                    $periodo                    =   Estado::where('id','=',$periodo_id)->first();
                    $procedencia                =   Estado::where('id','=',$procedencia_id)->first();

                    $idcertificado              =   $this->funciones->getCreateIdMaestra('certificados');
                    $codigo                     =   $this->funciones->generar_codigo('certificados',8);

                    $cabecera                   =   new Certificado();
                    $cabecera->id               =   $idcertificado;
                    $cabecera->codigo           =   $codigo;
                    $cabecera->institucion_id   =   $institucion_id;
                    $cabecera->institucion_codigo   =   $institucion->codigo;
                    $cabecera->institucion_nombre   =   $institucion->nombre;
                    $cabecera->institucion_nivel   =   $institucion->nivel;

                    $cabecera->periodo_id       =   $periodo_id;
                    $cabecera->periodo_nombre   =   $periodo->nombre;

                    $cabecera->procedente_id    =   $procedencia_id;
                    $cabecera->procedente_nombre=   $procedencia->nombre;

                    $cabecera->estado_id        =   'CEES00000001';
                    $cabecera->estado_nombre    =   'APROBADO';

                    $cabecera->fecha_crea       =   $this->fechaactual;
                    $cabecera->usuario_crea     =   Session::get('usuario')->id;
                    $cabecera->save();




                    $files                      =   $request['certificado'];
                    if(!is_null($files)){
                        foreach($files as $file){

                            $rutafile                   =   storage_path('app/').$this->pathFilesCer.$codigo.'/';
                            $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);
                            $numero                     =   $periodo_id;
                            $nombre                     =   $codigo.'-'.$file->getClientOriginalName();

                            $rutadondeguardar           =   $this->pathFilesCer.$codigo.'/';
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
                            $dcontrol->referencia_id    =   $idcertificado;
                            $dcontrol->nombre_archivo   =   $nombre;
                            $dcontrol->url_archivo      =   $urlmedio;
                            $dcontrol->area_id          =   '';
                            $dcontrol->area_nombre      =   '';

                            $dcontrol->periodo_id       =   '';
                            $dcontrol->periodo_nombre   =   '';
                            $dcontrol->codigo_doc       =   '';
                            $dcontrol->nombre_doc       =   '';


                            $dcontrol->usuario_nombre   =   $usuario->nombre;
                            $dcontrol->tipo_archivo     =   'certificado';
                            $dcontrol->fecha_crea       =   $this->fechaactual;
                            $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                            $dcontrol->save();

                            $cabecera->archivo_id       =   $idarchivo;
                            $cabecera->save();


                        }
                    }


                    DB::commit();
                
            } catch (Exception $ex) {
                DB::rollback();
                  $msj =$this->ge_getMensajeError($ex);
                return Redirect::to('/gestion-de-registro-certificado/'.$idopcion)->with('errorurl', $msj);
            }
            /******************************/

            return Redirect::to('/gestion-de-registro-certificado/'.$idopcion)->with('bienhecho', 'Registro registrado con exito');

        }else{

            $datos              =   DB::table('instituciones')
                                    ->where('activo','=',1)
                                    ->where('id','<>','1CIX00000001')
                                    ->select(DB::raw("codigo+' - '+nombre+' - '+nivel as nombres,id"))
                                    ->pluck('nombres','id')
                                    ->toArray();


            //dd($datos);

            $comboinstituciones =   array('' => "Seleccione Categoria") + $datos;
            $selectinstituciones=   '';
            $comboperiodo       =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO');
            $selectperiodo      =   '';
            $comboprocedencia   =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione procedencia','','APAFA_CONEI');
            $selectprocedencia  =   '';




            return View::make('requerimiento/agregarcertificado',
                        [
                            'idopcion'              =>  $idopcion,
                            'comboinstituciones'    =>  $comboinstituciones, 
                            'selectinstituciones'   =>  $selectinstituciones,
                            'comboperiodo'          =>  $comboperiodo, 
                            'selectperiodo'         =>  $selectperiodo,
                            'comboprocedencia'      =>  $comboprocedencia, 
                            'selectprocedencia'     =>  $selectprocedencia
                        ]);
        }

    }



}
