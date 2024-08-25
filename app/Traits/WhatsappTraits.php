<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;


use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use PDO;

trait WhatsappTraits
{

	public function insertar_whatsaap($numero,$nombre,$mensaje,$rutaimagen){

		DB::connection('sqlsrv_osiris')->table('whatsapp')->insert([
		    'numero_contacto' => $numero,
		    'nombre_contacto' => $nombre,
		    'mensaje' => $mensaje,
		    'ruta_imagen' => $rutaimagen,
		    'ind_envio' => 0,
		    'nombre_proyecto' => 'OSIRIS',
		    'fecha_crea' => date('d-m-Y H:i:s'),
		    'activo' => 1,
		]);



	}

}