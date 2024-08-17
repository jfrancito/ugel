<?php

namespace App\Http\Controllers;

use App\Modelos\Grupoopcion;
use App\Modelos\Opcion;
use App\Modelos\Rol;
use App\Modelos\RolOpcion;
use App\Modelos\Institucion;
use App\Modelos\Director;
use App\Modelos\Certificado;
use App\Modelos\DetalleCertificado;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use View;
use Stdclass;

class UserController extends Controller {



    public function actionAcceso()
	{

		$accesos  	= 	Permisouserempresa::where('activo','=',1)
						->where('user_id','=',Session::get('usuario')->id)->get();


		return View::make('acceso',
						 [
						 	'accesos' => $accesos,
						 ]);
	}
	
	public function actionLogin(Request $request) {

		if ($_POST) {
			/**** Validaciones laravel ****/
			$this->validate($request, [
				'name' => 'required',
				'password' => 'required',

			], [
				'name.required' => 'El campo Usuario es obligatorio',
				'password.required' => 'El campo Clave es obligatorio',
			]);

			/**********************************************************/

			$usuario = strtoupper($request['name']);
			$clave = strtoupper($request['password']);
			$local_id = $request['local_id'];

			$tusuario = User::whereRaw('UPPER(name)=?', [$usuario])
				->where('activo', '=', 1)
				->first();

			if (count($tusuario) > 0) {

				$clavedesifrada = strtoupper(Crypt::decrypt($tusuario->password));

				if ($clavedesifrada == $clave) {

					$listamenu = Grupoopcion::join('opciones', 'opciones.grupoopcion_id', '=', 'grupoopciones.id')
						->join('rolopciones', 'rolopciones.opcion_id', '=', 'opciones.id')
						->where('grupoopciones.activo', '=', 1)
						->where('rolopciones.rol_id', '=', $tusuario->rol_id)
						->where('rolopciones.ver', '=', 1)
						->groupBy('grupoopciones.id')
						->groupBy('grupoopciones.nombre')
						->groupBy('grupoopciones.icono')
						->groupBy('grupoopciones.orden')
						->select('grupoopciones.id', 'grupoopciones.nombre', 'grupoopciones.icono', 'grupoopciones.orden')
						->orderBy('grupoopciones.orden', 'asc')
						->get();

					$listaopciones = RolOpcion::where('rol_id', '=', $tusuario->rol_id)
						->where('ver', '=', 1)
						->orderBy('orden', 'asc')
						->pluck('opcion_id')
						->toArray();



					$tinstitucion 	= 	Institucion::where('id','=',$tusuario->institucion_id)
										->where('activo', '=', 1)
										->first();
					$tdireccion  	= 	Director::where('id','=',$tusuario->institucion_id)
										->where('activo', '=', 1)
										->first();



					Session::put('usuario', $tusuario);
					Session::put('listamenu', $listamenu);
					Session::put('listaopciones', $listaopciones);
					Session::put('institucion', $tinstitucion);
					Session::put('direccion', $tdireccion);


					return Redirect::to('bienvenido');

				} else {
					return Redirect::back()->withInput()->with('errorbd', 'Usuario o clave incorrecto');
				}
			} else {
				return Redirect::back()->withInput()->with('errorbd', 'Usuario o clave incorrecto');
			}

		} else {
			return view('usuario.login');
		}
	}

	public function actionCerrarSesion() {
		Session::forget('usuario');
		Session::forget('listamenu');
		Session::forget('listaopciones');
		Session::forget('institucion');
		Session::forget('direccion');
		return Redirect::to('/login');	

	}

	public function actionBienvenido() {
		View::share('titulo','Bienvenido Sistema Administrativo');
		

		$anio 				=	$this->anio;


		//CONEI
		$conei_periodo 		= 	$anio;
		$conei_estado 		= 	'SIN CONEI';
		//CONEI
		$detallecertificado = 	DetalleCertificado::where('periodo_nombre','=',$anio)
								->where('procedente_id','=','APCN00000002')
								->whereIn('estado_id',['CEES00000001','CEES00000007','CEES00000005','CEES00000008'])
								
								->where('institucion_id','=',Session::get('institucion')->id)
								->orderBy('estado_id','asc')

								->first();

		if(count($detallecertificado)>0){
			$certificado 		= 	Certificado::where('id','=',$detallecertificado->certificado_id)->first();
			$conei_periodo 		= 	$certificado->periodo_nombre;
			$conei_estado 		= 	$certificado->estado_nombre;
		}

		//APAFA
		$apafa_periodo 		= 	$anio;
		$apafa_estado 		= 	'SIN APAFA';
		//CONEI
		$detallecertificado = 	DetalleCertificado::where('periodo_nombre','=',$anio)
								->where('procedente_id','=','APCN00000001')
								->whereIn('estado_id',['CEES00000001','CEES00000007','CEES00000005','CEES00000008'])
								->where('institucion_id','=',Session::get('institucion')->id)
								->orderBy('estado_id','asc')
								->first();


		if(count($detallecertificado)>0){
			$certificado 		= 	Certificado::where('id','=',$detallecertificado->certificado_id)->first();
			$apafa_periodo 		= 	$certificado->periodo_nombre;
			$apafa_estado 		= 	$certificado->estado_nombre;
		}


		$fecha = date('Y-m-d');
		return View::make('bienvenido',
			[
				'conei_periodo' => $conei_periodo,
				'conei_estado'  => $conei_estado,
				'apafa_periodo' => $apafa_periodo,
				'apafa_estado'  => $apafa_estado,
				
			]);
	}

