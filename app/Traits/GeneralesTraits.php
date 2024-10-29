<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use App\Modelos\WEBCuentaContable;
use App\Modelos\ALMProducto;
use App\Modelos\Categoria;
use App\Modelos\Estado;
use App\Modelos\Conei;
use App\Modelos\Registro;

use App\Modelos\Requerimiento;
use App\Modelos\Archivo;

use View;
use Session;
use Hashids;
Use Nexmo;
use Keygen;
use Mail;

trait GeneralesTraits
{

	private function envio_correo_sin_editar($listasolicitudes,$url_real) {
        foreach($listasolicitudes as $item){
            $token                  =   substr($item->institucion_id, -8);
            $emailfrom          	=   'alertassys@induamerica.com.pe';
            // correos principales y  copias
            $email              	=   $item->correo_director;
            $url                	=   $url_real."activar-registro/".Hashids::encode($token);
            $array      =  Array(
                'PR'                =>  $item,
                'token'             =>  $token,
                'url'               =>  $url,
            );
            Mail::send('emails.confirmacion', $array, function($message) use ($emailfrom,$email)
            {
                $message->from($emailfrom, 'PERUGEL SOLICITUD REGISTRO');
                $message->to($email);
                $message->subject('CONFIRMACION DE REGISTRO');

            });

            $item->ind_email       	=   1;
            $item->save();
        }
        print_r("Se envio correctamente el correo CONFIRMACION");
	}

	private function envio_correo_registro($item,$url_real) {

            $token                  =   substr($item->institucion_id, -8);
            $emailfrom          	=   'alertassys@induamerica.com.pe';
            // correos principales y  copias
            $email              	=   $item->correo_director;
            $url                	=   $url_real."activar-registro/".Hashids::encode($token);
            $array      =  Array(
                'PR'                =>  $item,
                'token'             =>  $token,
                'url'               =>  $url,
            );
            Mail::send('emails.confirmacion', $array, function($message) use ($emailfrom,$email)
            {
                $message->from($emailfrom, 'PERUGEL SOLICITUD REGISTRO');
                $message->to($email);
                $message->subject('CONFIRMACION DE REGISTRO');

            });
	}


	private function envio_correo_cambio_clave($director,$url_real) {

            $token                  =   substr($director->institucion_id, -8);
            $emailfrom          	=   'alertassys@induamerica.com.pe';
            $email              	=   $director->correo;
            $url                	=   $url_real."cambio-clave/".Hashids::encode($token);
            $array      =  Array(
                'PR'                =>  $director,
                'token'             =>  $token,
                'url'               =>  $url,
            );
            Mail::send('emails.cambioclave', $array, function($message) use ($emailfrom,$email)
            {
                $message->from($emailfrom, 'PERUGEL SOLICITUD CAMBIO DE CLAVE');
                $message->to($email);
                $message->subject('CAMBIO DE CLAVE');

            });
	}



	private function envio_correo_confirmacion() {
        $listasolicitudes           =   Registro::where('ind_email','=',0)
        								->where('accion','=','REGISTRO')
                                        ->get();
        foreach($listasolicitudes as $item){

            $token                  =   substr($item->institucion_id, -8);
            $emailfrom          	=   'alertassys@induamerica.com.pe';
            // correos principales y  copias
            $email              	=   $item->email;
            $url                	=   "https://merge.grupoinduamerica.com/perugel/activar-registro/".Hashids::encode($token);
            $array      =  Array(
                'PR'                =>  $item,
                'token'             =>  $token,
                'url'               =>  $url,
            );
            Mail::send('emails.confirmacion', $array, function($message) use ($emailfrom,$email)
            {
                $message->from($emailfrom, 'PERUGEL REGISTRO INSTITUCION');
                $message->to($email);
                $message->subject('Confirmación de registro');

            });

            $item->ind_email       	=   1;
            $item->save();
        }
        print_r("Se envio correctamente el correo CONFIRMACION");
	}



