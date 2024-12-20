<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/********************** USUARIOS *************************/
// header('Access-Control-Allow-Origin:  *');
// header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: *');

Route::group(['middleware' => ['guestaw']], function () {

	Route::any('/', 'UserController@actionLogin');
	Route::any('/login', 'UserController@actionLogin');
	Route::any('/acceso', 'UserController@actionAcceso');

});

Route::get('/cerrarsession', 'UserController@actionCerrarSesion');
Route::any('/registrate', 'UserController@actionRegistrate');
Route::any('/olvidaste-contrasenia', 'UserController@actionOlvidasteContrasenia');
Route::any('/cambio-clave/{institucion_id}', 'UserController@actionCambioClave');
Route::any('/cambio-clave-guardar/{institucion_cr}', 'UserController@actionCambioClaveGuardar');


Route::any('/ajax-buscar-proveedor', 'UserController@actionAjaxBuscarProveedor');
Route::any('/ajax-buscar-director', 'UserController@actionAjaxBuscarDirector');
Route::any('/ajax-buscar-dni-ugel-libre', 'GestionApafaConeiController@actionBuscardnilibre');
Route::any('/activar-registro/{token}', 'UserController@actionActivarRegistro');
Route::get('/prueba-buscar-dni/{dni}', 'GestionApafaConeiController@actionPruebaBuscardnilibre');


