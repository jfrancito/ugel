<table id="nso" class="table table-striped table-borderless table-hover td-color-borde td-padding-7 listatabla">
  <thead>
    <tr>
      <th>CODIGO REQ.</th>
      <th>DIRECTOR</th>
      <th>TELEFONO</th>
      <th>CORREO ELECTRONICO</th>
      <th>FECHA CREA</th>
      <th>ESTADO</th>

    </tr>
  </thead>
  <tbody>
    @foreach($listadatos as $index => $item)
      <tr data_requerimiento_id = "{{$item->id}}">
        <td>{{$item->codigo}}</td>
        <td>{{$item->nombres_director_institucion}}</td>
        <td>{{$item->telefono_director_institucion}}</td>
        <td>{{$item->correo_director_institucion}}</td>
        <td>{{$item->fecha_crea}}</td>
        <td>
          @if($item->estado_id == 'ESRE00000001') 
              <span class="badge badge-default">{{$item->estado_nombre}}</span> 
          @else
            @if($item->estado_id == 'ESRE00000002') 
                <span class="badge badge-primary">{{$item->estado_nombre}}</span> 
            @else
                <span class="badge badge-primary">{{$item->estado_nombre}}</span> 
            @endif
          @endif
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