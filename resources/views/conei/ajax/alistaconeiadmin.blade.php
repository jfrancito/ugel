<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>CODIGO REQ.</th>
      <th>PERIODO</th>
      <th>INSTITUCION</th>
      <th>DIRECTOR</th>
      <th>DNI</th>
      <th>FECHA CREA</th>
      <th>ESTADO</th>
      <th>OPCION</th>
    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_requerimiento_id = "{{$item->id}}">

        <td>{{$item->codigo}}</td>
        <td>{{$item->periodo_nombre}}</td>
        <td>{{$item->institucion_nombre}}</td>
        <td>{{$item->director_nombre}}</td>
        <td>{{$item->director_dni}}</td>

        <td>{{$item->fecha_crea}}</td>
        <td>
          @include('requerimiento.ajax.estados')
        </td>

        <td class="rigth">
          <div class="btn-group btn-hspace">
            <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Acción <span class="icon-dropdown mdi mdi-chevron-down"></span></button>
            <ul role="menu" class="dropdown-menu pull-right">
              <li>
                <a href="{{ url('/gestion-detalle-conei/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  GESTION CONEI
                </a>
              </li>
              <li>
                <a href="{{ url('/detalle-conei/'.$idopcion.'/'.Hashids::encode(substr($item->id, -8))) }}">
                  DETALLLE
                </a>
              </li>
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