Route::group(['middleware' => ['authaw']], function () {

	Route::get('/bienvenido', 'UserController@actionBienvenido');
	//GESTION DE USUARIOS
	Route::any('/gestion-de-usuarios/{idopcion}', 'UserController@actionListarUsuarios');
	Route::any('/agregar-usuario/{idopcion}', 'UserController@actionAgregarUsuario');
	Route::any('/modificar-usuario/{idopcion}/{idusuario}', 'UserController@actionModificarUsuario');
	Route::any('/ajax-activar-perfiles', 'UserController@actionAjaxActivarPerfiles');
	Route::any('/gestion-de-director-ie/{idopcion}', 'UserController@actionListarIE');




	//GESTION DE SOLICITUD
	Route::any('/gestion-solicitud/{idopcion}', 'UserController@actionListarSolicitud');
	Route::any('/descargar-archivo-resolucion/{idregistro}', 'UserController@actionDescargarArchivosResulucion');
	Route::any('/enviar-correo-solicitud/{idopcion}/{idregistro}', 'UserController@actionEnviarSolicitudCorreo');



	//GESTION DE ROLES
	Route::any('/gestion-de-roles/{idopcion}', 'UserController@actionListarRoles');
	Route::any('/agregar-rol/{idopcion}', 'UserController@actionAgregarRol');
	Route::any('/modificar-rol/{idopcion}/{idrol}', 'UserController@actionModificarRol');
	//GESTION DE PERMISOS
	Route::any('/gestion-de-permisos/{idopcion}', 'UserController@actionListarPermisos');
	Route::any('/ajax-listado-de-opciones', 'UserController@actionAjaxListarOpciones');
	Route::any('/ajax-activar-permisos', 'UserController@actionAjaxActivarPermisos');
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
 	// SECCION DE GRUPO OPCIONES
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	Route::any('/gestion-grupo-opciones/{idopcion}', 'GestionMenuController@actionListarGrupoOpciones');
	Route::any('/agregar-grupo-opcion/{idopcion}', 'GestionMenuController@actionAgregarGrupoOpcion');
	Route::any('/modificar-grupo-opcion/{idopcion}/{idregistro}', 'GestionMenuController@actionModificarGrupoOpcion');
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
 	// SECCION DE OPCIONES
	///////////////////////////////////////////////////////////////////////////////////////////////////////////
	Route::any('/gestion-opciones/{idopcion}', 'GestionMenuController@actionListarOpciones');
	Route::any('/agregar-opcion/{idopcion}', 'GestionMenuController@actionAgregarOpcion');
	Route::any('/modificar-opcion/{idopcion}/{idregistro}', 'GestionMenuController@actionModificarOpcion');


	//Route::any('/gestion-apafa/{idopcion}', 'GestionApafaConeiController@actionListarApafa');
	//Route::any('/agregar-requerimiento-apafa/{idopcion}', 'GestionApafaConeiController@actionAgregarApafa');
	Route::any('/ajax-buscar-dni-ugel', 'GestionApafaConeiController@actionBuscardni');

	Route::any('/buscar-dni-01/{dni}', 'GestionApafaConeiController@actionBuscardni01');
	Route::any('/buscar-dni-02/{dni}', 'GestionApafaConeiController@actionBuscardni02');
	Route::any('/buscar-dni-03/{dni}', 'GestionApafaConeiController@actionBuscardni03');


	//CONEI
	Route::any('/gestion-conei/{idopcion}', 'GestionConeiController@actionListarConei');
	Route::any('/agregar-requerimiento-conei/{idopcion}', 'GestionConeiController@actionAgregarConei');
	Route::any('/ajax-modal-registro', 'GestionConeiController@actionModalRegistro');
	Route::any('/ajax-modal-registro-oi', 'GestionConeiController@actionModalRegistroOI');
	Route::any('/ajax-lista-tabla-oi', 'GestionConeiController@actionListaTablaOI');
	Route::any('/ajax-elminar-fila-tabla-oi', 'GestionConeiController@actionEliminarFilaTablaOI');
	Route::any('/ajax-modal-confirmar-registro', 'GestionConeiController@actionModalConfirmarRegistro');
	Route::any('/detalle-conei/{idopcion}/{idconei}', 'GestionConeiController@actionDetalleConei');
	Route::any('/descargar-archivo-requerimiento/{idopcion}/{idrequerimiento}/{idarchivo}', 'GestionConeiController@actionDescargarArchivosRequerimiento');
	Route::any('/ajax-modal-editar-director', 'GestionConeiController@actionModalEditarDirector');
	Route::any('/ajax-guardar-registro-director', 'GestionConeiController@actionModalGuardarRegistroDirector');
	Route::any('/ajax-guardar-registro-director-nuevo', 'GestionConeiController@actionModalGuardarRegistroDirectorNuevo');

	//APAFA
	Route::any('/gestion-apafa/{idopcion}', 'GestionApafaController@actionListarApafa');
	Route::any('/agregar-requerimiento-apafa/{idopcion}', 'GestionApafaController@actionAgregarApafa');
	Route::any('/ajax-modal-registro-oi-apafa', 'GestionApafaController@actionModalRegistroOIApafa');
	Route::any('/ajax-lista-tabla-oi-apafa', 'GestionApafaController@actionListaTablaOIApafa');
	Route::any('/ajax-elminar-fila-tabla-oi-apafa', 'GestionApafaController@actionEliminarFilaTablaOIApafa');
	Route::any('/ajax-modal-confirmar-registro-apafa', 'GestionApafaController@actionModalConfirmarRegistroApafa');

	Route::any('/ajax-modal-registro-oi-apafa-vi', 'GestionApafaController@actionModalRegistroOIApafaVi');
	Route::any('/ajax-lista-tabla-oi-apafa-vi', 'GestionApafaController@actionListaTablaOIApafaVi');
	Route::any('/ajax-elminar-fila-tabla-oi-apafa-vi', 'GestionApafaController@actionEliminarFilaTablaOIApafaVi');


	//gestion administrativo conei
	Route::any('/gestion-admin-conei/{idopcion}', 'GestionAdminConeiController@actionListarConei');
	Route::any('/gestion-detalle-conei/{idopcion}/{idconei}', 'GestionAdminConeiController@actionGestionDetalleConei');
	Route::any('/gestion-admin-conei-estado/{idopcion}/{idconei}', 'GestionAdminConeiController@actionGestionConeiEstado');
	Route::any('/gestion-observacion-conei/{idopcion}/{idconei}', 'GestionAdminConeiController@actionModificarConei');
	Route::any('/descargar-conei-folio/{idopcion}/{idconei}', 'GestionAdminConeiController@actionDescargarFolioConei');
	Route::any('/unir-pdf', 'ReporteCertificadoController@actionUnirpdf');


	//gestion administrativo apafa
	Route::any('/gestion-admin-apafa/{idopcion}', 'GestionAdminApafaController@actionListarApafa');
	Route::any('/gestion-detalle-apafa/{idopcion}/{idconei}', 'GestionAdminApafaController@actionGestionDetalleApafa');
	Route::any('/detalle-apafa/{idopcion}/{idconei}', 'GestionAdminApafaController@actionDetalleApafa');
	Route::any('/gestion-admin-apafa-estado/{idopcion}/{idconei}', 'GestionAdminApafaController@actionGestionApafaEstado');
	Route::any('/gestion-observacion-apafa/{idopcion}/{idconei}', 'GestionAdminApafaController@actionModificarApafa');
	Route::any('/descargar-apafa-folio/{idopcion}/{idconei}', 'GestionAdminApafaController@actionDescargarFolioApafa');


	Route::get('/serve-file', 'FileController@serveFile')->name('serve-file');

	/* SUBIR DOCENTE Y INTITUCION */
	Route::any('/gestion-de-institucion-docente/{idopcion}', 'CargarDatosInstitucionController@actionCargarDatos');
	Route::any('/formato-excel-cargar-datos-institucion-docente/{idopcion}', 'CargarDatosInstitucionController@actionDescargarFormatoCargaExcel');
	Route::any('/subir-excel-cargar-datos/{idopcion}', 'CargarDatosInstitucionController@actionCargarDato');
	// Route::any('/formato-excel-produccion-cargar-datos-produccion/{idopcion}', 'CargarDatosProduccionController@actionDescargarFormatoProduccionExcel');
	// Route::any('/formato-excel-departamentos-cargar-datos-produccion/{idopcion}', 'CargarDatosProduccionController@actionDescargarFormatoDepartamentosExcel');


	/* SUBIR CERTIFICADO MASIVO */
	Route::any('/gestion-de-subir-certificado/{idopcion}', 'CargarDatosCertificadoController@actionCargarDatos');
	Route::any('/formato-excel-cargar-datos-certificado/{idopcion}', 'CargarDatosCertificadoController@actionDescargarFormatoCertificadoCargaExcel');
	Route::any('/subir-excel-cargar-datos-certificado/{idopcion}', 'CargarDatosCertificadoController@actionCargarDatoCertificado');


	/* SUBIR CERTIFICADOS */
	Route::any('/gestion-de-registro-certificado/{idopcion}', 'GestionCertificadoController@actionListarCertificados');
	Route::any('/agregar-certificado/{idopcion}', 'GestionCertificadoController@actionAgregarCertificado');
	Route::any('/ajax-combo-periodo-xinstitucion', 'GestionCertificadoController@actionAjaxComboPeriodoxInstitucion');
	Route::any('/descargar-archivo-certificado/{idcertificado}/{idarchivo}', 'GestionCertificadoController@actionDescargarArchivosCertificado');
	Route::any('/modificar-certificado/{idopcion}/{idcertificado}', 'GestionCertificadoController@actionModificarCertificado');
	Route::any('/ajax-modal-periodo-xinstitucion-xprocedencia', 'GestionCertificadoController@actionAjaxListarPeriodos');
	Route::any('/ajax-periodo-fin-certificado', 'GestionCertificadoController@actionAjaxListarPeriodoFin');

	Route::any('/gestion-de-instituciones-certificado/{idopcion}', 'ReporteCertificadoController@actionListarCertificadosInstituciones');
	Route::any('/ajax-lista-instituciones-certificado', 'ReporteCertificadoController@actionAjaxListarInstitucionCertificado');

	Route::any('/gestion-de-instituciones-sin-certificado/{idopcion}', 'ReporteCertificadoController@actionListarSinCertificadosInstituciones');
	Route::any('/ajax-lista-instituciones-sin-certificado', 'ReporteCertificadoController@actionAjaxListarInstitucionSinCertificado');

	/* GESTION INGRESOS */
	Route::any('/gestion-de-ingresos/{idopcion}', 'GestionIngresoController@actionListarIngresos');	
	Route::any('/agregar-ingreso/{idopcion}', 'GestionIngresoController@actionAgregarIngreso');
	Route::any('/modificar-ingreso/{idopcion}/{idingreso}', 'GestionIngresoController@actionModificarIngreso');
	Route::any('/descargar-archivo-ingreso/{idopcion}/{idarchivo}', 'GestionIngresoController@actionDescargarArchivosIngreso');
	Route::any('/descargar-archivo-contrato/{idopcion}/{idarchivo}', 'GestionIngresoController@actionDescargarArchivosContrato');
	Route::any('/emitir-ingreso/{idopcion}/{idingreso}', 'GestionIngresoController@actionEmitirIngreso');
	Route::any('/ajax-cargar-trimestre-ingreso', 'GestionIngresoController@actionAjaxTrimestre');
	Route::any('/ajax-comparar-serie-numero', 'GestionIngresoController@actionCompararSerieNumero');
	Route::any('/ajax-modal-contrato-anterior', 'GestionIngresoController@actionAjaxListarContratosAnteriores');


	/* GESTION EGRESOS */
	Route::any('/gestion-de-egresos/{idopcion}', 'GestionEgresoController@actionListarEgresos');	
	Route::any('/agregar-egreso/{idopcion}', 'GestionEgresoController@actionAgregarEgreso');
	Route::any('/ajax-cargar-tipo-concepto', 'GestionEgresoController@actionAjaxTipoConcepto');
	Route::any('/modificar-egreso/{idopcion}/{idegreso}', 'GestionEgresoController@actionModificarEgreso');
	Route::any('/descargar-archivo-egreso/{idopcion}/{idarchivo}', 'GestionEgresoController@actionDescargarArchivosEgreso');
	Route::any('/emitir-egreso/{idopcion}/{idegreso}', 'GestionEgresoController@actionEmitirEgreso');
	Route::any('/ajax-cargar-trimestre-egreso', 'GestionEgresoController@actionAjaxTrimestre');
	Route::any('/ajax-buscar-ruc-ugel', 'GestionEgresoController@actionBuscarRuc');





	Route::get('/descargar-pdf/{filename}', function ($filename) {
	    $path = storage_path('app/plantillas/' . $filename);

	    if (!file_exists($path)) {
	        abort(404);
	    }

	    return Response::download($path);
	})->name('descargar.pdf');




});

Route::get('/pruebaemail/{emailfrom}/{nombreusuario}', 'PruebasController@actionPruebaEmail');
