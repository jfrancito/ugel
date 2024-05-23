<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Exception;
use App\Modelos\Categoria;
use App\Modelos\Producto;
use App\Modelos\CalendarioProducto as Registro;
use App\Modelos\Multimedia as Foto;
use App\Modelos\Producto as DetalleRegistro;
use App\Modelos\Multimedia;
use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Calendario;


use App\Modelos\Estado;
use App\Modelos\DetalleCertificado;
use App\Modelos\Certificado;
use App\Modelos\Institucion;



use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use Session;
use View;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use TPDF;
use ZipArchive;
use PDO;


use GuzzleHttp\Client;
use App\Traits\GeneralesTraits;

class CargarDatosCertificadoController extends Controller
{
    use GeneralesTraits;



    public function actionCargarDatos($idopcion) {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion, 'Ver');
        if ($validarurl != 'true') {return $validarurl;}
        /******************************************************/
        View::share('titulo',' Cargar Datos Certificado');
        $user_id            =   Session::get('usuario')->id;
        $swedit            =   $this->ge_isUsuarioAdmin($user_id);

        return View::make('cardadata/cargarcertificado',
            [
                // 'listaregistros'    =>  $listaregistros,
                'idopcion'          =>  $idopcion,
                'swedit'            =>  $swedit,
            ]);
    }


   public function actionCargarDatoCertificado($idopcion,Request $request)
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Modificar');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        $fileexcel              =   $request['inputdatosexcel'];
        /** Cargando el excel mediante un archivo recibido vía POST con name=RubrosTrabajador en htnml */
        $contador=0;
        $cant_reg=0;
        $msj = '';
        $mensaje = '';

        if($_POST)
        {
            if (!empty($fileexcel)) 
            {
                
                $datosExcel=Excel::load($fileexcel, function($reader) {
                    /*** $reader->get() nos permite obtener todas las  filas de nuestro archivo**/
                    $excel = $reader->get();
                })->get();
                //al leer el excel la primera pestaña es cero y las demas son 1, 2 como si fuese un vector
                // la cabecera o primera linea son tomadas como index para cada registro
                // si la cabecera tiene espacios en blanco pone un subgion entre las palabras que conforman el nombre de la cabecera
                try{    
                        DB::beginTransaction();

                        $listadatos = $datosExcel;
                        //dd($listadatos);
                        $cant_reg = count($listadatos);
                        // dd($cant_reg);
                        $fila=0;
                        $idproducto='';


                        foreach ($listadatos as $index => $item) 
                        {

                            //1 PERIODO
                            $periodofin_sw = 0;
                            if ($item->periodo_fin == '1 PERIODO'){$periodofin_sw = 1;}


                            if (ltrim(rtrim($item->codigo_local)) == null){$mensaje ='CODIGO LOCAL VACIO';throw new Exception($mensaje);}
                            if ($item->tipo == null){$mensaje ='TIPO VACIO';throw new Exception($mensaje);}
                            if ($item->periodo_inicio == null){$mensaje ='PERIODO INICIO VACIO';throw new Exception($mensaje);}


                            if ($periodofin_sw == 0){
                                if ($item->periodo_fin == null){$mensaje ='PERIODO FIN VACIO';throw new Exception($mensaje);}
                            }

                            if ($item->estado == null){$mensaje ='ESTADO VACIO';throw new Exception($mensaje);}
                            //if ($item->observacion == null){$mensaje ='OBSERVACION VACIO';throw new Exception($mensaje);}
                            //if ($item->nro_tramite == null){$mensaje ='NRO_TRAMITE VACIO';throw new Exception($mensaje);}

                            $institucion = Institucion::where('codigo','=',ltrim(rtrim($item->codigo_local)))->first();
                            if (count($institucion)<=0){$mensaje ='NO EXISTE CODIGO DE LOCAL EN LA BASE DE DATOS '.ltrim(rtrim($item->codigo_local)) . ' FILA : ' .$index;throw new Exception($mensaje);}
                            $tipo                    =   Estado::where('tipoestado','=','APAFA_CONEI')->where('nombre','=',$item->tipo)->first();
                            if (count($tipo)<=0){$mensaje ='NO EXISTE TIPO EN LA BASE DE DATOS '.$item->tipo. ' FILA : ' .$index;throw new Exception($mensaje);}
                            $periodoinicio           =   Estado::where('tipoestado','=','APAFA_CONEI_PERIODO')->where('nombre','=',$item->periodo_inicio)->first();
                            if (count($periodoinicio)<=0){$mensaje ='NO EXISTE PERIODO INICIO DE LOCAL EN LA BASE DE DATOS '.$item->periodo_inicio. ' FILA : ' .$index;throw new Exception($mensaje);}


                            $periodofin           =   Estado::where('tipoestado','=','APAFA_CONEI_PERIODO')->where('nombre','=',$item->periodo_fin)->first();
                            if ($periodofin_sw == 0){

                                if (count($periodofin)<=0){$mensaje ='NO EXISTE PERIODO FIN DE LOCAL EN LA BASE DE DATOS '.$item->periodo_fin. ' FILA : ' .$index;throw new Exception($mensaje);}
                            }

                            $estado             =   Estado::where('tipoestado','=','CERTIFICADO_ESTADO')->where('nombre','=',$item->estado)->first();
                            if (count($estado)<=0){$mensaje ='NO EXISTE ESTADO DE LOCAL EN LA BASE DE DATOS '.$item->estado. ' FILA : ' .$index;throw new Exception($mensaje);}


                            //periodo inicio
                            $detallecertificadoinicio       =   DetalleCertificado::where('institucion_codigo','=',ltrim(rtrim($item->codigo_local)))
                                                                ->where('estado_id','=','CEES00000001')
                                                                ->where('procedente_id','=',$tipo->id)
                                                                ->where('periodo_id','=',$periodoinicio->id)
                                                                ->first();




                            if (count($detallecertificadoinicio)>0){ $mensaje ='EXISTE UN CERTIFICADO EN ESTE LOCAL Y ESTE PERIODO APROBADO '.ltrim(rtrim($item->codigo_local)). ' FILA : ' .$index;throw new Exception($mensaje);}
                            if ($periodofin_sw == 0){
                                //periodo fin
                                $detallecertificadofin      =   DetalleCertificado::where('institucion_codigo','=',ltrim(rtrim($item->codigo_local)))
                                                                    ->where('estado_id','=','CEES00000001')
                                                                    ->where('procedente_id','=',$tipo->id)
                                                                    ->where('periodo_id','=',$periodofin->id)
                                                                    ->first();

                                if (count($detallecertificadofin)>0){ $mensaje ='EXISTE UN CERTIFICADO EN ESTE LOCAL Y ESTE PERIODO APROBADO '.ltrim(rtrim($item->codigo_local)). ' FILA : ' .$index;throw new Exception($mensaje);}
                            }

                            $detallecertificado             =   DetalleCertificado::where('institucion_codigo','=',ltrim(rtrim($item->codigo_local)))
                                                                ->whereIn('estado_id', ['CEES00000004', 'CEES00000005','CEES00000006'])
                                                                ->where('procedente_id','=',$tipo->id)
                                                                ->where('periodo_id','=',$periodoinicio->id)
                                                                ->first();
                            //AGREGAR
                            if(count($detallecertificado)<=0){

                                $nombreperiodog                 =   '';
                                if($periodofin_sw == 0){
                                    $nombreperiodog                 =   $periodoinicio->nombre . '-' .$periodofin->nombre;
                                }else{
                                    $nombreperiodog                 =   $periodoinicio->nombre;
                                }

                                $idcertificado                  =   $this->funciones->getCreateIdMaestra('certificados');
                                $codigo                         =   $this->funciones->generar_codigo('certificados',8);

                                $cabecera                       =   new Certificado();
                                $cabecera->id                   =   $idcertificado;
                                $cabecera->codigo               =   $codigo;
                                $cabecera->institucion_id       =   $institucion->id;
                                $cabecera->institucion_codigo   =   $institucion->codigo;
                                $cabecera->institucion_nombre   =   $institucion->nombre;
                                $cabecera->institucion_nivel    =   $institucion->nivel;
                                $cabecera->periodo_nombre       =   $nombreperiodog;
                                $cabecera->procedente_id        =   $tipo->id;
                                $cabecera->procedente_nombre    =   $tipo->nombre;
                                $cabecera->estado_id            =   $estado->id;
                                $cabecera->estado_nombre        =   $estado->nombre;
                                $cabecera->observacion          =   $item->observacion;
                                $cabecera->numero_tramite       =   $item->nro_tramite;
                                $cabecera->fecha_crea           =   $this->fechaactual;
                                $cabecera->usuario_crea         =   Session::get('usuario')->id;
                                $cabecera->save();


                                $iddetcertificado               =   $this->funciones->getCreateIdMaestra('detallecertificados');
                                //primer periodo
                                $cabeceradet                       =   new DetalleCertificado();
                                $cabeceradet->id                   =   $iddetcertificado;
                                $cabeceradet->codigo               =   $codigo;
                                $cabeceradet->institucion_id       =   $institucion->id;
                                $cabeceradet->institucion_codigo   =   $institucion->codigo;
                                $cabeceradet->institucion_nombre   =   $institucion->nombre;
                                $cabeceradet->institucion_nivel    =   $institucion->nivel;
                                $cabeceradet->periodo_id           =   $periodoinicio->id;    
                                $cabeceradet->periodo_nombre       =   $periodoinicio->nombre;
                                $cabeceradet->procedente_id        =   $tipo->id;
                                $cabeceradet->procedente_nombre    =   $tipo->nombre;
                                $cabeceradet->certificado_id       =   $idcertificado;
                                $cabeceradet->estado_id            =   $estado->id;
                                $cabeceradet->estado_nombre        =   $estado->nombre;
                                $cabeceradet->inicio_fin           =   'I';

                                $cabeceradet->fecha_crea           =   $this->fechaactual;
                                $cabeceradet->usuario_crea         =   Session::get('usuario')->id;
                                $cabeceradet->save();

                                if($periodofin_sw == 0){

                                    $iddetcertificado               =   $this->funciones->getCreateIdMaestra('detallecertificados');
                                    //primer periodo
                                    $cabeceradet                       =   new DetalleCertificado();
                                    $cabeceradet->id                   =   $iddetcertificado;
                                    $cabeceradet->codigo               =   $codigo;
                                    $cabeceradet->institucion_id       =   $institucion->id;
                                    $cabeceradet->institucion_codigo   =   $institucion->codigo;
                                    $cabeceradet->institucion_nombre   =   $institucion->nombre;
                                    $cabeceradet->institucion_nivel    =   $institucion->nivel;
                                    $cabeceradet->periodo_id           =   $periodofin->id;    
                                    $cabeceradet->periodo_nombre       =   $periodofin->nombre;
                                    $cabeceradet->procedente_id        =   $tipo->id;
                                    $cabeceradet->procedente_nombre    =   $tipo->nombre;
                                    $cabeceradet->certificado_id       =   $idcertificado;
                                    $cabeceradet->estado_id            =   $estado->id;
                                    $cabeceradet->estado_nombre        =   $estado->nombre;
                                    $cabeceradet->inicio_fin           =   'F';

                                    $cabeceradet->fecha_crea           =   $this->fechaactual;
                                    $cabeceradet->usuario_crea         =   Session::get('usuario')->id;
                                    $cabeceradet->save(); 
                                    
                                }

                            }else{
 


                                $nombreperiodog                 =   '';
                                if($periodofin_sw == 0){
                                    $nombreperiodog                 =   $periodoinicio->nombre . '-' .$periodofin->nombre;
                                }else{
                                    $nombreperiodog                 =   $periodoinicio->nombre;
                                }

                                $certificado                       =   Certificado::where('id','=',$detallecertificado->certificado_id)->first();
                                $certificado->institucion_id       =   $institucion->id;
                                $certificado->institucion_codigo   =   $institucion->codigo;
                                $certificado->institucion_nombre   =   $institucion->nombre;
                                $certificado->institucion_nivel    =   $institucion->nivel;
                                $certificado->periodo_nombre       =   $nombreperiodog;
                                $certificado->procedente_id        =   $tipo->id;
                                $certificado->procedente_nombre    =   $tipo->nombre;
                                $certificado->estado_id            =   $estado->id;
                                $certificado->estado_nombre        =   $estado->nombre;
                                $certificado->fecha_mod            =   $this->fechaactual;
                                $certificado->usuario_mod          =   Session::get('usuario')->id;
                                $certificado->save();


                                //PERIODO INICIO
                                $detallecertificadoI                =   DetalleCertificado::where('certificado_id','=',$detallecertificado->certificado_id)
                                                                        ->where('inicio_fin','=','I')
                                                                        ->where('periodo_id','=',$periodoinicio->id)
                                                                        ->first();

                                                                         //dd($periodoinicio); 

                                $detallecertificadoI->institucion_id       =   $institucion->id;
                                $detallecertificadoI->institucion_codigo   =   $institucion->codigo;
                                $detallecertificadoI->institucion_nombre   =   $institucion->nombre;
                                $detallecertificadoI->institucion_nivel    =   $institucion->nivel;
                                $detallecertificadoI->periodo_id           =   $periodoinicio->id;    
                                $detallecertificadoI->periodo_nombre       =   $periodoinicio->nombre;
                                $detallecertificadoI->procedente_id        =   $tipo->id;
                                $detallecertificadoI->procedente_nombre    =   $tipo->nombre;
                                $detallecertificadoI->estado_id            =   $estado->id;
                                $detallecertificadoI->estado_nombre        =   $estado->nombre;
                                $detallecertificadoI->fecha_mod           =   $this->fechaactual;
                                $detallecertificadoI->usuario_mod         =   Session::get('usuario')->id;
                                $detallecertificadoI->save();



                                //SI NO EXISTE
                                if($periodofin_sw == 0){

                                    //PERIODO FIN
                                    $detallecertificadoF                       =    DetalleCertificado::where('certificado_id','=',$detallecertificado->certificado_id)
                                                                                    ->where('inicio_fin','=','F')
                                                                                    ->where('periodo_id','=',$periodofin->id)
                                                                                    ->first();

                                    if(count($detallecertificadoF)<=0){

                                        $iddetcertificado                  =   $this->funciones->getCreateIdMaestra('detallecertificados');
                                        $codigo                            =   $this->funciones->generar_codigo('certificados',8);

                                        //primer periodo
                                        $cabeceradet                       =   new DetalleCertificado();
                                        $cabeceradet->id                   =   $iddetcertificado;
                                        $cabeceradet->codigo               =   $codigo;
                                        $cabeceradet->institucion_id       =   $institucion->id;
                                        $cabeceradet->institucion_codigo   =   $institucion->codigo;
                                        $cabeceradet->institucion_nombre   =   $institucion->nombre;
                                        $cabeceradet->institucion_nivel    =   $institucion->nivel;
                                        $cabeceradet->periodo_id           =   $periodofin->id;    
                                        $cabeceradet->periodo_nombre       =   $periodofin->nombre;
                                        $cabeceradet->procedente_id        =   $tipo->id;
                                        $cabeceradet->procedente_nombre    =   $tipo->nombre;
                                        $cabeceradet->certificado_id       =   $detallecertificado->certificado_id;
                                        $cabeceradet->estado_id            =   $estado->id;
                                        $cabeceradet->estado_nombre        =   $estado->nombre;
                                        $cabeceradet->inicio_fin           =   'F';

                                        $cabeceradet->fecha_crea           =   $this->fechaactual;
                                        $cabeceradet->usuario_crea         =   Session::get('usuario')->id;
                                        $cabeceradet->save(); 
                                        
                                    }else{

                                        // SI YA EXISTE MODIFICAMOS
                                        $detallecertificadoF->institucion_id       =   $institucion->id;
                                        $detallecertificadoF->institucion_codigo   =   $institucion->codigo;
                                        $detallecertificadoF->institucion_nombre   =   $institucion->nombre;
                                        $detallecertificadoF->institucion_nivel    =   $institucion->nivel;
                                        $detallecertificadoF->periodo_id           =   $periodoinicio->id;    
                                        $detallecertificadoF->periodo_nombre       =   $periodoinicio->nombre;
                                        $detallecertificadoF->procedente_id        =   $tipo->id;
                                        $detallecertificadoF->procedente_nombre    =   $tipo->nombre;
                                        $detallecertificadoF->estado_id            =   $estado->id;
                                        $detallecertificadoF->estado_nombre        =   $estado->nombre;
                                        $detallecertificadoF->fecha_mod           =   $this->fechaactual;
                                        $detallecertificadoF->usuario_mod         =   Session::get('usuario')->id;
                                        $detallecertificadoF->save();



                                    }
                                }else{

                                    $detallecertificadoFin                    =    DetalleCertificado::where('certificado_id','=',$detallecertificado->certificado_id)
                                                                                    ->where('inicio_fin','=','F')
                                                                                    ->first();


                                    if(count($detallecertificadoFin)>0){
                                        // SI YA EXISTE MODIFICAMOS
                                        $detallecertificadoFin->institucion_id       =   $institucion->id;
                                        $detallecertificadoFin->institucion_codigo   =   $institucion->codigo;
                                        $detallecertificadoFin->institucion_nombre   =   $institucion->nombre;
                                        $detallecertificadoFin->institucion_nivel    =   $institucion->nivel;
                                        $detallecertificadoFin->periodo_id           =   $periodoinicio->id;    
                                        $detallecertificadoFin->periodo_nombre       =   $periodoinicio->nombre;
                                        $detallecertificadoFin->procedente_id        =   $tipo->id;
                                        $detallecertificadoFin->procedente_nombre    =   $tipo->nombre;
                                        $detallecertificadoFin->estado_id            =   'CEES00000002';
                                        $detallecertificadoFin->estado_nombre        =   'EXTORNO';
                                        $detallecertificadoFin->fecha_mod            =   $this->fechaactual;
                                        $detallecertificadoFin->usuario_mod          =   Session::get('usuario')->id;
                                        $detallecertificadoFin->save();

                                    }

                                }

                            }
                        }

                    DB::commit();

                }catch(\Exception $ex){
                    DB::rollback(); 
                    $msj = $mensaje. $this->ge_getMensajeError($ex,false);
                    // $msj = $mensaje;
                    return Redirect::to('gestion-de-subir-certificado/'.$idopcion)->with('errorurl', ' Fila:  '.$fila.' Error: '.$msj);
                }

                return Redirect::to('gestion-de-subir-certificado/'.$idopcion)->with('bienhecho', 'Cargaron '.$contador.' registros de '.$cant_reg);
               
            }else{
                return Redirect::to('gestion-de-subir-certificado/'.$idopcion)->with('errorurl', 'Seleccione Archivo Excel a Importar ');
            }

        }
    }
    



    public function actionDescargarFormatoCertificadoCargaExcel($idopcion) 
    {

        /******************* validar url **********************/
        $validarurl = $this->funciones->getUrl($idopcion,'Ver');
        if($validarurl <> 'true'){return $validarurl;}
        /******************************************************/
        // $periodo_actual =    $this->funciones->getPeriodoActual();
        $funcion                =   $this;
        $titulo                 =   'Formato Cargar Datos ';



        Excel::create($titulo, function($excel) use ( $funcion ) {
            $company = 'PERUGEL-CERTIFICADO';
            $excel->setTitle('Formato Cargar Datos')
                ->setCreator('SWAP')
                ->setCompany($company)
                ->setDescription('Excel Formato Cargar Datos');
            $excel->sheet('Formato Cargar Datos', function($sheet) use ($funcion) {

                $sheet->loadView('cardadata/excel/formatocargacertificado')
                        ->with('funcion',$funcion);

                $sheet->cells('A1:G1', function($cells) {
                   $cells->setFontColor('#000000');
                   $cells->setAlignment('center');
                   $cells->setValignment('center');
                    $cells->setFont(array(
                        'name'     => 'Tahoma',
                        'size'     => '9',
                        'bold'     =>  true
                    ));



                                    });
                $sheet->getStyle('A1:N1', $sheet->getHighestRow())->getAlignment()->setWrapText(true);

                $sheet->setWidth(array(
                    'A'     =>  '20',
                    'B'     =>  '20',
                    'C'     =>  '20',
                    'D'     =>  '20',
                    'E'     =>  '20',
                    'F'     =>  '25',
                    'G'     =>  '20'
                ));


                //format 
                $sheet->setColumnFormat(array(
                  'B' => '@'
                ));
                //set general font style
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Tahoma',
                        'size'      =>  10,
                        'bold'      =>  false
                    )
                ));


            });
        })->export('xls');
    }




    
}
