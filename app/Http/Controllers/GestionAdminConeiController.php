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
        $listaoic           =   OtroIntegranteConei::where('conei_id','=',$idconei)->orderby('representante_nombre','asc')->get();
        $larchivos          =   Archivo::where('referencia_id','=',$idconei)->where('tipo_archivo','=','requerimiento_conei')->get();
        $selectestado       =   $conei->estado_id; 
        $comboestado        =   $this->ge_combo_estado_cnei($conei->estado_id);
        $funcion            =   $this;


        return View::make('conei/gestiondetalleconei',
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



}
