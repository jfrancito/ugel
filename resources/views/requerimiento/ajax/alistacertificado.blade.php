<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>ITEM</th>
      <th>CODIGO LOCAL</th>
      <th>INSTITUCION</th>
      <th>NIVEL</th>
      <th>PERIODO</th>
      <th>PROCEDENCIA</th>
      <th>FECHA CREA</th>
      <th>ESTADO</th>
      <th>MOTIVO</th>
      <th>NRO TRAMITE</th>
      <th>OPCION</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_certificado_id = "{{$item->id}}">
        <td>{{$index +1}}</td>
        <td>{{$item->institucion_codigo}}</td>
        <td>{{$item->institucion->nombre}}</td>
        <td>{{$item->institucion_nivel}}</td>

        <td>{{$item->periodo_nombre}}</td>
        <td>{{$item->procedencia->nombre}}</td>
        <td>{{$item->fecha_crea}}</td>

        <td>
          @include('requerimiento.ajax.estados')
        </td>
        <td>{{$item->msj_extorno}}</td>
        <td>{{$item->numero_tramite}}</td>

        
        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">


              @if($item->estado_id == 'CEES00000001' || $item->estado_id == 'CEES00000003' || $item->estado_id == 'CEES00000002') 
              <li>
                <a href="{{ url('/descargar-archivo-certificado/'.Hashids::encode(substr($item->id, -8)).'/'.Hashids::encode(substr($item->archivo_id, -8))) }}">
                  Descargar
                </a>  
              </li>
              @endif

              @if($item->estado_id == 'CEES00000001' || $item->estado_id == 'CEES00000003' || $item->estado_id == 'CEES00000004' || $item->estado_id == 'CEES00000006') 
                <li>
                  <a href="{{ url('/modificar-certificado/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                    Modificar
                  </a>  
                </li>
              @endif
            </ul>
          </div>
        </td>


      </tr>                    
    @endforeach


  </tbody>
</table>

@if(isset($ajax))
  <script type="text/javascript">
    $(document).ready(function(){
       App.dataTables();
    });
  </script> 
@endif