	public function actionObtenerTipoCambio()
	{

		$fecha   = date_format(date_create(date('Y-m-d')), 'Y-m-d');
        // URL del servicio web de SUNAT para obtener el tipo de cambio
        $url = 'https://www.sunat.gob.pe/a/txt/tipoCambio.txt';
        // Realizar la solicitud HTTP para obtener el contenido del archivo de tipo de cambio
        $response = file_get_contents($url);
        // Verificar si la solicitud fue exitosa
        if ($response !== false) {
            // Dividir el contenido en líneas
            $datos = explode('|',$response);
            // dd('sss');
            $tipocambio           =   new TipoCambio();
            $tipocambio->compra   =   (float)$datos[1];
            $tipocambio->venta    =   (float)$datos[2];
            $tipocambio->fecha    =   $fecha;
            $tipocambio->save();

        }
        else{
            $registro               =   new Ilog();
            $registro->descripcion  =   'NO SE REGISTRO TIPO DE CAMBIO PARA LA FECHA '.date('Y-m-d');
            $registro->save();
        }
		return Redirect::to('bienvenido');
	}

	public function actionListarUsuarios($idopcion) {
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Ver');

		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/

	    View::share('titulo','Lista de usuarios');
		
		$listausuarios = 	User::join('instituciones','instituciones.id','=','users.institucion_id')
							->join('directores','directores.institucion_id','=','instituciones.id')
							->select('users.*','directores.nombres')
							->where('users.id', '<>', $this->prefijomaestro . '00000001')->orderBy('users.id', 'asc')->get();

		return View::make('usuario/listausuarios',
			[
				'listausuarios' => $listausuarios,
				'idopcion' => $idopcion,
			]);
	}

	public function actionAgregarUsuario($idopcion, Request $request) {
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Anadir');
		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/
		View::share('titulo','Agregar Usuario');
		if ($_POST) {

			/**** Validaciones laravel ****/
			$this->validate($request, [
	            'name' => 'unique:users',
			], [
            	'name.unique' => 'Usuario ya registrado',
        	]);
			/******************************/



			$idusers = $this->funciones->getCreateIdMaestra('users');

			$cabecera = new User;
			$cabecera->id = $idusers;
			$cabecera->nombre = $request['nombre'];
			$cabecera->name = $request['name'];
			$cabecera->password = Crypt::encrypt($request['password']);
			$cabecera->rol_id = $request['rol_id'];
            $cabecera->institucion_id  =   '1CIX00000001';
			$cabecera->fecha_crea = $this->fechaactual;
			$cabecera->usuario_crea = Session::get('usuario')->id;
			$cabecera->save();


			return Redirect::to('/gestion-de-usuarios/' . $idopcion)->with('bienhecho', 'Usuario ' . $request['nombre'] . ' registrado con exito');

		} else {

			$rol = DB::table('Rols')->where('id', '<>', $this->prefijomaestro . '00000001')->pluck('nombre', 'id')->toArray();
			$comborol = array('' => "Seleccione Rol") + $rol;

			return View::make('usuario/agregarusuario',
				[
					'comborol' => $comborol,
					'idopcion' => $idopcion,
				]);
		}
	}

	public function actionModificarUsuario($idopcion, $idusuario, Request $request) {

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Modificar');
		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/
		$idusuario = $this->funciones->decodificarmaestra($idusuario);
	    View::share('titulo','Modificar Usuario');
		if ($_POST) {

			$cabecera = User::find($idusuario);
			$cabecera->nombre = $request['nombre'];
			$cabecera->name = $request['name'];
			$cabecera->fecha_mod = $this->fechaactual;
			$cabecera->usuario_mod = Session::get('usuario')->id;
			$cabecera->password = Crypt::encrypt($request['password']);
			$cabecera->activo = $request['activo'];
			$cabecera->rol_id = $request['rol_id'];
			$cabecera->save();

			return Redirect::to('/gestion-de-usuarios/' . $idopcion)->with('bienhecho', 'Usuario ' . $request['nombre'] . ' modificado con exito');

		} else {

			$usuario = User::where('id', $idusuario)->first();
			$rol = DB::table('Rols')->where('id', '<>', $this->prefijomaestro . '00000001')->pluck('nombre', 'id')->toArray();


			$comborol = array($usuario->rol_id => $usuario->rol->nombre) + $rol;
			$funcion = $this;

			return View::make('usuario/modificarusuario',
				[
					'usuario' => $usuario,
					'comborol' => $comborol,
					'idopcion' => $idopcion,
					'funcion' => $funcion,
				]);
		}
	}

