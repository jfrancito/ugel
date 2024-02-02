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

Route::group(['middleware' => ['authaw']], function () {

	Route::get('/bienvenido', 'UserController@actionBienvenido');
	//GESTION DE USUARIOS
	Route::any('/gestion-de-usuarios/{idopcion}', 'UserController@actionListarUsuarios');
	Route::any('/agregar-usuario/{idopcion}', 'UserController@actionAgregarUsuario');
	Route::any('/modificar-usuario/{idopcion}/{idusuario}', 'UserController@actionModificarUsuario');
	Route::any('/ajax-activar-perfiles', 'UserController@actionAjaxActivarPerfiles');
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


	Route::any('/gestion-apafa-conei/{idopcion}', 'GestionApafaConeiController@actionListarApafaConei');
	Route::any('/agregar-requerimiento-apafa-conei/{idopcion}', 'GestionApafaConeiController@actionAgregarApafaConei');
	Route::any('/ajax-buscar-dni-ugel', 'GestionApafaConeiController@actionBuscardni');





});

Route::get('/pruebaemail/{emailfrom}/{nombreusuario}', 'PruebasController@actionPruebaEmail');
