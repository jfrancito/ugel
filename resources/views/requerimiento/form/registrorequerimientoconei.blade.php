<div class="tab-container">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#informacion" data-toggle="tab">INFORMACION</a></li>
    <li><a href="#apafa" data-toggle="tab">APAFA</a></li>
  </ul>
  <div class="tab-content">
    <div id="informacion" class="tab-pane active cont">

      <fieldset>
        <legend>DATOS DE LA II.EE.</legend>

            <input type="hidden" name="institucion_id" id = 'institucion_id' value='{{$institucion->id}}'>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>CODIGO INSTITUTO <small class="">(II.EE.)</small> : </b></label>
                <p>{{$institucion->codigoinstitucion}}</p>
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
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>DNI <small class="">(Director)</small> : </b></label>

                <input  type="text"
                        id="dni_director" name='dni_director' 
                        value="{{$director->dni_director}}"                       
                        placeholder="DNI"
                        required = ""
                        maxlength="300"                     
                        autocomplete="off" class="form-control input-sm dni_director" data-aw="1" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('dni_director')  , 
                                              'error' => $errors->first('dni_director', ':message') , 
                                              'data' => '1'])
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>NOMBRES <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="nombre_director" name='nombre_director' 
                        value="{{$director->nombre_director}}"                        
                        placeholder="NOMBRES"
                        required = ""
                        maxlength="300"                     
                        autocomplete="off" class="form-control input-sm nombre_director" data-aw="2" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('nombre_director')  , 
                                              'error' => $errors->first('nombre_director', ':message') , 
                                              'data' => '2'])
              </div>
            </div>


            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>APELLIDOS PATERNO <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="apellidopaterno_director" name='apellidopaterno_director' 
                        value="{{$director->apellidopaterno_director}}"                      
                        placeholder="APELLIDOS PATERNO"
                        required = ""
                        maxlength="200"                     
                        autocomplete="off" class="form-control input-sm apellidopaterno_director" data-aw="3" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('apellidopaterno_director')  , 
                                              'error' => $errors->first('apellidopaterno_director', ':message') , 
                                              'data' => '3'])
              </div>
            </div>


            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>APELLIDOS MATERNO <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="apellidomaterno_director" name='apellidomaterno_director' 
                        value="{{$director->apellidomaterno_director}}"                     
                        placeholder="APELLIDOS MATERNO"
                        required = ""
                        maxlength="200"                     
                        autocomplete="off" class="form-control input-sm apellidomaterno_director" data-aw="4" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('apellidomaterno_director')  , 
                                              'error' => $errors->first('apellidomaterno_director', ':message') , 
                                              'data' => '4'])
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>TELEFONO <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="telefono_director" name='telefono_director' 
                        value="{{$director->telefono_director}}"                    
                        placeholder="TELEFONO"
                        required = ""
                        maxlength="50"                     
                        autocomplete="off" class="form-control input-sm telefono_director" data-aw="5" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('telefono_director')  , 
                                              'error' => $errors->first('telefono_director', ':message') , 
                                              'data' => '5'])
              </div>
            </div>


            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>CORREO ELECTRONICO <small class="">(Director)</small> : </b></label>
                <input  type="text"
                        id="correo_director" name='correo_director' 
                        value="{{$director->correo_director}}"                     
                        placeholder="CORREO ELECTRONICO"
                        required = ""
                        maxlength="50"                     
                        autocomplete="off" class="form-control input-sm correo_director" data-aw="6" readonly/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('correo_director')  , 
                                              'error' => $errors->first('correo_director', ':message') , 
                                              'data' => '6'])
              </div>
            </div>
      </fieldset>


    </div>
    <div id="apafa" class="tab-pane cont">

      <fieldset>
        <legend>CONEI</legend>


            <div class="col-sm-3">
              <div class="input-group my-group">
                <label class="control-label"><b>DNI <small class="">(Director CONEI)</small> : </b></label>
                <input  type="text"
                        id="dni_director_conei" name='dni_director_conei' 
                        value=""
                        value="{{ old('dni_director_conei') }}"                         
                        placeholder="DNI"
                        required = ""
                        maxlength="10"                     
                        autocomplete="off" class="form-control input-sm dni_director_conei" data-aw="7"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('dni_director_conei')  , 
                                              'error' => $errors->first('dni_director_conei', ':message') , 
                                              'data' => '7'])

                    <span class="input-group-btn">
                      <button class="btn btn-primary btn-buscar_dni"
                              data_dni = 'dni_director_conei'
                              data_nombre = 'nombre_director_conei'
                              data_apellidopaterno = 'apellidopaterno_director_conei'
                              data_apellidomaterno = 'apellidomaterno_director_conei'
                              type="button" 
                              style="margin-top: 27px;height: 37px;">
                              <span class="mdi mdi-search"></span></button>
                    </span>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>NOMBRES <small class="">(Director CONEI)</small> : </b></label>
                <input  type="text"
                        id="nombre_director_conei" name='nombre_director_conei' 
                        value=""
                        value="{{ old('nombre_director_conei') }}"                         
                        placeholder="NOMBRES"
                        required = ""
                        maxlength="300"                     
                        autocomplete="off" class="form-control input-sm nombre_director_conei" data-aw="8"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('nombre_director_conei')  , 
                                              'error' => $errors->first('nombre_director_conei', ':message') , 
                                              'data' => '8'])
              </div>
            </div>


            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>APELLIDOS PATERNO <small class="">(Director CONEI)</small> : </b></label>
                <input  type="text"
                        id="apellidopaterno_director_conei" name='apellidopaterno_director_conei' 
                        value=""
                        value="{{ old('apellidopaterno_director_conei') }}"                         
                        placeholder="APELLIDOS PATERNO"
                        required = ""
                        maxlength="200"                     
                        autocomplete="off" class="form-control input-sm apellidopaterno_director_conei" data-aw="9"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('apellidopaterno_director_conei')  , 
                                              'error' => $errors->first('apellidopaterno_director_conei', ':message') , 
                                              'data' => '9'])
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>APELLIDOS MATERNO <small class="">(Director CONEI)</small> : </b></label>
                <input  type="text"
                        id="apellidomaterno_director_conei" name='apellidomaterno_director_conei' 
                        value=""
                        value="{{ old('apellidomaterno_director_conei') }}"                         
                        placeholder="APELLIDOS MATERNO"
                        required = ""
                        maxlength="200"                     
                        autocomplete="off" class="form-control input-sm apellidomaterno_director_conei" data-aw="10"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('apellidomaterno_director_conei')  , 
                                              'error' => $errors->first('apellidomaterno_director_conei', ':message') , 
                                              'data' => '10'])
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
      <fieldset>
        <legend>APAFA</legend>

            <div class="col-sm-3">
              <div class="input-group my-group">
                <label class="control-label"><b>DNI <small class="">(Presidente)</small> : </b></label>
                <input  type="text"
                        id="dni_presidente_apafa" name='dni_presidente_apafa' 
                        value=""
                        value="{{ old('dni_presidente_apafa') }}"                         
                        placeholder="DNI"
                        required = ""
                        maxlength="10"                     
                        autocomplete="off" class="form-control input-sm dni_presidente_apafa" data-aw="11"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('dni_presidente_apafa')  , 
                                              'error' => $errors->first('dni_presidente_apafa', ':message') , 
                                              'data' => '11'])

                    <span class="input-group-btn">
                      <button class="btn btn-primary btn-buscar_dni"
                              data_dni = 'dni_presidente_apafa'
                              data_nombre = 'nombre_presidente_apafa'
                              data_apellidopaterno = 'apellidopaterno_presidente_apafa'
                              data_apellidomaterno = 'apellidomaterno_presidente_apafa'
                              type="button" 
                              style="margin-top: 27px;height: 37px;">
                              <span class="mdi mdi-search"></span></button>
                    </span>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>NOMBRES <small class="">(Presidente)</small> : </b></label>
                <input  type="text"
                        id="nombre_presidente_apafa" name='nombre_presidente_apafa' 
                        value=""
                        value="{{ old('nombre_presidente_apafa') }}"                         
                        placeholder="NOMBRES"
                        required = ""
                        maxlength="300"                     
                        autocomplete="off" class="form-control input-sm nombre_presidente_apafa" data-aw="12"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('nombre_presidente_apafa')  , 
                                              'error' => $errors->first('nombre_presidente_apafa', ':message') , 
                                              'data' => '12'])
              </div>
            </div>


            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>APELLIDOS PATERNO <small class="">(Presidente)</small> : </b></label>
                <input  type="text"
                        id="apellidopaterno_presidente_apafa" name='apellidopaterno_presidente_apafa' 
                        value=""
                        value="{{ old('apellidopaterno_presidente_apafa') }}"                         
                        placeholder="APELLIDOS PATERNO"
                        required = ""
                        maxlength="200"                     
                        autocomplete="off" class="form-control input-sm apellidopaterno_presidente_apafa" data-aw="13"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('apellidopaterno_presidente_apafa')  , 
                                              'error' => $errors->first('apellidopaterno_presidente_apafa', ':message') , 
                                              'data' => '13'])
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label"><b>APELLIDOS MATERNO <small class="">(Presidente)</small> : </b></label>
                <input  type="text"
                        id="apellidomaterno_presidente_apafa" name='apellidomaterno_presidente_apafa' 
                        value=""
                        value="{{ old('apellidomaterno_presidente_apafa') }}"                         
                        placeholder="APELLIDOS MATERNO"
                        required = ""
                        maxlength="200"                     
                        autocomplete="off" class="form-control input-sm apellidomaterno_presidente_apafa" data-aw="14"/>
                @include('error.erroresvalidate', [ 'id' => $errors->has('apellidomaterno_presidente_apafa')  , 
                                              'error' => $errors->first('apellidomaterno_presidente_apafa', ':message') , 
                                              'data' => '14'])
              </div>
            </div>

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

