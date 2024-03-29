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

						<div class="col-md-10 col-md-offset-1 bciid">

							<div class="col-md-12">
						            <div class="col-md-4 " style="padding-right: 0px;">
				                      <div class="icon-container color_azul right" style=" height: 155px;">
				                        	<span class="mdi mdi-balance" 
				                        	style="font-size: 80px; color: #fff; padding-top: 15px;"></span>
				                      </div>
						            </div>
						            <div class="col-md-7" style="padding-left: 0px;">
						              <div class="panel panel-contrast color_azul" style="margin-bottom:0px;border-radius:0px;">
						                <div class="panel-body">
						                	<h3 class="cblanco center"><b>INSTITUCION</b>  </h3>
						                  	<h3 class="center cblanco"><b> {{Session::get('institucion')->codigo}}</b></h3>
						                  	<h5 class="center color_amarillo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>CODIGO LOCAL</b></h5>
						                  	<h3 class="center cblanco"><b> {{Session::get('institucion')->nombre}}</b></h3>
						                  	<h5 class="center color_amarillo cursiva" style="margin-bottom:0px;margin-top: 0px;">
						                  		<b>{{Session::get('institucion')->departamento}} | {{Session::get('institucion')->provincia}} | {{Session::get('institucion')->distrito}}</b>
						                  	</h5>
						                </div>
						              </div>
						            </div>
						    </div>
							<div class="col-md-12">

						            <div class="col-md-4 " style="padding-right: 0px;">
				                      <div class="icon-container right" style=" height: 246px;">
				                        	<span class="mdi mdi-account cazul" 
				                        	style="font-size: 80px; color: #fff; padding-top: 50px;"></span>
				                      </div>
						            </div>
						            <div class="col-md-7" style="padding-left: 0px;">
						              <div class="panel panel-contrast" style="margin-bottom:0px;border-radius:0px;">
						                <div class="panel-body ">
						                	<h3 class="center"><b>DIRECTOR</b>  </h3>
						                  	<h3 class="center"><b> {{Session::get('direccion')->dni}}</b></h3>
						                  	<h5 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>DNI</b></h5>

						                  	<h3 class="center"><b> {{Session::get('direccion')->nombres}}</b></h3>
						                  	<h5 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>NOMBRES</b></h5>

						                  	<h3 class="center"><b> {{Session::get('direccion')->telefono}}</b></h3>
						                  	<h5 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>TELEFONO</b></h5>

						                  	<h3 class="center"><b> {{Session::get('direccion')->correo}}</b></h3>
						                  	<h5 class="center color_rojo cursiva" style="margin-bottom:0px;margin-top: 0px;"><b>CORREO</b></h5>					                  	
						                </div>
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
