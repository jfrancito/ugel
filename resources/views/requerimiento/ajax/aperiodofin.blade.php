<div class="col-sm-6">
  <div class="form-group">
    <label class="control-label"><b>Periodo Fin : </b></label>
    {!! Form::select( 'periodofin_id', $comboperiodofin, $selectperiodofin,
                      [
                        'class'       => 'select4 form-control control input-xs',
                        'id'          => 'periodofin_id',
                        'disabled'    => true
                      ]) !!}
  </div>
</div>

<div class="col-sm-12">
    <h5  style="text-align:center;font-size: 16px;">{{$mensaje}}</h5>
    <input type="hidden" name="indb" id="indb" value='{{$ind}}'>
</div>
@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
      $(".select4").select2({
          width: '100%'
      });
    });
  </script>
@endif