	public function ge_combo_estado_cnei($estado_id)
	{

		$array_estado       =   array();
        if($estado_id == 'CEES00000005'){
            $array_estado       =   array('CEES00000008','CEES00000006');
            $comboestado        =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione estado','','CERTIFICADO_ESTADO',$array_estado);
        }else{
            if($estado_id == 'CEES00000006'){
                $array_estado       =   array('CEES00000001');
                $comboestado        =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione estado','','CERTIFICADO_ESTADO',$array_estado);
            }else{
                $comboestado        =   $this->gn_generacion_combo_tabla_in_array('estados','id','nombre','Seleccione estado','','CERTIFICADO_ESTADO',$array_estado);
            }
        }

		return $comboestado;
	}


	public function ge_validarArchivoDuplicado($nombrearchivo,$registrodestino_id)
	{
		$valor = true;
		$larchivos = Archivo::where('referencia_id','=',$registrodestino_id)->where('activo',1)->get();
		foreach ($larchivos as $key => $archivo) {
			if($nombrearchivo==$archivo->nombre_archivo){
				$valor=false;
				break;
			}
		}
		return $valor;
	}

	public function getIdEstado($descripcion)
	{
		$id = ($descripcion!=='') ? Categoria::where('tipo_categoria','ESTADO_GENERAL')->where('descripcion',$descripcion)->first()->id:'';
		return $id;
	}
	public function getIdTipoMovimiento($descripcion)
	{
		$id = ($descripcion!=='') ? Categoria::where('tipo_categoria','TIPO_MOVIMIENTO')->where('descripcion',$descripcion)->first()->id:'';
		return $id;
	}
	public function getIdCompraVenta($descripcion)
	{
		$id = ($descripcion!=='') ? Categoria::where('tipo_categoria','COMPRAVENTA')->where('descripcion',$descripcion)->first()->id:'';
		return $id;
	}
	public function getIdMotivoDocumento($descripcion)
	{
		$id = ($descripcion!=='') ? Categoria::where('tipo_categoria','MOTIVO_DOCUMENTO')->where('descripcion',$descripcion)->first()->id:'';
		return $id;
	}
	public function getIdTipoCompra($descripcion)
	{
		$id = ($descripcion!=='') ? Categoria::where('tipo_categoria','TIPO_COMPRA')->where('descripcion',$descripcion)->first()->id:'';
		return $id;
	}	


	public function ge_validarSizeArchivos($files,$arr_archivos,$lote,$limite,$unidad)
	{
		$sw 			= 	true;
		$sizerestante 	=	0;
		$sizefileslote  = 	(float)DB::table('archivos')
								->where('activo','=',1)
								->where('lote','=',$lote)
								->sum('size'); ///en bytes  //1024^2 para ser megas
		$sizefiles = 0;
		foreach($files as $file){
			$nombreoriginal 			= $file->getClientOriginalName();
			if(in_array($nombreoriginal,$arr_archivos)){
				$sizefiles = $sizefiles + filesize($file);
			}
		}

		if($limite>=($sizefileslote + $sizefiles))
		{
			//no supera el limite
			$sw=false;
			$sizerestante = $limite -  ($sizefileslote + $sizefiles);
		}
		// $sizeusado = $sizefiles + $sizefileslote;
		$sizeusado = $sizefileslote;

		$sizefiles 		= round(($sizefiles/pow(1024,$unidad)),2);
		$sizeusado 		= round(($sizeusado/pow(1024,$unidad)),2);
		$sizerestante 	= round(($sizerestante/pow(1024,$unidad)),2);
		$sizefileslote 	= round(($sizefileslote/pow(1024,$unidad)),2);
		$limitesize 	= round(($limite/pow(1024,$unidad)),2);
		 
		// dd(compact('sw','sizefiles','sizefileslote','sizeusado','sizerestante','limitesize'));
		return compact('sw','sizefiles','sizefileslote','sizeusado','sizerestante','limitesize');
	}
    public function ge_isUsuarioAdmin()
    {
        $valor=false;
        if(Session::get('usuario')->id=='1CIX00000001'){
            $valor=true;
        }
        return $valor;
    }

