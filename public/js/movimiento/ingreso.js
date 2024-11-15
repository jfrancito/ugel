$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".ingreso").on('keyup','.seriekeypress', function() {
        var valueserie              =   $(this).val().toUpperCase();
        $(this).val(valueserie);

    });

    $(".ingreso").on('keyup','#numero , #dni , #numero_deposito_bancario', function() {
        var valuennumero              =   $(this).val().replace(/\D/g, '');
        $(this).val(valuennumero);

    });

    $(".ingreso").on('focusout','#numero', function() {

        var valuennumero              =   $(this).val().toString().padStart(8,0);
        $(this).val(valuennumero);

    });

    $('.ingreso').on('change','#fecha_comprobante', function() {
        var _token                  =   $('#token').val();
        debugger;
        var fecha_comprobante = $('#fecha_comprobante').val();        
        
        $.ajax({
                    type    :   "POST",
                    url     :   carpeta+"/ajax-cargar-trimestre-ingreso",
                    data    :   {
                                    _token  : _token,
                                    fecha_comprobante : fecha_comprobante,
                                },
                    success: function (data) {
                        $(".ingreso .trimestre").html(data);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
    });

    // $(".ingreso").on('click','.btn-agregar-ingreso', function() {
    //     console.log(filesSelected);
    //     if (!filesSelected) {alerterrorajax("Ingrese un Archivo PDF de Sustento.");return false;}
    //     abrircargando();
    //     return true;
    // });

    
});