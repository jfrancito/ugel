<div class="tab-container">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#informacion" data-toggle="tab"><b> INFORMACION</b></a></li>
    <li><a href="#conei" class="conei" data-toggle="tab"><b> CONEI </b></a></li>
  </ul>
  <div class="tab-content">
    <div id="informacion" class="tab-pane active cont">

      <fieldset>
        <legend>DATOS DE LA II.EE.</legend>

            <input type="hidden" name="institucion_id" id = 'institucion_id' value='{{$institucion->id}}'>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>CODIGO INSTITUTO <small class="">(II.EE.)</small> : </b></label>
                <p>{{$institucion->codigo}}</p>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>NOMBRE DE LA II.EE. <small class="">(II.EE.)</small> : </b></label>
                <p>{{strtoupper($institucion->nombre)}}</p>

              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label"><b>DEPARTAMENTO <small class="">(II.EE.)</small> : </b></label>
                <p>{{strtoupper($institucion->departamento)}} - {{strtoupper($institucion->departamento)}} - {{strtoupper($institucion->distrito)}}</p>

              </div>
            </div>
      </fieldset>
      <fieldset>
        <legend>DATOS DEL DIRECTOR</legend>

            <input type="hidden" name="director_id" id = 'director_id' value='{{$director->id}}'>

            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label"><b>NOMBRES <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="nombre_director" name='nombre_director' 
                        value="{{$director->nombres}}"                        
                        placeholder="NOMBRES"
                        required = ""
                        maxlength="300"                     
                        autocomplete="off" class="form-control input-sm nombre_director" data-aw="1" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('nombre_director')  , 
                                              'error' => $errors->first('nombre_director', ':message') , 
                                              'data' => '1'])
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>TELEFONO <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="telefono_director" name='telefono_director' 
                        value="{{$director->telefono}}"                    
                        placeholder="TELEFONO"
                        required = ""
                        maxlength="50"                     
                        autocomplete="off" class="form-control input-sm telefono_director" data-aw="2"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('telefono_director')  , 
                                              'error' => $errors->first('telefono_director', ':message') , 
                                              'data' => '2'])
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>CORREO ELECTRONICO <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="correo_director" name='correo_director' 
                        value="{{$director->correo}}"                     
                        placeholder="CORREO ELECTRONICO"
                        required = ""
                        maxlength="50"                     
                        autocomplete="off" class="form-control input-sm correo_director" data-aw="3"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('correo_director')  , 
                                              'error' => $errors->first('correo_director', ':message') , 
                                              'data' => '3'])
              </div>
            </div>
      </fieldset>
      <br>
      <div style="text-align: right;">
        <button type="button" class="btn btn-space btn-success btn-next">Siguiente</button>
      </div>
    </div>
    <div id="conei" class="tab-pane cont">



      <fieldset>
        <legend>CONEI </legend>

            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>DIRECTOR : </b></label>

                  <input  type="text"
                          id="director_nombres" name='director_nombres' 
                          value=""
                          value="{{ old('director_nombres') }}"                         
                          placeholder="DIRECTOR"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm director_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('director_nombres')  , 
                                                'error' => $errors->first('director_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_director'
                            data_dni    = 'i_dni_director'
                            data_nombre = 'i_nombre_director'
                            data_nombre_visible = 'director_nombres'
                            data_titulo = 'DIRECTOR'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>


                  <input type="hidden" name="i_tipodocumento_director">
                  <input type="hidden" name="i_dni_director">
                  <input type="hidden" name="i_nombre_director">

              </div>
            </div>



      </fieldset>


      <fieldset>
        <legend>ARCHIVOS</legend>

            <div class="col-sm-6">
              <div>
                <label class="labelarchivos" for="uploadapafa">
                  <input type="file" id="uploadapafa" name='uploadapafa[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required>
                  Adjuntar la Resolución Directoral de reconocimiento de APAFA en formato PDF
                </label>
              </div>
              <div class="files filesapafa">
                <h4>Archivos Seleccionados</h4>
                <ul id='larchivosapafa' class="larchivosapafa"></ul>
                <input type="hidden" name="archivos" id='archivos' value="">
              </div>
            </div>


            <div class="col-sm-6">
              <div>
                <label class="labelarchivos" for="upload">
                  <input type="file" id="upload" name='upload[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required>
                  Adjuntar la Resolución Directoral de reconocimiento de CONEI en formato PDF
                </label>
              </div>
              <div class="files">
                <h4>Archivos Seleccionados</h4>
                <ul id='larchivos' class="larchivos"></ul>
                <input type="hidden" name="archivos" id='archivos' value="">
              </div>
            </div>
      </fieldset>


      <div class="row xs-pt-15">
        <div class="col-xs-6">
            <div class="be-checkbox">
            </div>
        </div>
        <div class="col-xs-6">
          <p class="text-right">
            <button type="submit" class="btn btn-space btn-primary">Guardar</button>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

