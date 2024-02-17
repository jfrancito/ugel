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

                  <label class="control-label"><b>DIRECTOR : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="director_nombres" name='director_nombres' 
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


                  <input type="hidden" name="i_tipodocumento_director" id = 'i_tipodocumento_director'>
                  <input type="hidden" name="i_dni_director" id = 'i_dni_director'>
                  <input type="hidden" name="i_nombre_director" id = 'i_nombre_director'>

              </div>
            </div>


            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>SUBDIRECTOR : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="subdirector_nombres" name='subdirector_nombres' 
                          value="{{ old('subdirector_nombres') }}"                         
                          placeholder="SUBDIRECTOR"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm subdirector_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('subdirector_nombres')  , 
                                                'error' => $errors->first('subdirector_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_subdirector'
                            data_dni    = 'i_dni_subdirector'
                            data_nombre = 'i_nombre_subdirector'
                            data_nombre_visible = 'subdirector_nombres'
                            data_titulo = 'SUBDIRECTOR'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>


                  <input type="hidden" name="i_tipodocumento_subdirector" id = 'i_tipodocumento_subdirector'>
                  <input type="hidden" name="i_dni_subdirector" id = 'i_dni_subdirector'>
                  <input type="hidden" name="i_nombre_subdirector" id = 'i_nombre_subdirector'>

              </div>
            </div>


            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>REPRESENTANTE DE DOCENTE : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="representantedocente_nombres" name='representantedocente_nombres' 
                          value="{{ old('representantedocente_nombres') }}"                         
                          placeholder="REPRESENTANTE DE DOCENTE"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm representantedocente_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('representantedocente_nombres')  , 
                                                'error' => $errors->first('representantedocente_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_representantedocente'
                            data_dni    = 'i_dni_representantedocente'
                            data_nombre = 'i_nombre_representantedocente'
                            data_nombre_visible = 'representantedocente_nombres'
                            data_titulo = 'REPRESENTANTE DE DOCENTE'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>


                  <input type="hidden" name="i_tipodocumento_representantedocente" id = 'i_tipodocumento_representantedocente'>
                  <input type="hidden" name="i_dni_representantedocente" id = 'i_dni_representantedocente'>
                  <input type="hidden" name="i_nombre_representantedocente" id = 'i_nombre_representantedocente'>

              </div>
            </div>



            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>REPRESENTANTE DE ADMINISTRATIVO : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="representanteadministrativo_nombres" name='representanteadministrativo_nombres' 
                          value="{{ old('representanteadministrativo_nombres') }}"                        
                          placeholder="REPRESENTANTE DE ADMINISTRATIVO"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm representanteadministrativo_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('representanteadministrativo_nombres')  , 
                                                'error' => $errors->first('representanteadministrativo_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_representanteadministrativo'
                            data_dni    = 'i_dni_representanteadministrativo'
                            data_nombre = 'i_nombre_representanteadministrativo'
                            data_nombre_visible = 'representanteadministrativo_nombres'
                            data_titulo = 'REPRESENTANTE DE ADMINISTRATIVO'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>

                  <input type="hidden" name="i_tipodocumento_representanteadministrativo" id = 'i_tipodocumento_representanteadministrativo'>
                  <input type="hidden" name="i_dni_representanteadministrativo" id = 'i_dni_representanteadministrativo'>
                  <input type="hidden" name="i_nombre_representanteadministrativo" id = 'i_nombre_representanteadministrativo'>

              </div>
            </div>


            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>REPRESENTANTE DE APAFA : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="representanteapafa_nombres" name='representanteapafa_nombres' 
                          value="{{ old('representanteapafa_nombres') }}"                        
                          placeholder="REPRESENTANTE DE APAFA"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm representanteapafa_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('representanteapafa_nombres')  , 
                                                'error' => $errors->first('representanteapafa_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_representanteapafa'
                            data_dni    = 'i_dni_representanteapafa'
                            data_nombre = 'i_nombre_representanteapafa'
                            data_nombre_visible = 'representanteapafa_nombres'
                            data_titulo = 'REPRESENTANTE DE APAFA'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>

                  <input type="hidden" name="i_tipodocumento_representanteapafa" id = 'i_tipodocumento_representanteapafa'>
                  <input type="hidden" name="i_dni_representanteapafa" id = 'i_dni_representanteapafa'>
                  <input type="hidden" name="i_nombre_representanteapafa" id = 'i_nombre_representanteapafa'>

              </div>
            </div>




            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>REPRESENTANTE DE ESTUDIANTE : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="representanteestudiante_nombres" name='representanteestudiante_nombres' 
                          value="{{ old('representanteestudiante_nombres') }}"                        
                          placeholder="REPRESENTANTE DE ESTUDIANTE"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm representanteestudiante_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('representanteestudiante_nombres')  , 
                                                'error' => $errors->first('representanteestudiante_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_representanteestudiante'
                            data_dni    = 'i_dni_representanteestudiante'
                            data_nombre = 'i_nombre_representanteestudiante'
                            data_nombre_visible = 'representanteestudiante_nombres'
                            data_titulo = 'REPRESENTANTE DE ESTUDIANTE'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>

                  <input type="hidden" name="i_tipodocumento_representanteestudiante" id = 'i_tipodocumento_representanteestudiante'>
                  <input type="hidden" name="i_dni_representanteestudiante" id = 'i_dni_representanteestudiante'>
                  <input type="hidden" name="i_nombre_representanteestudiante" id = 'i_nombre_representanteestudiante'>

              </div>
            </div>


            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>REPRESENTANTE DE EX ALUMNO : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="representanteexalumno_nombres" name='representanteexalumno_nombres' 
                          value="{{ old('representanteexalumno_nombres') }}"                        
                          placeholder="REPRESENTANTE DE EX ALUMNO"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm representanteexalumno_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('representanteexalumno_nombres')  , 
                                                'error' => $errors->first('representanteexalumno_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_representanteexalumno'
                            data_dni    = 'i_dni_representanteexalumno'
                            data_nombre = 'i_nombre_representanteexalumno'
                            data_nombre_visible = 'representanteexalumno_nombres'
                            data_titulo = 'REPRESENTANTE DE EX ALUMNO'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>

                  <input type="hidden" name="i_tipodocumento_representanteexalumno" id = 'i_tipodocumento_representanteexalumno'>
                  <input type="hidden" name="i_dni_representanteexalumno" id = 'i_dni_representanteexalumno'>
                  <input type="hidden" name="i_nombre_representanteexalumno" id = 'i_nombre_representanteexalumno'>

              </div>
            </div>


            <div class="col-sm-6">
              <div class="input-group my-group">

                  <label class="control-label"><b>OTRO REPRESENTANTE DE LA COMUNIDAD : <small class="obligatorio">(*) Obligatorio</small></b></label>

                  <input  type="text"
                          id="otrorepresentatecomunidad_nombres" name='otrorepresentatecomunidad_nombres' 
                          value="{{ old('otrorepresentatecomunidad_nombres') }}"                        
                          placeholder="OTRO REPRESENTANTE DE LA COMUNIDAD"
                          required = ""                   
                          autocomplete="off" class="form-control input-sm otrorepresentatecomunidad_nombres" data-aw="4" readonly/>

                  @include('error.erroresvalidate', [ 'id' => $errors->has('otrorepresentatecomunidad_nombres')  , 
                                                'error' => $errors->first('otrorepresentatecomunidad_nombres', ':message') , 
                                                'data' => '4'])

                  <span class="input-group-btn">
                    <button class="btn btn-primary modal-registro"
                            data_td     = 'i_tipodocumento_otrorepresentatecomunidad'
                            data_dni    = 'i_dni_otrorepresentatecomunidad'
                            data_nombre = 'i_nombre_otrorepresentatecomunidad'
                            data_nombre_visible = 'otrorepresentatecomunidad_nombres'
                            data_titulo = 'OTRO REPRESENTANTE DE LA COMUNIDAD'
                            type="button" 
                            style="margin-top: 26px;height: 38px;">
                            Buscar</button>
                  </span>

                  <input type="hidden" name="i_tipodocumento_otrorepresentatecomunidad" id = 'i_tipodocumento_otrorepresentatecomunidad'>
                  <input type="hidden" name="i_dni_otrorepresentatecomunidad" id = 'i_dni_otrorepresentatecomunidad'>
                  <input type="hidden" name="i_nombre_otrorepresentatecomunidad" id = 'i_nombre_otrorepresentatecomunidad'>

              </div>
            </div>





      </fieldset>


      <fieldset>
        <legend>ARCHIVOS</legend>


                  <table class="table table-striped table-borderless">
                    <thead>
                      <tr>
                        <th>Requisito</th>
                        <th>Seleccionar</th>
                        <th>Archivo</th>
                      </tr>
                    </thead>
                    <tbody class="no-border-x">

                      <tr>
                        <td>Resolución de Reconocimiento del CONEI de la Institución Educativa.</td>

                        <td>
                          
                            <label class="labelarchivos" for="uploadapafa">
                              <input type="file" id="uploadapafa" name='uploadapafa[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required>
                              Selecionar archivo
                            </label>

                        </td>
                        <td>
                          <div class="files filesapafa">
                            <ul id='larchivosapafa' class="larchivosapafa"></ul>
                            <input type="hidden" name="archivos" id='archivos' value="">
                          </div>
                        </td>


                      </tr>

                      <tr>
                        <td>Actas de Instalación de los miembros del CONEI.</td>

                        <td>
                          <label class="labelarchivos" for="upload">
                            <input type="file" id="upload" name='upload[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required>
                            Selecionar archivo
                          </label>
                        </td>
                        <td>  
                          <div class="files">
                            <ul id='larchivos' class="larchivos"></ul>
                            <input type="hidden" name="archivos" id='archivos' value="">
                          </div>
                        </td>

                      </tr>

                      <tr>
                        <td>Copia de DNI de los integrantes del CONEI.</td>

                        <td>
                          
                            <label class="labelarchivos" for="upload03">
                              <input type="file" id="upload03" name='upload03[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required>
                              Selecionar archivo
                            </label>

                        </td>
                        <td>
                          <div class="archivito files03">
                            <ul id='larchivos03' class="larchivos03"></ul>
                            <input type="hidden" name="archivos" id='archivos' value="">
                          </div>
                        </td>
                      </tr>

                      <tr>
                        <td>Declaración Jurada Simple de antecedentes judiciales y policiales de los integrantes.</td>
                        <td>
                          
                            <label class="labelarchivos" for="upload04">
                              <input type="file" id="upload04" name='upload04[]' accept=".doc,.docx,.xls,.xlsx,.pppt,.pptx,.pdf,image/*,video/*,.mp3,audio/wav,.txt" required>
                              Selecionar archivo
                            </label>

                        </td>
                        <td>
                          <div class="files files04">
                            <ul id='larchivos04' class="larchivos04"></ul>
                            <input type="hidden" name="archivos" id='archivos' value="">
                          </div>
                        </td>
                      </tr>





                    </tbody>
                  </table>

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

