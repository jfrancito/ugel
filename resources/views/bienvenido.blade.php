@extends('template_lateral')

@section('style')
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />

<style type="text/css">
.my-card
{
    position:absolute;
    left:40%;
    top:-20px;
    border-radius:50%;
}	

</style>
@stop


@section('section')
	<div class="be-content contenido" style="height: 100vh;">
		<div class="main-content container-fluid">
				<div class='container'>
					<div class="row">
						<div class="col-md-12">
					            <div class="col-md-4">
					              <div class="panel panel-contrast">
					                <div class="panel-heading panel-heading-contrast">REQUERIMIENTO<span class="panel-subtitle">CONEI Y APAFA</span></div>
					                <div class="panel-body">
					                  <p> PLATAFORMA PARA REGISTRO DE CONEI Y APAFA DE INSTITUCIONES EDUCATIVAS PÚBLICAS</p>

					                  <a class="btn btn-rounded btn-space btn-success" href="{{ url('agregar-requerimiento-apafa/oj') }}">Crear Requerimiento APAFA</a>
					                  <a class="btn btn-rounded btn-space btn-success" href="{{ url('agregar-requerimiento-conei/oj') }}">Crear Requerimiento CONEI</a>
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
		{{-- <script src="{{ asset('public/js/app-dashboard.js') }}" type="text/javascript"></script> --}}


		<script type="text/javascript">
			$(document).ready(function(){
				App.init();
				// App.dashboard();
			});
		</script>   

@stop
