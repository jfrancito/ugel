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

class GestionAdminConeiController extends Controller
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
        $listadatos     =   $this->con_lista_conei_admin();
        $funcion        =   $this;

        return View::make('conei/listaconeiadmin',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }


    public function actionGestionDetalleConei($idopcion, $idconei, Request $request) {

        View::share('titulo','DETALLE CONEI');
        $idconei            =   $this->funciones->decodificarmaestra($idconei);
        $conei              =   Conei::where('id','=',$idconei)
                                ->first();
        $institucion        =   Institucion::where('id','=',$conei->institucion_id)->first();
        $listaoic           =   OtroIntegranteConei::where('conei_id','=',$idconei)->orderby('representante_nombre','asc')->where('activo','=','1')->get();
        $larchivos          =   Archivo::where('referencia_id','=',$idconei)->where('tipo_archivo','=','requerimiento_conei')->where('activo','=','1')->get();
        $selectestado       =   $conei->estado_id; 
        $comboestado        =   $this->ge_combo_estado_cnei($conei->estado_id);

        //en proceso
        if($conei->estado_id == 'CEES00000005'){
            $arrayestado        =   array('CEES00000007','CEES00000008');
            $selectestado       =   'CEES00000007';
        }else{
            //verificado
            if($conei->estado_id == 'CEES00000007'){
                $arrayestado        =   array('CEES00000001');
                $selectestado       =   'CEES00000001';
            }else{
                $arrayestado        =   array();
                $selectestado       =   '';
            }
        }

        $comboestado        =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','','','CERTIFICADO_ESTADO',$arrayestado);

        $funcion            =   $this;


        return View::make('conei/gestiondetalleconei',
                         [
                            'conei'             =>  $conei,
                            'institucion'       =>  $institucion,
                            'listaoic'          =>  $listaoic,
                            'larchivos'         =>  $larchivos,

                            'selectestado'      =>  $selectestado,
                            'comboestado'       =>  $comboestado,

                            'unidad'            =>  $this->unidadmb,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }




    public function actionGestionConeiEstado($idopcion, $idconei, Request $request) {

        View::share('titulo','DETALLE CONEI');
        $idconei            =   $this->funciones->decodificarmaestra($idconei);


        try {
                DB::beginTransaction();
                /******************************/

            $estado_id                      =   $request['estado_id'];
            $descripcion                    =   $request['descripcion'];
            $estado                         =   Estado::where('id','=',$estado_id)->first();

            $conei                          =   Conei::where('id','=',$idconei)
                                                ->first();
            $conei->estado_id               =   $estado->id;
            $conei->estado_nombre           =   $estado->nombre;
            $conei->fecha_mod               =   $this->fechaactual;
            $conei->usuario_mod             =   Session::get('usuario')->id;
            $conei->save();                        

            $cabecera                       =   Certificado::where('referencia_id','=',$idconei)->where('procedente_id','=','APCN00000002')
                                                ->first();
            $cabecera->estado_id            =   $estado->id;
            $cabecera->estado_nombre        =   $estado->nombre;
            $cabecera->observacion          =   $descripcion;
            $cabecera->fecha_mod            =   $this->fechaactual;
            $cabecera->usuario_mod          =   Session::get('usuario')->id;
            $cabecera->save();


            DetalleCertificado::where("certificado_id", '=',$cabecera->id)
                ->update(["fecha_mod" => $this->fechaactual, "usuario_mod" => Session::get('usuario')->id, 
                            "estado_id" => $estado->id,
                            "estado_nombre" => $estado->nombre,
                        ]);

            $codigo         =   $cabecera->codigo;
            $usuario                        =   User::where('id',Session::get('usuario')->id)->first();
            $files                      =   $request['certificado'];
            if(!is_null($files)){
                foreach($files as $file){

                    $rutafile                   =   storage_path('app/').$this->pathFilesCer.$codigo.'/';
                    $valor                      =   $this->ge_crearCarpetaSiNoExiste($rutafile);
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
                    $dcontrol->referencia_id    =   $cabecera->id;
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
            return Redirect::to('/gestion-admin-conei/'.$idopcion)->with('errorurl', $msj);
        }
        /******************************/

        return Redirect::to('/gestion-admin-conei/'.$idopcion)->with('bienhecho', 'Documento gestionado con exito');



    }



    public function actionModificarConei($idopcion,$idconei,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/

        $idconei            =   $this->funciones->decodificarmaestra($idconei);

        View::share('titulo','Modificar Requerimiento CONEI');
        if($_POST)
        {

        try {
                DB::beginTransaction();
                /******************************/

            $institucion_id                             =   Session::get('institucion')->id;;

            $institucion                                =   Institucion::where('id','=',$institucion_id)->first();
            $usuario                                    =   User::where('id',Session::get('usuario')->id)->first();


            $conei                                      =   Conei::where('id','=',$idconei)->first();
            $conei->estado_id                           =   'CEES00000005';
            $conei->estado_nombre                       =   'EN PROCESO';
            $conei->fecha_mod                           =   $this->fechaactual;
            $conei->usuario_mod                         =   Session::get('usuario')->id;
            $conei->save();

            $idrequerimiento                            =   $idconei;
            $codigo                                     =   $conei->codigo;

            OtroIntegranteConei::where("conei_id", '=',$conei->id)
                ->update(["fecha_mod" => $this->fechaactual, "usuario_mod" => Session::get('usuario')->id, 
                            "activo" => 0
                        ]);
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


            $tarchivos                              =  DocumentosAsociado::where('activo','=','1')->where('id','=','APCN00000002')->get();

                //dd($tarchivos);

            foreach($tarchivos as $index=>$item){
                //01

                $files                              =   $request[$item->cod_archivo];

                if(!is_null($files)){
                    foreach($files as $file){

                        Archivo::where("referencia_id", '=',$conei->id)->where('tipo_archivo','=','requerimiento_conei')->where('codigo_doc','=',$item->cod_archivo)
                        ->update(["fecha_mod" => $this->fechaactual, "usuario_mod" => Session::get('usuario')->id, 
                                    "activo" => 0
                                ]);

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





            $cabecera                       =   Certificado::where('referencia_id','=',$conei->id)->where('procedente_id','=','APCN00000002')
                                                ->first();
            $cabecera->estado_id            =   'CEES00000005';
            $cabecera->estado_nombre        =   'EN PROCESO';
            $cabecera->fecha_mod            =   $this->fechaactual;
            $cabecera->usuario_mod          =   Session::get('usuario')->id;
            $cabecera->save();


            DetalleCertificado::where("certificado_id", '=',$cabecera->id)
                ->update(["fecha_mod" => $this->fechaactual, "usuario_mod" => Session::get('usuario')->id, 
                            "estado_id" => 'CEES00000005',
                            "estado_nombre" => 'EN PROCESO'
                        ]);
                

            DB::commit();
            } catch (Exception $ex) {
                DB::rollback();
                  $msj =$this->ge_getMensajeError($ex);
                return Redirect::to('/gestion-admin-conei/'.$idopcion)->with('errorurl', $msj);
            }
            /******************************/

            return Redirect::to('/gestion-admin-conei/'.$idopcion)->with('bienhecho', 'Requerimiento '.$conei->codigo.' modificado con exito');

        }else{


            $institucion_id                     =   Session::get('usuario')->institucion_id;
            $institucion                        =   Institucion::where('id','=',$institucion_id)->first();
            $director                           =   Director::where('institucion_id','=',$institucion_id)->where('activo','=','1')->first();

            $combotd                            =   $this->gn_generacion_combo_tabla('estados','id','nombre','','','TIPO_DOCUMENTO');
            $selecttd                           =   'TIDO00000001';
            $conei                              =   Conei::where('id','=',$idconei)->first();
            $otrosintegrantes                   =   OtroIntegranteConei::where('conei_id','=',$idconei)->where('activo','=','1')->get();

            $director_i_tipodocumento_id        =   'TIDO00000001';
            $director_i_tipodocumento_nombre    =   'DNI';
            $director_i_representante_id        =   'ESRP00000001';
            $director_i_representante_nombre    =   'DIRECTOR';
            $director_i_documento               =   $director->dni;
            $director_i_nombres                 =   $director->nombres;

            $array_detalle_producto             =   array();
            foreach($otrosintegrantes as $index=>$item){
                $arraynuevo                         =   array(
                                                            "fila"                      => $index + 1,
                                                            "tdg"                       => $item->tipo_documento_id,
                                                            "tdgtexto"                  => $item->tipo_documento_nombre,
                                                            "documentog"                => $item->documento,
                                                            "nombresg"                  => $item->nombres,
                                                            "dcargoni"                  => $item->cargo,
                                                            "representante_id"          => $item->representante_id,
                                                            "representante_txt"         => $item->representante_nombre,
                                                            "codigo_modular_id"         => $item->nivel_id,
                                                            "niveltexto"                => $item->nivel_nombre,
                                                        );
                array_push($array_detalle_producto,$arraynuevo);
            }


            $array_detalle_producto     =   $this->ordernar_array($array_detalle_producto);


            $disabled                   =   false;

            $procedencia_id             =   'APCN00000002';
            $array_periodos             =   DetalleCertificado::where('institucion_id','=',$institucion_id)
                                            ->where('procedente_id','=',$procedencia_id)
                                            ->where('activo','=',1)
                                            ->whereIn('estado_id',['CEES00000001','CEES00000005','CEES00000008'])
                                            ->pluck('periodo_id')                                   
                                            ->toArray();

            $certificado                =   Certificado::where('referencia_id','=',$idconei)->where('procedente_id','=','APCN00000002')
                                                ->first();




            $detallecerti               =   DetalleCertificado::where('certificado_id','=',$certificado->id)
                                            ->where('activo','=','1')->where('inicio_fin','=','I')->first();

            $detallecertf               =   DetalleCertificado::where('certificado_id','=',$certificado->id)
                                            ->where('activo','=','1')->where('inicio_fin','=','F')->first();

            $comboperiodo               =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo','','APAFA_CONEI_PERIODO',$array_periodos);
            $selectperiodo              =   $detallecerti->periodo_id;
            $comboperiodofin            =   $this->gn_generacion_combo_tabla('estados','id','nombre','Seleccione periodo fin','','APAFA_CONEI_PERIODO',$array_periodos);
            $selectperiodofin           =   '';
            if(count($detallecertf)>0){
                $selectperiodofin           =   $detallecertf->periodo_id;;
            }


            $ind                        =   0;
            $checked                    =   false;
            $mensaje                    =   'SELECCIONE PERIODOS';
            $color                      =   '';

            $tarchivos                  =   DocumentosAsociado::where('activo','=','1')->where('id','=','APCN00000002')->get();




            $arrayrepresentante         =   $this->array_representante_obligatrio(Session::get('institucion')->tipo_institucion);

            $robligatorios              =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE')
                                            ->whereIn('id',$arrayrepresentante)
                                            ->get();   

            $lrepresentantes            =   Estado::where('tipoestado','=','ESTADO_REPRESENTANTE')->get();


            $archivo                    =   Archivo::where('referencia_id','=',$conei->id)->where('tipo_archivo','=','requerimiento_conei')->where('activo','=','1')->first();
            $archivos                   =   Archivo::where('referencia_id','=',$conei->id)->where('tipo_archivo','=','requerimiento_conei')->where('activo','=','1')->get();
            $rutafoto                   =   asset('storage/app/requerimiento_conei/'.$archivo->lote.'/');

            $archivootro                =   Archivo::where('referencia_id','=',$conei->id)->where('codigo_doc','=','000006')->where('tipo_archivo','=','requerimiento_conei')->where('activo','=','1')->first();
            $otro_doc                   =   '';
            if(count($archivootro)>0){
                $otro_doc                   =   'SI';
            }

            //dd($array_detalle_producto);

            return View::make('requerimiento.modificarconei',
                        [
                            'array_detalle_producto'                =>  $array_detalle_producto,
                            'robligatorios'                         =>  $robligatorios,
                            'archivos'                              =>  $archivos,
                            'conei'                                 =>  $conei,
                            'rutafoto'                              =>  $rutafoto,
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
                            'certificado'                           =>  $certificado,

                            'color'                                 =>  $color,
                            'otro_doc'                              =>  $otro_doc,


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
