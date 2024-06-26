@extends('template_lateral')
@section('style')
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" />
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/jqvmap/jqvmap.min.css') }} "/>
		<link rel="stylesheet" type="text/css" href="{{ asset('public/lib/datetimepicker/css/bootstrap-datetimepicker.min.css') }}" />
    	<link rel="stylesheet" type="text/css" href="{{ asset('public/css/dashboard.css?v='.$version) }} "/>
@stop
@section('section')
	@include('dashboard')
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
