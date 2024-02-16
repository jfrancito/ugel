$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".conei").on('click','.modal-registro', function() {
        event.preventDefault();
        var _token                  =   $('#token').val();
        var data_td                 =   $(this).attr('data_td');
        var data_dni                =   $(this).attr('data_dni');
        var data_nombre             =   $(this).attr('data_nombre');
        var data_titulo             =   $(this).attr('data_titulo');
        var data_nombre_visible     =   $(this).attr('data_nombre_visible');

        data                        =   {
                                            _token                  : _token,
                                            data_td                 : data_td,
                                            data_dni                : data_dni,
                                            data_nombre             : data_nombre,
                                            data_nombre_visible     : data_nombre_visible,
                                            data_titulo             : data_titulo,
                                        };
                              
        ajax_modal(data,"/ajax-modal-registro",
                  "modal-conei-apafa","modal-conei-apafa-container");

    });



    $(".conei").on('click','.btn_buscar_dni', function(e) {
        event.preventDefault();


        var data_dni_m              =   $(this).attr('data_dni_m');
        var data_nombre_m           =   $(this).attr('data_nombre_m');
        var dni                     =   $('#documentog').val();
        var _token                  =   $('#token').val();
        actualizar_ajax_dni_conei(_token,carpeta,dni,data_nombre_m);


    });

    $(".conei").on('click','.btn_asignar_nombre', function(e) {

        event.preventDefault();
        var tdg                     =   $('#tdg').val();
        var documentog              =   $('#documentog').val();
        var nombresg                =   $('#nombresg').val();

        var data_td                 =   $(this).attr('data_td');
        var data_dni                =   $(this).attr('data_dni');
        var data_nombre             =   $(this).attr('data_nombre');
        var data_nombre_visible     =   $(this).attr('data_nombre_visible');



        if(tdg ==''){ alerterrorajax("Seleccione un tipo documento."); return false;}
        if(documentog ==''){ alerterrorajax("Ingrese un documento de identidad."); return false;}
        if(nombresg ==''){ alerterrorajax("Ingrese un nombre."); return false;}

        $('#'+data_td).val(tdg);
        $('#'+data_dni).val(documentog);
        $('#'+data_nombre).val(nombresg);
        $('#'+data_nombre_visible).val(documentog + ' - ' +nombresg);

        $('#modal-conei-apafa').niftyModal('hide');


    });






    function actualizar_ajax_dni_conei(_token,carpeta,data_dni_m,data_nombre_m){

        abrircargando();
        $.ajax({
            type    :   "POST",
            url     :   carpeta+"/ajax-buscar-dni-ugel",
            data    :   {
                            _token          : _token,
                            dni             : data_dni_m
                        },
            success: function (data){
                cerrarcargando();
                var array        = $.parseJSON(data);
                var nombrea      = array[1];
                var apellidopa   = array[2];
                var apellidoma   = array[3];
                $('#'+data_nombre_m).val(nombrea+' '+apellidopa+' '+apellidoma);

            
            },
            error: function (data) {
                cerrarcargando();
                error500(data);
            }
        });
    }




});