    public function mostrarValor($dato)
    {
        if($this->ge_isUsuarioAdmin()){
            dd($dato);
        }
    }
	public function ge_getMensajeError($error,$sw=true)
	{
		$mensaje = ($sw==true)?'Ocurrio un error Inesperado':'';
        if($this->ge_isUsuarioAdmin()){
            if(isset($error)){
                $mensaje=$mensaje.': '.$error;
            }
        }
        return $mensaje;
	}

	public function ge_crearCarpetaSiNoExiste($ruta){
		$valor = false;
		
		if (!file_exists($ruta)) {
		    mkdir($ruta, 0777, true);
		    $valor=true;
		}
		return $valor;
	}

	private function gn_combo_departamentos()
	{
		$datos =	[];
		$datos = 	DB::table('departamentos')
						->where('activo',1)
						->pluck('descripcion','id')
						->toArray();
		return [''=>'SELECCIONE DEPARTAMENTO']+$datos;
	}



	private function gn_generacion_combo_niveles($codigo) {
		
		$array 							= 	DB::table('detalleinstituciones')
        									->where('activo','=',1)
        									->where('codigo','=',$codigo)
        									->orderby('codigo','desc')
		        							->pluck('nivel','codigomodular')
											->toArray();
	 	return  $array;					 			
	}


	private function gn_generacion_combo_tabla($tabla,$atributo1,$atributo2,$titulo,$todo,$tipoestado) {
		
		$array 							= 	DB::table($tabla)
        									->where('activo','=',1)
        									->where('tipoestado','=',$tipoestado)
        									->orderby('codigo','asc')
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}


