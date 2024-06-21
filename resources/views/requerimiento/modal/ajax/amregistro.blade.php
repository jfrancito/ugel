<div class="modal-header">
	<button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
	<h3 class="modal-title">
		 {{$data_titulo}}
	</h3>
</div>
<div class="modal-body">
	<div  class="row regla-modal">
		    <div class="col-md-12">

            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label"><b>Tipo Documento : </b></label>
                {!! Form::select( 'tdg', $combotd, $selecttd,
                                  [
                                    'class'       => 'select3 form-control control input-xs combo ctipodocumento',
                                    'id'          => 'tdg',
                                  ]) !!}
              </div>
            </div>
            <div class="col-sm-6">
              <div class="input-group my-group">
                  <label class="control-label"><b>Documento : </b></label>
                  <input  type="text"
                          id="documentog" 
                          name='documentog'                        
                          placeholder="DOCUMENTO"
                          required = ""
                          maxlength="10"                     
                          autocomplete="off" class="form-control input-sm"/>

                    <span class="input-group-btn">
                      <button class="btn btn-primary btn_buscar_dni"
                              data_dni_m = 'documentog'
                              data_nombre_m = 'nombresg'
                              type="button" 
                              style="margin-top: 26px;height: 37px;">
                              Buscar Reniec</button>
                    </span>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label class="control-label"><b>NOMBRES <small class="">({{$data_titulo}})</small> : </b></label>
                <input  type="text"
                        id="nombresg" 
                        name='nombresg'                        
                        placeholder="NOMBRES"
                        required = ""
                        maxlength="300"                     
                        autocomplete="off" class="form-control input-sm"/>

              </div>
            </div>
		    </div>
	    <div class="col-md-6">
	    </div>
	</div>
</div>

<div class="modal-footer">
  <button type="submit" 
  				data-dismiss="modal" class="btn btn-success btn_asignar_nombre"

  				data_td_id = '{{$data_td_id}}'
          data_td_no = '{{$data_td_no}}'
          data_docu = '{{$data_docu}}'
          data_nombre = '{{$data_nombre}}'
          data_nombre_visible = '{{$data_nombre_visible}}'

          data_rp_id_val = '{{$data_rp_id_val}}'
          data_rp_no_val = '{{$data_rp_no_val}}'
          data_rp_id = '{{$data_rp_id}}'
          data_rp_no = '{{$data_rp_no}}'




  				>
  	Asignar
  </button>
</div>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
			$(".select3").select2({
		      width: '100%'
		  });
    });
  </script>
@endif



