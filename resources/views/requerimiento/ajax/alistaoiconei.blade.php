<div class="col-sm-12">
  <div class="panel panel-default panel-table">
    <div class="panel-heading">Registre otros representantes
      <div class="tools">
        <button class="btn btn-rounded btn-space btn-success modal-registro-oi" data_representante_id='ESRP00000009'>Agregar</button>
      </div>
    </div>
    <div class="panel-body">
      <table class="table table-striped table-borderless totrosrepresentante">
        <thead>
          <tr>
            <th>Fila</th>
            <th>Representante</th>
            <th>Nivel</th>
            <th>Tipo documento</th>
            <th>Documento</th>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Eliminar</th>
          </tr>
        </thead>
        <tbody class="no-border-x">
          @foreach($array_detalle_producto as $index => $item)
            <tr t_nivel="{{$item['codigo_modular_id']}}" t_representante_id="{{$item['representante_id']}}">
              <td>{{$index+1}}</td>
              <td>{{$item['representante_txt']}}</td>
              <td>{{$item['niveltexto']}}</td>
              <td>{{$item['tdgtexto']}}</td>
              <td>{{$item['documentog']}}</td>
              <td>{{$item['nombresg']}}</td>
              <td>{{$item['dcargoni']}}</td>
              <td>
                <div class="icon" style="text-align: center;">
                  <span class="mdi mdi-close-circle eliminaroi" data-fila= "{{$item['fila']}}" style="font-size: 20px;cursor: pointer;color: #E74C3C;"></span>
                </div>
              </td>
            </tr>

          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
<input type="hidden" name="array_detalle_producto" id='array_detalle_producto' value='{{json_encode($array_detalle_producto)}}'>