	private function gn_generacion_combo_tabla_in_array($tabla,$atributo1,$atributo2,$titulo,$todo,$tipoestado,$array) {
		
		$array 							= 	DB::table($tabla)
        									->where('activo','=',1)
        									->whereIn('id',$array)
        									->where('tipoestado','=',$tipoestado)
        									->orderby('codigo','desc')
		        							->pluck($atributo2,$atributo1)
											->toArray();


		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}
	 	return  $combo;					 			
	}



	private function gn_generacion_combo_tabla_not_array($tabla,$atributo1,$atributo2,$titulo,$todo,$tipoestado,$array) {
		
		$array 							= 	DB::table($tabla)
        									->where('activo','=',1)
        									->whereNotIn('id',$array)
        									->where('tipoestado','=',$tipoestado)
        									->orderby('codigo','asc')
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}



	private function gn_generacion_combo_institucion_distrito($titulo,$todo) {
		
		$array 							= 	DB::table('instituciones')
        									->where('activo','=',1)
        									->where('id','<>','1CIX00000001')
        									->groupby('instituciones.distrito')
        									->orderby('instituciones.distrito','asc')
		        							->pluck('distrito','distrito')
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}






	private function gn_generacion_estados_sobrantes($tabla,$atributo1,$atributo2,$titulo,$todo,$tipoestado) {
		
		$periodo_array 					=   Conei::where('institucion_id','=',Session::get('usuario')->institucion_id)
			    							->where('periodo_id','<>','ESRE00000003')
											->pluck('periodo_id')
											->toArray();

		$array 							= 	DB::table($tabla)
											->whereNotIn('id',$periodo_array)
        									->where('activo','=',1)
        									->where('tipoestado','=',$tipoestado)
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}


	private function gn_combo_provincias($departamento_id)
	{
		$datos =	[];
		$datos = 	DB::table('provincias')
						->where('departamento_id','=',$departamento_id)
						->where('activo',1)
						->pluck('descripcion','id')
						->toArray();
		return [''=>'SELECCIONE PROVINCIA']+$datos;
	}

	private function gn_combo_distritos($provincia_id)
	{
		$datos =	[];
		$datos = 	DB::table('distritos')
						->where('provincia_id','=',$provincia_id)
						->where('activo',1)
						->pluck('descripcion','id')
						->toArray();
		return [''=>'SELECCIONE DISTRITO']+$datos;
	}

	private function gn_combo_categoria($tipocategoria,$titulo,$todo) {
		$array 						= 	DB::table('categorias')
        								->where('activo','=',1)
        								->where('tipo_categoria','=',$tipocategoria)
		        						->pluck('descripcion','id')
										->toArray();
		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}
	 	return  $combo;					 			
	}

	private function gn_combo_estadoscompras($titulo,$todo) {
		$array 						= 	DB::table('categorias')
        								->where('activo','=',1)
        								->where('tipo_categoria','=','ESTADO_GENERAL')
        								->whereIn('descripcion', ['GENERADO','EMITIDO','EXTORNADO'])
		        						->pluck('descripcion','id')
										->toArray();
		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}
	 	return  $combo;					 			
	}

	private function gn_generacion_combo_array($titulo, $todo , $array)
	{
		if($todo=='TODO'){
			$combo_anio_pc  		= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo_anio_pc  		= 	array('' => $titulo) + $array;
		}
	    return $combo_anio_pc;
	}

	private function gn_generacion_combo($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 						= 	DB::table($tabla)
        								->where('activo','=',1)
		        						->pluck($atributo2,$atributo1)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_tabla_osiris($tabla,$atributo1,$atributo2,$titulo,$todo) {
		
		$array 							= 	DB::table($tabla)
        									->where('COD_ESTADO','=',1)
		        							->pluck($atributo2,$atributo1)
											->toArray();
		if($titulo==''){
			$combo  					= 	$array;
		}else{
			if($todo=='TODO'){
				$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
			}else{
				$combo  				= 	array('' => $titulo) + $array;
			}
		}

	 	return  $combo;					 			
	}

	private function gn_generacion_combo_categoria($txt_grupo,$titulo,$todo) {
		
		$array 						= 	DB::table('CMP.CATEGORIA')
        								->where('COD_ESTADO','=',1)
        								->where('TXT_GRUPO','=',$txt_grupo)
		        						->pluck('NOM_CATEGORIA','COD_CATEGORIA')
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;					 			
	}


	public function gn_background_fila_activo($activo)
	{
		$background =	'';
		if($activo == 0){
			$background = 'fila-desactivada';
		}
	    return $background;
	}


	public function gn_combo_tipo_cliente()
	{
		$combo  	= 	array('' => 'Seleccione tipo de cliente' , '0' => 'Tercero', '1' => 'Relacionada');
	    return $combo;
	}

	public function rp_generacion_combo_resultado_control($titulo)
	{
		$combo  	= 	array('' => $titulo , '1' => 'Nueva Cita', '2' => 'Resultado');
	    return $combo;
	}

	public function rp_sexo_paciente($sexo_letra)
	{
		$sexo = 'Femenino';
		if($sexo_letra == 'M'){
			$sexo = 'Maculino';
		}
	    return $sexo;
	}	

	public function rp_tipo_cita($ind_tipo_cita)
	{
		$tipo_cita = 'Nueva Cita';
		if($ind_tipo_cita == 2){
			$tipo_cita = 'Resultado';
		}
	    return $tipo_cita;
	}	
	public function rp_estado_control($ind_atendido)
	{
		$estado = 'Atendido';
		if($ind_atendido == 0){
			$estado = 'Sin atender';
		}
	    return $estado;
	}	


	private function gn_generacion_combo_productos($titulo,$todo)
	{


		$array 						= 	ALMProducto::where('COD_ESTADO','=',1)
										->whereIn('IND_MATERIAL_SERVICIO', ['M','S'])
		        						->pluck('NOM_PRODUCTO','COD_PRODUCTO')
		        						->take(10)
										->toArray();

		if($todo=='TODO'){
			$combo  				= 	array('' => $titulo , $todo => $todo) + $array;
		}else{
			$combo  				= 	array('' => $titulo) + $array;
		}

	 	return  $combo;	
	}


}