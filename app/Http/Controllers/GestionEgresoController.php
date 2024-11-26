<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Egreso;
use App\Modelos\Estado;
use App\Modelos\Archivo;
use App\Modelos\Trimestre;


use App\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use App\Traits\GeneralesTraits;
use App\Traits\EgresoTraits;
use Hashids;
use SplFileInfo;

class GestionEgresoController extends Controller
{
    use GeneralesTraits;    
    use EgresoTraits;


    public function actionListarEgresos($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista Egresos');

        $user_id        =   Session::get('usuario')->id;
        $listadatos     =   $this->con_lista_egresos();
        $funcion        =   $this;

        //dd($listadatos);

        return View::make('movimiento/listaegreso',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }

    public function actionAgregarEgreso($idopcion,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Egreso');

        if($_POST)
        {

            try {
                    DB::beginTransaction();
                    /******************************/

                    $usuario                            =   User::where('id',Session::get('usuario')->id)->first();

                    $fecha_comprobante                  =   $request['fecha_comprobante'];
                    $tipo_comprobante_id                =   $request['tipo_comprobante_id'];
                    $serie                              =   $request['serie'];
                    $numero                             =   $request['numero'];
                    $tipo_documento_id                  =   $request['tipo_documento_id'];
                    $dni                                =   $request['dni'];
                    $razon_social                       =   $request['razon_social'];
                    $tipo_gasto_id                      =   $request['tipo_gasto_id'];

                    if($tipo_gasto_id == 'TGEG00000001'){
                        $tipo_compra_id                      =   $request['tipo_compra_id'];
                    }

                    $tipo_concepto_id                   =   $request['tipo_concepto_id'];
                    $detalle_concepto                   =   $request['detalle_concepto'];                    
                    $total                              =   floatval(str_replace(",","",$request['total']));
                    $observacion                        =   $request['observacion'];
                    
                    $tipo_comprobante                   =   Estado::where('id','=',$tipo_comprobante_id)->first();
                    $tipo_documento                     =   Estado::where('id','=',$tipo_documento_id)->first();
                    
                    $tipo_gasto                         =   Estado::where('id','=',$tipo_gasto_id)->first();

                    if($tipo_gasto_id == 'TGEG00000001'){
                        $tipo_compra                      =   Estado::where('id','=',$tipo_compra_id)->first();
                    }

                    $tipo_concepto                      =   Estado::where('id','=',$tipo_concepto_id)->first();

                    $trimestre                          =   Trimestre::where('fecha_ini', '<=', $fecha_comprobante)
                                                            ->where('fecha_fin', '>=', $fecha_comprobante)
                                                            ->where('activo','=',1)
                                                            ->first(); 

                    $idegreso                          =   $this->funciones->getCreateIdMaestra('egresos');
                    $codigo                             =   $this->funciones->generar_codigo('egresos',8);

                    $cabecera                           =   new Egreso();
                    $cabecera->id                       =   $idegreso;
                    $cabecera->codigo                   =   $codigo;
                    $cabecera->institucion_id           =   Session::get('institucion')->id;
                    $cabecera->institucion_nombre       =   Session::get('institucion')->nombre;
                    $cabecera->trimestre_id             =   $trimestre->id;
                    $cabecera->trimestre_nombre         =   $trimestre->nombre;
                    $cabecera->fecha_comprobante        =   $fecha_comprobante;
                    $cabecera->tipo_comprobante_id      =   $tipo_comprobante_id;
                    $cabecera->tipo_comprobante_nombre  =   $tipo_comprobante->nombre;
                    $cabecera->serie                    =   $serie;         
                    $cabecera->numero                   =   $numero;
                    $cabecera->tipo_documento_id        =   $tipo_documento_id;
                    $cabecera->tipo_documento_nombre    =   $tipo_documento->nombre;
                    $cabecera->dni                      =   $dni;         
                    $cabecera->razon_social             =   $razon_social;         
                    $cabecera->tipo_gasto_id            =   $tipo_gasto_id;
                    $cabecera->tipo_gasto_nombre        =   $tipo_gasto->nombre;

                    if($tipo_gasto_id == 'TGEG00000001'){
                        $cabecera->tipo_compra_id         =   $tipo_compra_id;
                        $cabecera->tipo_compra_nombre     =   $tipo_compra->nombre;
                    }

                    $cabecera->tipo_concepto_id         =   $tipo_concepto_id;
                    $cabecera->tipo_concepto_nombre     =   $tipo_concepto->nombre;
                    $cabecera->detalle_concepto         =   $detalle_concepto;                    
                    $cabecera->total                    =   $total;
                    $cabecera->observacion              =   $observacion;                    
                    $cabecera->estado_id                =   'ESEG00000001';
                    $cabecera->estado_nombre            =   'GENERADO';                   
                    $cabecera->fecha_crea               =   $this->fechaactual;
                    $cabecera->usuario_crea             =   Session::get('usuario')->id;
                    $cabecera->save();           

                    $codigo_institucion                 =   Session::get('institucion')->codigo;         

                    $files                              =   $request['egreso'];
                    if(!is_null($files)){
                        foreach($files as $file){

                            $rutafile                   =   storage_path('app/').$codigo_institucion.'/'.$this->pathFilesEgr;
                            $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);                            
                            $nombre                     =   $codigo.'-'.$file->getClientOriginalName();

                            $rutadondeguardar           =   $codigo_institucion.'/'.$this->pathFilesEgr;
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
                            $dcontrol->referencia_id    =   $idegreso;
                            $dcontrol->nombre_archivo   =   $nombre;
                            $dcontrol->url_archivo      =   $urlmedio;
                            $dcontrol->area_id          =   '';
                            $dcontrol->area_nombre      =   '';
                            $dcontrol->periodo_id       =   '';
                            $dcontrol->periodo_nombre   =   '';
                            $dcontrol->codigo_doc       =   '';
                            $dcontrol->nombre_doc       =   '';
                            $dcontrol->usuario_nombre   =   $usuario->nombre;
                            $dcontrol->tipo_archivo     =   'egreso';
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
                return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('errorurl', $msj);
            }
            /******************************/

            return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('bienhecho', 'Egreso registrado con exito');

        }else{                        
            $arraytipocomprobante               =   array('TICO00000001','TICO00000003','TICO00000004','TICO00000005');            
            $combo_tipo_comprobante             =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo comprobante','','TIPO_COMPROBANTE',$arraytipocomprobante);
            $select_tipo_comprobante            =   '';

            $arraytipodocumento                 =   array('TIDO00000001','TIDO00000004');
            $combo_tipo_documento               =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo documento','','TIPO_DOCUMENTO',$arraytipodocumento);
            $select_tipo_documento              =   '';

            $combo_tipo_gasto                   =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo gasto','','TIPO_GASTO_EGRESO');
            $select_tipo_gasto                  =   '';

            $combo_tipo_compra                  =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo compra','','TIPO_COMPRA_EGRESO');
            $select_tipo_compra                 =   '';

            $combo_tipo_concepto                =   [];
            $select_tipo_concepto               =   '';
           
            return View::make('movimiento/agregaregreso',
                        [
                            'idopcion'                          =>  $idopcion,                            

                            'combo_tipo_comprobante'            =>  $combo_tipo_comprobante, 
                            'select_tipo_comprobante'           =>  $select_tipo_comprobante,

                            'combo_tipo_documento'              =>  $combo_tipo_documento, 
                            'select_tipo_documento'             =>  $select_tipo_documento,

                            'combo_tipo_gasto'                  =>  $combo_tipo_gasto, 
                            'select_tipo_gasto'                 =>  $select_tipo_gasto,

                            'combo_tipo_compra'                 =>  $combo_tipo_compra, 
                            'select_tipo_compra'                =>  $select_tipo_compra,

                            'combo_tipo_concepto'               =>  $combo_tipo_concepto, 
                            'select_tipo_concepto'              =>  $select_tipo_concepto,
                        ]);
        }

    }

    public function actionAjaxTipoConcepto(Request $request)
    {
        $tipo_gasto_id           =   $request['tipo_gasto_id'];

        if($tipo_gasto_id == 'TGEG00000001'){
            $combo_tipo_concepto     =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_EGRESO_COMPRAS');
        }elseif($tipo_gasto_id == 'TGEG00000002'){
            $combo_tipo_concepto     =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_EGRESO_MOVILIDAD');
        }elseif($tipo_gasto_id == 'TGEG00000003'){
            $combo_tipo_concepto     =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_EGRESO_SERVICIO');
        }else{
            $combo_tipo_concepto     = [];
        }
                 
        $select_tipo_concepto    =   '';
        
        
        return View::make('movimiento/ajax/atipoconcepto',
                        [
                            'select_tipo_concepto'       => $select_tipo_concepto,
                            'combo_tipo_concepto'        => $combo_tipo_concepto,                              
                            'ajax'                      =>  true,
                        ]);
    }

    public function actionModificarEgreso($idopcion,$idegreso,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $id_egreso = $this->funciones->decodificarmaestra($idegreso);
        $egreso    =   Egreso::where('id','=',$id_egreso)->first();

        if($_POST)
        {
            try {
                    DB::beginTransaction();
                    /******************************/

                    $usuario                            =   User::where('id',Session::get('usuario')->id)->first();

                    $fecha_comprobante                  =   $request['fecha_comprobante'];
                    $tipo_comprobante_id                =   $request['tipo_comprobante_id'];
                    $serie                              =   $request['serie'];
                    $numero                             =   $request['numero'];
                    $tipo_documento_id                  =   $request['tipo_documento_id'];
                    $dni                                =   $request['dni'];
                    $razon_social                       =   $request['razon_social'];
                    $tipo_gasto_id                      =   $request['tipo_gasto_id'];

                    if($tipo_gasto_id == 'TGEG00000001'){
                        $tipo_compra_id                      =   $request['tipo_compra_id'];
                    }

                    $tipo_concepto_id                   =   $request['tipo_concepto_id'];
                    $detalle_concepto                   =   $request['detalle_concepto'];
                    
                    $total                              =   floatval(str_replace(",","",$request['total']));
                    $observacion                        =   $request['observacion'];
                    
                    $tipo_comprobante                   =   Estado::where('id','=',$tipo_comprobante_id)->first();
                    $tipo_documento                     =   Estado::where('id','=',$tipo_documento_id)->first();

                    $tipo_gasto                         =   Estado::where('id','=',$tipo_gasto_id)->first();

                    if($tipo_gasto_id == 'TGEG00000001'){
                        $tipo_compra                      =   Estado::where('id','=',$tipo_compra_id)->first();
                    }

                    $tipo_concepto                      =   Estado::where('id','=',$tipo_concepto_id)->first();

                    $trimestre                          =   Trimestre::where('fecha_ini', '<=', $fecha_comprobante)
                                                            ->where('fecha_fin', '>=', $fecha_comprobante)
                                                            ->where('activo','=',1)
                                                            ->first();

                    $egreso->trimestre_id              =   $trimestre->id;
                    $egreso->trimestre_nombre          =   $trimestre->nombre;
                    $egreso->fecha_comprobante         =   $fecha_comprobante;
                    $egreso->tipo_comprobante_id       =   $tipo_comprobante_id;
                    $egreso->tipo_comprobante_nombre   =   $tipo_comprobante->nombre;
                    $egreso->serie                     =   $serie;         
                    $egreso->numero                    =   $numero;
                    $egreso->tipo_documento_id         =   $tipo_documento_id;
                    $egreso->tipo_documento_nombre     =   $tipo_documento->nombre;
                    $egreso->dni                       =   $dni;      
                    $egreso->razon_social              =   $razon_social;      
                    $egreso->tipo_gasto_id             =   $tipo_gasto_id;
                    $egreso->tipo_gasto_nombre         =   $tipo_gasto->nombre;

                    if($tipo_gasto_id == 'TGEG00000001'){
                        $egreso->tipo_compra_id         =   $tipo_compra_id;
                        $egreso->tipo_compra_nombre     =   $tipo_compra->nombre;
                    }else{
                        $egreso->tipo_compra_id         =   NULL;
                        $egreso->tipo_compra_nombre     =   NULL;
                    }   
                    $egreso->tipo_concepto_id          =   $tipo_concepto_id;
                    $egreso->tipo_concepto_nombre      =   $tipo_concepto->nombre;
                    $egreso->detalle_concepto          =   $detalle_concepto;                    
                    $egreso->total                     =   $total;
                    $egreso->observacion               =   $observacion;                                        
                    $egreso->fecha_mod                 =   $this->fechaactual;
                    $egreso->usuario_mod               =   Session::get('usuario')->id;
                    $egreso->save();        

                    $files                      =   $request['egreso'];
                    if(!is_null($files)){
                        foreach($files as $file){

                            $codigo                     =   $egreso->codigo;
                            $codigo_institucion         =   Session::get('institucion')->codigo;

                            $rutafile                   =   storage_path('app/').$codigo_institucion.'/'.$this->pathFilesEgr;
                            $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);                            
                            $nombre                     =   $codigo.'-'.$file->getClientOriginalName();

                            $rutadondeguardar           =   $codigo_institucion.'/'.$this->pathFilesEgr;
                            $urlmedio                   =   'app/'.$rutadondeguardar.$nombre;

                            $nombreoriginal             =   $file->getClientOriginalName();
                            $info                       =   new SplFileInfo($nombreoriginal);
                            $extension                  =   $info->getExtension();
                            copy($file->getRealPath(),$rutafile.$nombre);                            

                            $dcontrol                   =   Archivo::where('referencia_id','=',$egreso->id)->where('tipo_archivo','=','egreso')->where('activo','=',1)->first();
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
                return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('errorurl', $msj);                
            }
            /******************************/

            return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('bienhecho', 'Egreso modificado con exito');



        }else{
                View::share('titulo','Modificar Egreso');

                $trimestre_nombre                   =   $egreso->trimestre_nombre;

                $arraytipocomprobante               =   array('TICO00000001','TICO00000003','TICO00000004','TICO00000005');            
                $combo_tipo_comprobante             =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo comprobante','','TIPO_COMPROBANTE',$arraytipocomprobante);
                $select_tipo_comprobante            =   $egreso->tipo_comprobante_id;

                $arraytipodocumento                 =   array('TIDO00000001','TIDO00000004');
                $combo_tipo_documento               =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo documento','','TIPO_DOCUMENTO',$arraytipodocumento);
                $select_tipo_documento              =   $egreso->tipo_documento_id;

                $combo_tipo_gasto                   =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo gasto','','TIPO_GASTO_EGRESO');
                $select_tipo_gasto                  =   $egreso->tipo_gasto_id;

                $combo_tipo_compra                  =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo compra','','TIPO_COMPRA_EGRESO');
                if($select_tipo_gasto == 'TGEG00000001'){                    
                    $select_tipo_compra                 =   $egreso->tipo_compra_id;
                }else{
                    $select_tipo_compra                 =   '';
                }

                if($select_tipo_gasto == 'TGEG00000001'){
                    $combo_tipo_concepto     =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_EGRESO_COMPRAS');
                }elseif($select_tipo_gasto == 'TGEG00000002'){
                    $combo_tipo_concepto     =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_EGRESO_MOVILIDAD');
                }elseif($select_tipo_gasto == 'TGEG00000003'){
                    $combo_tipo_concepto     =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_EGRESO_SERVICIO');                
                }                
                $select_tipo_concepto               =   $egreso->tipo_concepto_id;;
                

                $multimedia         =   Archivo::where('referencia_id','=',$egreso->id)->where('tipo_archivo','=','egreso')->where('activo','=',1)->first();
                

                $rutafoto           =   !empty($multimedia) ? asset('storage/'.$multimedia->url_archivo) : asset('public/img/no-foto.png');
                
                return View::make('movimiento/modificaregreso', 
                                [
                                    'egreso'                    =>  $egreso,
                                    'idopcion'                  =>  $idopcion,
                                    'trimestre_nombre'          =>  $trimestre_nombre,
                                    'combo_tipo_comprobante'    =>  $combo_tipo_comprobante, 
                                    'select_tipo_comprobante'   =>  $select_tipo_comprobante,
                                    'combo_tipo_documento'      =>  $combo_tipo_documento, 
                                    'select_tipo_documento'     =>  $select_tipo_documento,
                                    'combo_tipo_gasto'          =>  $combo_tipo_gasto, 
                                    'select_tipo_gasto'         =>  $select_tipo_gasto,
                                    'combo_tipo_compra'         =>  $combo_tipo_compra, 
                                    'select_tipo_compra'        =>  $select_tipo_compra,
                                    'combo_tipo_concepto'       =>  $combo_tipo_concepto, 
                                    'select_tipo_concepto'      =>  $select_tipo_concepto,
                                    'rutafoto'                  =>  $rutafoto,
                                    'multimedia'                =>  $multimedia,
                                ]);
        }

    }

    public function actionDescargarArchivosEgreso($idopcion,$idarchivo)
    {

        $archivo_id = $this->funciones->decodificarmaestra($idarchivo);        

        View::share('titulo','Descargar Archivos del Egreso');

        try{            
            $archivo                =   Archivo::where('id','=',$archivo_id)->first();
            $storagePath            =   storage_path($archivo->url_archivo);

            if(is_file($storagePath)){
                return response()->download($storagePath);
            }else{
                return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('errorurl', 'archivo no encontrado');
            }            
        }catch(\Exception $ex){                        
            $mensaje  = $this->ge_getMensajeError($ex);            
            return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('errorurl', $mensaje);
        }        
    }

    public function actionEmitirEgreso($idopcion,$idegreso,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $id_egreso = $this->funciones->decodificarmaestra($idegreso);
        $egreso    =   Egreso::where('id','=',$id_egreso)->first();
       
        try {
                DB::beginTransaction();
                /******************************/                    
                
                $egreso->estado_id                 =   'ESEG00000002';
                $egreso->estado_nombre             =   'EMITIDO';
                $egreso->fecha_emision             =   $this->fechaactual;
                $egreso->usuario_emision           =   Session::get('usuario')->id;
                $egreso->save();        
                                    
                DB::commit();                
        } catch (Exception $ex) {
            DB::rollback();
              $msj =$this->ge_getMensajeError($ex);
            return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('errorurl', $msj);                
        }
        /******************************/

        return Redirect::to('/gestion-de-egresos/'.$idopcion)->with('bienhecho', 'Egreso emitido con exito');
    }

    public function actionAjaxTrimestre(Request $request)
    {
        $fecha_comprobante           =   $request['fecha_comprobante'];

        if($fecha_comprobante == NULL){
            $trimestre_nombre     =   '';

        }else{


            $trimestre            = Trimestre::where('fecha_ini', '<=', $fecha_comprobante)
                                    ->where('fecha_fin', '>=', $fecha_comprobante)
                                    ->where('activo','=',1)
                                    ->first();            

            if(isset($trimestre)){
                $trimestre_nombre     = $trimestre->nombre;
            }else{
                $trimestre_nombre     =   '';
            }                        
            
            
        }        
        
        return View::make('movimiento/ajax/atrimestre',
                        [
                            'trimestre_nombre'       => $trimestre_nombre,                            
                            'ajax'                   =>  true,
                        ]);
    }


    public function actionBuscarRuc(Request $request)
    {

        $dni                =   $request['dni'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://dniruc.apisperu.com/api/v1/ruc/'.$dni.'?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImhlbnJyeWluZHVAZ21haWwuY29tIn0.m3cyXSejlDWl0BLcphHPUTfPNqpa5kXWoBcmQ6WvkII',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_AUTOREFERER => true,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_FOLLOWLOCATION => true,

          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
            'Accept: application/json'
        ],

        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $persona = json_decode($response, true);


        if(isset($persona['razonSocial'])){            
            print_r($persona['razonSocial']);
        }else{
            print_r(null);
        }

    }
}
