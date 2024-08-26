<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;


use App\Modelos\Whatsapp;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait WhatsappTraits
{

	public function insertar_whatsaap($numero,$nombre,$mensaje,$rutaimagen){

			$cabecera            	 	=	new Whatsapp;
			$cabecera->NumeroContacto 	=   $numero;
			$cabecera->NombreContacto 	=	$nombre;
			$cabecera->Mensaje  		=	$mensaje;
			$cabecera->IndArchivo   	=	0;
			$cabecera->RutaArchivo   	=	'';
			$cabecera->SizeArchivo   	=	0;
			$cabecera->IndProgramado   	=	0;
			$cabecera->IndManual   		=	0;
			$cabecera->IndEnvio  		=	0;
			$cabecera->FechaCreacion 	=  	date('d-m-Y H:i:s');
			$cabecera->activo 	 		= 	1;
			$cabecera->save();

	}

}