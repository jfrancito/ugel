<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Ingreso;
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
use App\Traits\IngresoTraits;
use Hashids;
use SplFileInfo;

class GestionIngresoController extends Controller
{
    use GeneralesTraits;    
    use IngresoTraits;


    public function actionListarIngresos($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista Ingresos');

        $user_id        =   Session::get('usuario')->id;
        $listadatos     =   $this->con_lista_ingresos();
        $funcion        =   $this;

        //dd($listadatos);

        return View::make('movimiento/listaingreso',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }

    public function actionAgregarIngreso($idopcion,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Ingreso');

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
                    $tipo_concepto_id                   =   $request['tipo_concepto_id'];
                    $detalle_concepto                   =   $request['detalle_concepto'];
                    $numero_deposito_bancario           =   $request['numero_deposito_bancario'];
                    $total                              =   floatval(str_replace(",","",$request['total']));
                    $observacion                        =   $request['observacion'];
                    $contrato_id                        =   $request['contrato_id'];                             
                    
                    $tipo_comprobante                   =   Estado::where('id','=',$tipo_comprobante_id)->first();
                    $tipo_documento                     =   Estado::where('id','=',$tipo_documento_id)->first();
                    $tipo_concepto                      =   Estado::where('id','=',$tipo_concepto_id)->first();

                    $trimestre                          =   Trimestre::where('fecha_ini', '<=', $fecha_comprobante)
                                                            ->where('fecha_fin', '>=', $fecha_comprobante)
                                                            ->where('activo','=',1)
                                                            ->first();                    

                    $idingreso                          =   $this->funciones->getCreateIdMaestra('ingresos');
                    $codigo                             =   $this->funciones->generar_codigo('ingresos',8);

                    $cabecera                           =   new Ingreso();
                    $cabecera->id                       =   $idingreso;
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
                    $cabecera->tipo_concepto_id         =   $tipo_concepto_id;
                    $cabecera->tipo_concepto_nombre     =   $tipo_concepto->nombre;
                    $cabecera->detalle_concepto         =   $detalle_concepto;
                    $cabecera->numero_deposito_bancario =   $numero_deposito_bancario;
                    $cabecera->total                    =   $total;
                    $cabecera->observacion              =   $observacion;                    
                    $cabecera->estado_id                =   'ESIN00000001';
                    $cabecera->estado_nombre            =   'GENERADO';                   
                    $cabecera->fecha_crea               =   $this->fechaactual;
                    $cabecera->usuario_crea             =   Session::get('usuario')->id;
                    $cabecera->save();                    

                    $codigo_institucion                 =   Session::get('institucion')->codigo;

                    $files                              =   $request['ingreso'];
                    if(!is_null($files)){
                        foreach($files as $file){

                            $rutafile                   =   storage_path('app/').$codigo_institucion.'/'.$this->pathFilesIng;
                            $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);                            
                            $nombre                     =   $codigo.'-'.$file->getClientOriginalName();

                            $rutadondeguardar           =   $codigo_institucion.'/'.$this->pathFilesIng;
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
                            $dcontrol->referencia_id    =   $idingreso;
                            $dcontrol->nombre_archivo   =   $nombre;
                            $dcontrol->url_archivo      =   $urlmedio;
                            $dcontrol->area_id          =   '';
                            $dcontrol->area_nombre      =   '';
                            $dcontrol->periodo_id       =   '';
                            $dcontrol->periodo_nombre   =   '';
                            $dcontrol->codigo_doc       =   '';
                            $dcontrol->nombre_doc       =   '';
                            $dcontrol->usuario_nombre   =   $usuario->nombre;
                            $dcontrol->tipo_archivo     =   'ingreso';
                            $dcontrol->fecha_crea       =   $this->fechaactual;
                            $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                            $dcontrol->save();

                            $cabecera->archivo_id       =   $idarchivo;
                            $cabecera->save();


                        }
                    }

                    if($contrato_id == '1'){
                        $contratos                          =   $request['contrato'];                    

                        if(!is_null($contratos)){
                            foreach($contratos as $contrato){

                                $rutafile_contrato          =   storage_path('app/').$codigo_institucion.'/'.$this->pathFilesCon;
                                $valor_contrato             =   $this->ge_crearCarpetaSiNoExiste($rutafile_contrato);                            
                                $nombre_contrato            =   $codigo.'-'.$contrato->getClientOriginalName();

                                $rutadondeguardar_contrato  =   $codigo_institucion.'/'.$this->pathFilesCon;
                                $urlmedio_contrato          =   'app/'.$rutadondeguardar_contrato.$nombre_contrato;

                                $nombreoriginal_contrato    =   $contrato->getClientOriginalName();
                                $info_contrato              =   new SplFileInfo($nombreoriginal_contrato);
                                $extension_contrato         =   $info_contrato->getExtension();
                                copy($contrato->getRealPath(),$rutafile_contrato.$nombre_contrato);
                                $idarchivo_contrato         =   $this->funciones->getCreateIdMaestra('archivos');

                                $dcontrol                   =   new Archivo;
                                $dcontrol->id               =   $idarchivo_contrato;
                                $dcontrol->size             =   filesize($contrato);
                                $dcontrol->extension        =   $extension_contrato;
                                $dcontrol->lote             =   $codigo;
                                $dcontrol->referencia_id    =   $idingreso;
                                $dcontrol->nombre_archivo   =   $nombre_contrato;
                                $dcontrol->url_archivo      =   $urlmedio_contrato;
                                $dcontrol->area_id          =   '';
                                $dcontrol->area_nombre      =   '';
                                $dcontrol->periodo_id       =   '';
                                $dcontrol->periodo_nombre   =   '';
                                $dcontrol->codigo_doc       =   '';
                                $dcontrol->nombre_doc       =   '';
                                $dcontrol->usuario_nombre   =   $usuario->nombre;
                                $dcontrol->tipo_archivo     =   'contrato';
                                $dcontrol->fecha_crea       =   $this->fechaactual;
                                $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                                $dcontrol->save();

                                $cabecera->archivo_contrato_id       =   $idarchivo_contrato;
                                $cabecera->save();
                            }
                        }
                    }else if($contrato_id == '3'){
                        $contrato_anterior_id               =   $request['contrato_anterior_id'];     

                        if($contrato_anterior_id != ''){
                            $id_contrato_anterior = $this->funciones->decodificarmaestra($contrato_anterior_id);

                            $contrato_anterior    = Archivo::where('id','=',$id_contrato_anterior)->first(); 

                            $idarchivo_contrato         =   $this->funciones->getCreateIdMaestra('archivos');

                            $dcontrol                   =   new Archivo;
                            $dcontrol->id               =   $idarchivo_contrato;
                            $dcontrol->size             =   $contrato_anterior->size;
                            $dcontrol->extension        =   $contrato_anterior->extension;
                            $dcontrol->lote             =   $codigo;
                            $dcontrol->referencia_id    =   $idingreso;
                            $dcontrol->nombre_archivo   =   $contrato_anterior->nombre_archivo;
                            $dcontrol->url_archivo      =   $contrato_anterior->url_archivo;
                            $dcontrol->area_id          =   '';
                            $dcontrol->area_nombre      =   '';
                            $dcontrol->periodo_id       =   '';
                            $dcontrol->periodo_nombre   =   '';
                            $dcontrol->codigo_doc       =   '';
                            $dcontrol->nombre_doc       =   '';
                            $dcontrol->usuario_nombre   =   $usuario->nombre;
                            $dcontrol->tipo_archivo     =   'contrato';
                            $dcontrol->fecha_crea       =   $this->fechaactual;
                            $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                            $dcontrol->save();

                            $cabecera->archivo_contrato_id       =   $idarchivo_contrato;
                            $cabecera->save();
                        }                        
                    }
                    
                    


                    DB::commit();
                
            } catch (Exception $ex) {
                DB::rollback();
                  $msj =$this->ge_getMensajeError($ex);
                return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', $msj);
            }
            /******************************/