	public function actionListarRoles($idopcion) {

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Ver');
		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/
	    View::share('titulo','Lista de Roles');
		$listaroles = Rol::where('id', '<>', $this->prefijomaestro . '00000001')->orderBy('id', 'asc')->get();

		return View::make('usuario/listaroles',
			[
				'listaroles' => $listaroles,
				'idopcion' => $idopcion,
			]);

	}

	public function actionAgregarRol($idopcion, Request $request) {
		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Anadir');
		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/
	    View::share('titulo','Agregar Rol');
		if ($_POST) {

			/**** Validaciones laravel ****/

			$this->validate($request, [
				'nombre' => 'unico:dbo,rols',
			], [
				'nombre.unico' => 'Rol ya registrado',
			]);

			/******************************/
			$idrol = $this->funciones->getCreateIdMaestra('rols');

			$cabecera = new Rol;
			$cabecera->id = $idrol;
			$cabecera->fecha_crea = $this->fechaactual;
			$cabecera->usuario_crea = Session::get('usuario')->id;
			$cabecera->nombre = $request['nombre'];
			$cabecera->save();

			$listaopcion = Opcion::orderBy('id', 'asc')->get();
			$count = 1;
			foreach ($listaopcion as $item) {

				$idrolopciones = $this->funciones->getCreateIdMaestra('rolopciones');

				$detalle = new RolOpcion;
				$detalle->id = $idrolopciones;
				$detalle->opcion_id = $item->id;
				$detalle->fecha_crea = $this->fechaactual;
				$detalle->rol_id = $idrol;
				$detalle->orden = $count;
				$detalle->ver = 0;
				$detalle->anadir = 0;
				$detalle->modificar = 0;
				$detalle->eliminar = 0;
				$detalle->todas = 0;
				$detalle->fecha_crea = $this->fechaactual;
				$detalle->usuario_crea = Session::get('usuario')->id;
				$detalle->save();
				$count = $count + 1;
			}

			return Redirect::to('/gestion-de-roles/' . $idopcion)->with('bienhecho', 'Rol ' . $request['nombre'] . ' registrado con exito');
		} else {

			return View::make('usuario/agregarrol',
				[
					'idopcion' => $idopcion,
				]);

		}
	}

	public function actionModificarRol($idopcion, $idrol, Request $request) {

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Modificar');
		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/
		$idrol = $this->funciones->decodificarmaestra($idrol);
	    View::share('titulo','Modificar Rol');
		if ($_POST) {

			/**** Validaciones laravel ****/
			$this->validate($request, [
				'nombre' => 'unico_menos:dbo,rols,id,' . $idrol,
			], [
				'nombre.unico_menos' => 'Rol ya registrado',
			]);
			/******************************/

			$cabecera = Rol::find($idrol);
			$cabecera->nombre = $request['nombre'];
			$cabecera->fecha_mod = $this->fechaactual;
			$cabecera->usuario_mod = Session::get('usuario')->id;
			$cabecera->activo = $request['activo'];
			$cabecera->save();

			return Redirect::to('/gestion-de-roles/' . $idopcion)->with('bienhecho', 'Rol ' . $request['nombre'] . ' modificado con éxito');

		} else {
			$rol = Rol::where('id', $idrol)->first();

			return View::make('usuario/modificarrol',
				[
					'rol' => $rol,
					'idopcion' => $idopcion,
				]);
		}
	}

	public function actionListarPermisos($idopcion) {

		/******************* validar url **********************/
		$validarurl = $this->funciones->getUrl($idopcion, 'Ver');
		if ($validarurl != 'true') {return $validarurl;}
		/******************************************************/
	     View::share('titulo','Lista Permisos');
		$listaroles = Rol::where('id', '<>', $this->prefijomaestro . '00000001')->orderBy('id', 'asc')->get();

		return View::make('usuario/listapermisos',
			[
				'listaroles' => $listaroles,
				'idopcion' => $idopcion,
			]);
	}

	public function actionAjaxListarOpciones(Request $request) {
		$idrol = $request['idrol'];
		$idrol = $this->funciones->decodificarmaestra($idrol);

		$listaopciones = RolOpcion::where('rol_id', '=', $idrol)->get();

		return View::make('usuario/ajax/listaopciones',
			[
				'listaopciones' => $listaopciones,
			]);
	}

	public function actionAjaxActivarPermisos(Request $request) {

		$idrolopcion = $request['idrolopcion'];
		$idrolopcion = $this->funciones->decodificarmaestra($idrolopcion);

		$cabecera = RolOpcion::find($idrolopcion);
		$cabecera->ver = $request['ver'];
		$cabecera->anadir = $request['anadir'];
		$cabecera->fecha_mod = $this->fechaactual;
		$cabecera->usuario_mod = Session::get('usuario')->id;
		$cabecera->modificar = $request['modificar'];
		$cabecera->todas = $request['todas'];
		$cabecera->save();

		echo ("gmail");

	}

}
