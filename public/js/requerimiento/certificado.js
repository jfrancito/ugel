$(document).ready(function(){

    var carpeta = $("#carpeta").val();


    $(".certificado").on('change','#institucion_id', function() {

        event.preventDefault();
        var institucion_id       =   $('#institucion_id').val();
        var procedencia_id       =   $('#procedencia_id').val();

        var _token               =   $('#token').val();
        //validacioones
        if(institucion_id ==''){ alerterrorajax("Seleccione una institucion."); return false;}

        data            =   {
                                _token              : _token,
                                institucion_id      : institucion_id,
                                procedencia_id      : procedencia_id,
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xinstitucion","ajax_periodo")                    

    });


    $(".certificado").on('click','.btn-guardar-certificado', function() {

        var activo       =   $('#activo').val();
        var descripcion       =   $('#descripcion').val();
        debugger;
        if(activo == 'CEES00000002' || activo == 'CEES00000003'){
            if(descripcion == ''){ alerterrorajax("Ingrese un Motivo de Extorno/Baja."); return false;}
        }            
        return true;
        

    });





    $(".certificado").on('change','#activo', function() {

        event.preventDefault();
        var activo       =   $('#activo').val();
                

        if(activo == 'CEES00000002' || activo == 'CEES00000003'){
            $( ".bajaextorno" ).addClass( "mostrar" );
            $( ".bajaextorno" ).removeClass( "ocultar" );
        }else{
            $( ".bajaextorno" ).addClass( "ocultar" );
            $( ".bajaextorno" ).removeClass( "mostrar" );
        }



    });

    $(".certificado").on('change','#procedencia_id', function() {

        event.preventDefault();
        var procedencia_id       =   $('#procedencia_id').val();
        var institucion_id       =   $('#institucion_id').val();

        var _token               =   $('#token').val();
        //validacioones

        if(procedencia_id ==''){ alerterrorajax("Seleccione una procedencia."); return false;}

        data            =   {
                                _token              : _token,
                                institucion_id      : institucion_id,
                                procedencia_id      : procedencia_id,
                            };

        ajax_normal_combo(data,"/ajax-combo-periodo-xinstitucion","ajax_periodo")                    

    });






});