            return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('bienhecho', 'Ingreso registrado con exito');

        }else{                        
            $arraytipocomprobante               =   array('TICO00000001','TIcO00000002');            
            $combo_tipo_comprobante             =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo comprobante','','TIPO_COMPROBANTE',$arraytipocomprobante);
            $select_tipo_comprobante            =   '';

            $arraytipodocumento                 =   array('TIDO00000001','TIDO00000004');
            $combo_tipo_documento               =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo documento','','TIPO_DOCUMENTO',$arraytipodocumento);
            $select_tipo_documento              =   '';

            $combo_tipo_concepto                =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_INGRESO');
            $select_tipo_concepto               =   '';

            $combo_contrato                     =   array('' => 'Seleccione contrato' , '0' => 'NO', '1' => 'SI', '3' => 'CONTRATO ANTERIOR');
            $select_contrato                    =   0;
           
            return View::make('movimiento/agregaringreso',
                        [
                            'idopcion'                          =>  $idopcion,                            

                            'combo_tipo_comprobante'            =>  $combo_tipo_comprobante, 
                            'select_tipo_comprobante'           =>  $select_tipo_comprobante,

                            'combo_tipo_documento'              =>  $combo_tipo_documento, 
                            'select_tipo_documento'             =>  $select_tipo_documento,

                            'combo_tipo_concepto'               =>  $combo_tipo_concepto, 
                            'select_tipo_concepto'              =>  $select_tipo_concepto,

                            'combo_contrato'                    =>  $combo_contrato, 
                            'select_contrato'                   =>  $select_contrato,
                        ]);
                }

    }

    public function actionModificarIngreso($idopcion,$idingreso,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $id_ingreso = $this->funciones->decodificarmaestra($idingreso);
        $ingreso    =   Ingreso::where('id','=',$id_ingreso)->first();

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
                    $tipo_concepto_id                   =   $request['tipo_concepto_id'];
                    $detalle_concepto                   =   $request['detalle_concepto'];
                    $numero_deposito_bancario           =   $request['numero_deposito_bancario'];
                    $total                              =   floatval(str_replace(",","",$request['total']));
                    $observacion                        =   $request['observacion'];
                    $contrato_id                        =   $request['contrato_id'];                             
                    
                    $tipo_comprobante                   =   Estado::where('id','=',$tipo_comprobante_id)->first();
                    $tipo_documento                     =   Estado::where('id','=',$tipo_documento_id)->first();
                    $tipo_concepto                      =   Estado::where('id','=',$tipo_concepto_id)->first();

                    $trimestre                          =   Trimestre::where('fecha_ini', '<=', $fecha_comprobante)
                                                            ->where('fecha_fin', '>=', $fecha_comprobante)
                                                            ->where('activo','=',1)
                                                            ->first();

                    $ingreso->trimestre_id              =   $trimestre->id;
                    $ingreso->trimestre_nombre          =   $trimestre->nombre;
                    $ingreso->fecha_comprobante         =   $fecha_comprobante;
                    $ingreso->tipo_comprobante_id       =   $tipo_comprobante_id;
                    $ingreso->tipo_comprobante_nombre   =   $tipo_comprobante->nombre;
                    $ingreso->serie                     =   $serie;         
                    $ingreso->numero                    =   $numero;
                    $ingreso->tipo_documento_id         =   $tipo_documento_id;
                    $ingreso->tipo_documento_nombre     =   $tipo_documento->nombre;
                    $ingreso->dni                       =   $dni;    
                    $ingreso->razon_social              =   $razon_social;              
                    $ingreso->tipo_concepto_id          =   $tipo_concepto_id;
                    $ingreso->tipo_concepto_nombre      =   $tipo_concepto->nombre;
                    $ingreso->detalle_concepto          =   $detalle_concepto;
                    $ingreso->numero_deposito_bancario  =   $numero_deposito_bancario;
                    $ingreso->total                     =   $total;
                    $ingreso->observacion               =   $observacion;                                        
                    $ingreso->fecha_mod                 =   $this->fechaactual;
                    $ingreso->usuario_mod               =   Session::get('usuario')->id;
                    $ingreso->save();        

                    $codigo                             =   $ingreso->codigo;
                    $codigo_institucion                 =   Session::get('institucion')->codigo;
                    
                    $files                      =   $request['ingreso'];
                    if(!is_null($files)){
                        foreach($files as $file){

                            $rutafile                   =   storage_path('app/').$codigo_institucion.'/'.$this->pathFilesIng;
                            $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);                            
                            $nombre                     =   $codigo.'-'.$file->getClientOriginalName();

                            $rutadondeguardar           =   $codigo_institucion.'/'.$this->pathFilesIng;
                            $urlmedio                   =   'app/'.$rutadondeguardar.$nombre;

                            $nombreoriginal             =   $file->getClientOriginalName();
                            $info                       =   new SplFileInfo($nombreoriginal);
                            $extension                  =   $info->getExtension();
                            copy($file->getRealPath(),$rutafile.$nombre);                            

                            $dcontrol                   =   Archivo::where('referencia_id','=',$ingreso->id)->where('tipo_archivo','=','ingreso')->where('activo','=',1)->first();
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

                    if($contrato_id == '1'){
                        $contratos                          =   $request['contrato'];                    
                        if(!is_null($contratos)){
                            foreach($contratos as $contrato){

                                $rutafile_contrato          =   storage_path('app/').$codigo_institucion.'/'.$this->pathFilesCon;
                                $valor_contrato             =   $this->ge_crearCarpetaSiNoExiste($rutafile_contrato);                            
                                $nombre_contrato            =   $codigo.'-'.$contrato->getClientOriginalName();

                                $rutadondeguardar_contrato  =   $codigo_institucion.'/'.$this->pathFilesCon;
                                $urlmedio_contrato          =   'app/'.$rutadondeguardar_contrato.$nombre_contrato;

                                $nombreoriginal_contrato    =   $contrato->getClientOriginalName();
                                $info_contrato              =   new SplFileInfo($nombreoriginal_contrato);
                                $extension_contrato         =   $info_contrato->getExtension();
                                copy($contrato->getRealPath(),$rutafile_contrato.$nombre_contrato);
                                

                                $dcontrol                   =   Archivo::where('referencia_id','=',$ingreso->id)->where('tipo_archivo','=','contrato')->where('activo','=',1)->first();         

                                if($dcontrol == NULL){                                
                                    $idarchivo_contrato         =   $this->funciones->getCreateIdMaestra('archivos');

                                    $dcontrol                   =   new Archivo;
                                    $dcontrol->id               =   $idarchivo_contrato;
                                    $dcontrol->size             =   filesize($contrato);
                                    $dcontrol->extension        =   $extension_contrato;
                                    $dcontrol->lote             =   $codigo;
                                    $dcontrol->referencia_id    =   $ingreso->id;
                                    $dcontrol->nombre_archivo   =   $nombre_contrato;
                                    $dcontrol->url_archivo      =   $urlmedio_contrato;
                                    $dcontrol->area_id          =   '';
                                    $dcontrol->area_nombre      =   '';
                                    $dcontrol->periodo_id       =   '';
                                    $dcontrol->periodo_nombre   =   '';
                                    $dcontrol->codigo_doc       =   '';
                                    $dcontrol->nombre_doc       =   '';
                                    $dcontrol->usuario_nombre   =   $usuario->nombre;
                                    $dcontrol->tipo_archivo     =   'contrato';
                                    $dcontrol->fecha_crea       =   $this->fechaactual;
                                    $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                                    $dcontrol->save();

                                    $ingreso->archivo_contrato_id       =   $idarchivo_contrato;
                                    $ingreso->save();
                                }else{
                                    $dcontrol->size             =   filesize($contrato);
                                    $dcontrol->extension        =   $extension_contrato;                            
                                    $dcontrol->nombre_archivo   =   $nombre_contrato;
                                    $dcontrol->url_archivo      =   $urlmedio_contrato;
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
                        }
                    }else if($contrato_id == '3'){
                        $contrato_anterior_id               =   $request['contrato_anterior_id'];     

                        if($contrato_anterior_id != ''){

                            $dcontrol                   =   Archivo::where('referencia_id','=',$ingreso->id)->where('tipo_archivo','=','contrato')->where('activo','=',1)->first();

                            $id_contrato_anterior = $this->funciones->decodificarmaestra($contrato_anterior_id);
                            $contrato_anterior    = Archivo::where('id','=',$id_contrato_anterior)->first();

                            if($dcontrol == NULL){                                 

                                $idarchivo_contrato         =   $this->funciones->getCreateIdMaestra('archivos');

                                $dcontrol                   =   new Archivo;
                                $dcontrol->id               =   $idarchivo_contrato;
                                $dcontrol->size             =   $contrato_anterior->size;
                                $dcontrol->extension        =   $contrato_anterior->extension;
                                $dcontrol->lote             =   $codigo;
                                $dcontrol->referencia_id    =   $ingreso->id;
                                $dcontrol->nombre_archivo   =   $contrato_anterior->nombre_archivo;
                                $dcontrol->url_archivo      =   $contrato_anterior->url_archivo;
                                $dcontrol->area_id          =   '';
                                $dcontrol->area_nombre      =   '';
                                $dcontrol->periodo_id       =   '';
                                $dcontrol->periodo_nombre   =   '';
                                $dcontrol->codigo_doc       =   '';
                                $dcontrol->nombre_doc       =   '';
                                $dcontrol->usuario_nombre   =   $usuario->nombre;
                                $dcontrol->tipo_archivo     =   'contrato';
                                $dcontrol->fecha_crea       =   $this->fechaactual;
                                $dcontrol->usuario_crea     =   Session::get('usuario')->id;
                                $dcontrol->save();

                                $ingreso->archivo_contrato_id       =   $idarchivo_contrato;
                                $ingreso->save();

                            }else{
                                $dcontrol->size             =   $contrato_anterior->size;
                                $dcontrol->extension        =   $contrato_anterior->extension;
                                $dcontrol->nombre_archivo   =   $contrato_anterior->nombre_archivo;
                                $dcontrol->url_archivo      =   $contrato_anterior->url_archivo;
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
                    }                    
                    DB::commit();                
            } catch (Exception $ex) {
                DB::rollback();
                  $msj =$this->ge_getMensajeError($ex);
                return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', $msj);                
            }
            /******************************/

            return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('bienhecho', 'Ingreso modificado con exito');



        }else{
                View::share('titulo','Modificar Ingreso');

                $trimestre_nombre                   =   $ingreso->trimestre_nombre;

                $arraytipocomprobante               =   array('TICO00000001','TIcO00000002');            
                $combo_tipo_comprobante             =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo comprobante','','TIPO_COMPROBANTE',$arraytipocomprobante);
                $select_tipo_comprobante            =   $ingreso->tipo_comprobante_id;

                $arraytipodocumento                 =   array('TIDO00000001','TIDO00000004');
                $combo_tipo_documento               =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione tipo documento','','TIPO_DOCUMENTO',$arraytipodocumento);
                $select_tipo_documento              =   $ingreso->tipo_documento_id;

                $combo_tipo_concepto                =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione tipo concepto','','TIPO_CONCEPTO_INGRESO');
                $select_tipo_concepto               =   $ingreso->tipo_concepto_id;

                $combo_contrato                     =   array('' => 'Seleccione contrato' , '0' => 'NO', '1' => 'SI', '3' => 'CONTRATO ANTERIOR');

                if($ingreso->archivo_contrato_id != NULL || $ingreso->archivo_contrato_id != ''){
                    $select_contrato                    =   1;
                }else{
                    $select_contrato                    =   0;
                }
                
                

                $multimedia                         =   Archivo::where('referencia_id','=',$ingreso->id)->where('tipo_archivo','=','ingreso')->where('activo','=',1)->first();
                

                $rutafoto                           =   !empty($multimedia) ? asset('storage/'.$multimedia->url_archivo) : asset('public/img/no-foto.png');               

                $multimedia_contrato                =   Archivo::where('referencia_id','=',$ingreso->id)->where('tipo_archivo','=','contrato')->where('activo','=',1)->first();
                

                $rutafoto_contrato                  =   !empty($multimedia_contrato) ? asset('storage/'.$multimedia_contrato->url_archivo) : asset('public/img/no-foto.png');  
               
                return View::make('movimiento/modificaringreso', 
                                [
                                    'ingreso'                   =>  $ingreso,
                                    'idopcion'                  =>  $idopcion,
                                    'trimestre_nombre'          =>  $trimestre_nombre,
                                    'combo_tipo_comprobante'    =>  $combo_tipo_comprobante, 
                                    'select_tipo_comprobante'   =>  $select_tipo_comprobante,
                                    'combo_tipo_documento'      =>  $combo_tipo_documento, 
                                    'select_tipo_documento'     =>  $select_tipo_documento,
                                    'combo_tipo_concepto'       =>  $combo_tipo_concepto, 
                                    'select_tipo_concepto'      =>  $select_tipo_concepto,
                                    'combo_contrato'            =>  $combo_contrato, 
                                    'select_contrato'           =>  $select_contrato,
                                    'rutafoto'                  =>  $rutafoto,
                                    'multimedia'                =>  $multimedia,
                                    'rutafoto_contrato'         =>  $rutafoto_contrato,
                                    'multimedia_contrato'       =>  $multimedia_contrato,
                                ]);
        }

    }

    public function actionDescargarArchivosIngreso($idopcion,$idarchivo)
    {

        $archivo_id = $this->funciones->decodificarmaestra($idarchivo);        

        View::share('titulo','Descargar Archivos del Ingreso');

        try{            
            $archivo                =   Archivo::where('id','=',$archivo_id)->first();
            $storagePath            =   storage_path($archivo->url_archivo);

            if(is_file($storagePath)){
                return response()->download($storagePath);
            }else{
                return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', 'archivo no encontrado');
            }            
        }catch(\Exception $ex){                        
            $mensaje  = $this->ge_getMensajeError($ex);            
            return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', $mensaje);
        }        
    }

    public function actionDescargarArchivosContrato($idopcion,$idarchivo)
    {

        $archivo_id = $this->funciones->decodificarmaestra($idarchivo);        

        View::share('titulo','Descargar Archivos del Ingreso');

        try{            
            $archivo                =   Archivo::where('id','=',$archivo_id)->first();
            $storagePath            =   storage_path($archivo->url_archivo);

            if(is_file($storagePath)){
                return response()->download($storagePath);
            }else{
                return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', 'archivo no encontrado');
            }            
        }catch(\Exception $ex){                        
            $mensaje  = $this->ge_getMensajeError($ex);            
            return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', $mensaje);
        }        
    }

    public function actionEmitirIngreso($idopcion,$idingreso,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $id_ingreso = $this->funciones->decodificarmaestra($idingreso);
        $ingreso    =   Ingreso::where('id','=',$id_ingreso)->first();
       
        try {
                DB::beginTransaction();
                /******************************/                    
                
                $ingreso->estado_id                 =   'ESIN00000002';
                $ingreso->estado_nombre             =   'EMITIDO';
                $ingreso->fecha_emision             =   $this->fechaactual;
                $ingreso->usuario_emision           =   Session::get('usuario')->id;
                $ingreso->save();        
                                    
                DB::commit();                
        } catch (Exception $ex) {
            DB::rollback();
              $msj =$this->ge_getMensajeError($ex);
            return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('errorurl', $msj);                
        }
        /******************************/

        return Redirect::to('/gestion-de-ingresos/'.$idopcion)->with('bienhecho', 'Ingreso emitido con exito');
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

    public function actionCompararSerieNumero(Request $request)
    {
        $serie                  =   $request['serie'];
        $numero                 =   $request['numero'];
        $tipo_comprobante_id    =   $request['tipo_comprobante_id'];
        $dni                    =   $request['dni'];
        $registro_id            =   $request['registro_id'];
        $accion                 =   $request['accion'];

        if($accion == 'I'){

            $registro        =  Ingreso::where('serie', '=', $serie)
                                ->where('numero', '=', $numero)                                                             
                                ->where('tipo_comprobante_id','=',$tipo_comprobante_id)
                                ->where('institucion_id','=',Session::get('institucion')->id)                                
                                ->IdBuscar($registro_id)    
                                ->where('activo','=',1)                                
                                ->first();

        }else{

            $registro        =  Egreso::where('serie', '=', $serie)
                                ->where('numero', '=', $numero)                                
                                ->where('tipo_comprobante_id','=',$tipo_comprobante_id)
                                ->where('dni','=',$dni)                                
                                ->IdBuscar($registro_id)                               
                                ->where('activo','=',1)
                                ->first();

        }

        if(isset($registro)){
            print_r('1');
        }else{
            print_r('0');
        }
    }

    public function actionAjaxListarContratosAnteriores(Request $request)
    {
        $dni              =     $request['dni'];
        $idopcion         =     $request['idopcion'];

        $listadatos       =     DB::table('archivos')
                                ->join('ingresos','archivos.referencia_id','=','ingresos.id')
                                ->where('ingresos.institucion_id','=',Session::get('institucion')->id)
                                ->where('ingresos.dni','=',$dni)
                                ->where('archivos.tipo_archivo','=','contrato')
                                ->where('archivos.activo','=',1)
                                ->select('ingresos.serie', 'ingresos.numero', 'ingresos.fecha_comprobante', 'ingresos.total', 'archivos.nombre_archivo', 'ingresos.archivo_contrato_id')
                                ->orderby('archivos.fecha_crea','desc')
                                ->get();

        return View::make('movimiento/modal/ajax/amccontratoanterior',
                         [          
                            'listadatos'    => $listadatos,  
                            'idopcion'      => $idopcion,  
                            'ajax'          => true,                            
                         ]);
    }
}
