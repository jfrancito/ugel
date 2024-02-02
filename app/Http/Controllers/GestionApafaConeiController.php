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
class GestionApafaConeiController extends Controller
{
    use GeneralesTraits;
    use ApafaConeiTraits;
    public function actionListarApafaConei($idopcion)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Lista Requerimiento APAFA Y CONEI');
        // $codempresa = Session::get('empresas')->id;
        $user_id        =   Session::get('usuario')->id;
        $listadatos     =   $this->con_lista_requerimiento();
        $funcion        =   $this;

        return View::make('requerimiento/listaapafaconei',
                         [
                            'listadatos'        =>  $listadatos,
                            'funcion'           =>  $funcion,
                            'idopcion'          =>  $idopcion,
                         ]);
    }



    public function actionAgregarApafaConei($idopcion,Request $request)
    {
        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Anadir');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        View::share('titulo','Agregar Requerimiento APAFA Y CONEI');
        if($_POST)
        {

            $dni_director                               =   $request['dni_director'];
            $nombre_director                            =   $request['nombre_director'];
            $apellidopaterno_director                   =   $request['apellidopaterno_director'];
            $apellidomaterno_director                   =   $request['apellidomaterno_director'];
            $telefono_director                          =   $request['telefono_director'];
            $correo_director                            =   $request['correo_director'];
            $dni_director_conei                         =   $request['dni_director_conei'];
            $nombre_director_conei                      =   $request['nombre_director_conei'];
            $apellidopaterno_director_conei             =   $request['apellidopaterno_director_conei'];
            $apellidomaterno_director_conei             =   $request['apellidomaterno_director_conei'];
            $institucion_id                             =   $request['institucion_id'];
            $director_id                                =   $request['director_id'];


            $dni_presidente_apafa                       =   $request['dni_presidente_apafa'];
            $nombre_presidente_apafa                    =   $request['nombre_presidente_apafa'];
            $apellidopaterno_presidente_apafa           =   $request['apellidopaterno_presidente_apafa'];
            $apellidomaterno_presidente_apafa           =   $request['apellidomaterno_presidente_apafa'];



            $idrequerimiento                            =   $this->funciones->getCreateIdMaestra('requerimientos');
            $codigo                                     =   $this->funciones->generar_codigo('requerimientos',8);

            $cabecera                                   =   new Requerimiento;
            $cabecera->id                               =   $idrequerimiento;
            $cabecera->codigo                           =   $codigo;
            $cabecera->institucion_id                   =   $institucion_id;
            $cabecera->director_id                      =   $director_id;            
            $cabecera->dni_director                     =   $dni_director;
            $cabecera->nombre_director                  =   $nombre_director;
            $cabecera->apellidopaterno_director         =   $apellidopaterno_director;
            $cabecera->apellidomaterno_director         =   $apellidomaterno_director;
            $cabecera->telefono_director                =   $telefono_director;
            $cabecera->correo_director                  =   $correo_director;

            $cabecera->dni_director_conei               =   $dni_director_conei;
            $cabecera->nombre_director_conei            =   $nombre_director_conei;
            $cabecera->apellidopaterno_director_conei   =   $apellidopaterno_director_conei;
            $cabecera->apellidomaterno_director_conei   =   $apellidomaterno_director_conei;

            $cabecera->dni_presidente_apafa             =   $dni_presidente_apafa;
            $cabecera->nombre_presidente_apafa          =   $nombre_presidente_apafa;
            $cabecera->apellidopaterno_presidente_apafa =   $apellidopaterno_presidente_apafa;
            $cabecera->apellidomaterno_presidente_apafa =   $apellidomaterno_presidente_apafa;

            $cabecera->fecha_crea                       =   $this->fechaactual;
            $cabecera->usuario_crea                     =   Session::get('usuario')->id;
            $cabecera->save();

            $usuario                            =   User::where('id',Session::get('usuario')->id)->first();
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



            $filesapafa                                      =   $request['uploadapafa'];
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


            return Redirect::to('/gestion-apafa-conei/'.$idopcion)->with('bienhecho', 'Requerimiento '.$codigo.' registrado con exito');

        }else{

            $institucion_id =   Session::get('usuario')->institucion_id;
            $institucion    =   Institucion::where('id','=',$institucion_id)->first();
            $director       =   Director::where('institucion_id','=',$institucion_id)->where('activo','=','1')->first();


            return View::make('requerimiento.agregarapafaconei',
                        [
                            'idopcion'          =>  $idopcion,
                            'institucion'       =>  $institucion,
                            'director'          =>  $director,
                        ]);
        }
    }



    public function actionBuscardni(Request $request)
    {

        $dni                =   $request['dni'];

        $curl               =   curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://ugelhuanta.gob.pe/transparencia/apafaconei/consulta_reniec.php',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('dni'=>$dni)
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        $string = $response;
        // Encontrar el string entre corchetes
        $inicio = strpos($string, '[');
        $fin = strrpos($string, ']');
        $substring = substr($string, $inicio, $fin - $inicio + 1);
        // Decodificar el JSON
        //$data = json_decode($substring, true);
        // Imprimir los datos
        print_r($substring);


    }




}
