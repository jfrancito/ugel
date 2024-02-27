@extends('template_lateral')

@section('style')
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
@stop

@section('section')
	<div class="be-content contenido" style="height: 100vh;">
		<div class="main-content container-fluid">
				<div class='container'>
					<div class="row">

						<div class="col-md-12">
					            <div class="col-md-5">
					              <div class="panel panel-contrast">
					                <div class="panel-heading panel-heading-contrast color_azul cblanco" >DIRECTOR<span class="panel-subtitle cblanco">DATOS PERSONALES</span></div>
					                <div class="panel-body ">

					                  	<h4 class="center"><b> {{Session::get('direccion')->dni}}</b></h4>
					                  	<h6 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>DNI</b></h6>

					                  	<h4 class="center"><b> {{Session::get('direccion')->nombres}}</b></h4>
					                  	<h6 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>NOMBRES</b></h6>

					                  	<h4 class="center"><b> {{Session::get('direccion')->telefono}}</b></h4>
					                  	<h6 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>TELEFONO</b></h6>

					                  	<h4 class="center"><b> {{Session::get('direccion')->correo}}</b></h4>
					                  	<h6 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>CORREO</b></h6>					                  	
					                </div>
					              </div>
					            </div>
					    </div>


					</div>
			</div>
		</div>
	</div>
@stop 
@section('script')

		<script src="{{ asset('public/lib/jquery-flot/jquery.flot.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/jquery.flot.pie.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/jquery.flot.resize.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/plugins/jquery.flot.orderBars.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-flot/plugins/curvedLines.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery.sparkline/jquery.sparkline.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/countup/countUp.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jqvmap/jquery.vmap.min.js') }}" type="text/javascript"></script>
		<script src="{{ asset('public/lib/jqvmap/maps/jquery.vmap.world.js') }}" type="text/javascript"></script>

		<script type="text/javascript">
			$(document).ready(function(){
				App.init();
				// App.dashboard();
			});
		</script>   

@stop
