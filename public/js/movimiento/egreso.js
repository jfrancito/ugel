$(document).ready(function(){

    var carpeta = $("#carpeta").val();

    $(".egreso").on('keyup','.seriekeypress', function() {
        var valueserie              =   $(this).val().toUpperCase();
        $(this).val(valueserie);

    });

    $(".egreso").on('keyup','#numero , #dni , #numero_deposito_bancario', function() {
        var valuennumero              =   $(this).val().replace(/\D/g, '');
        $(this).val(valuennumero);

    });

    $(".egreso").on('focusout','#numero', function() {

        var valuennumero              =   $(this).val().toString().padStart(8,0);
        $(this).val(valuennumero);

    });

    /*$(".egreso").on('click','.btn-agregar-egreso', function() {

        console.log(filesSelected);
        if (!filesSelected) {alerterrorajax("Ingrese un Archivo PDF de Sustento.");return false;}
        
        abrircargando();
        return true;        
    });*/

    $(".formegreso").on('change','#tipo_gasto_id', function() {
debugger;
        var _token                  =   $('#token').val();

        var tipogasto               =   $('#tipo_gasto_id').select2('data');
        var tipo_gasto_id           =   $('#tipo_gasto_id').val();
        
        if(tipogasto)
        {
            tipo_gasto_id   =   tipogasto[0].id;
            if(tipo_gasto_id=='TGEG00000001'){
                $('.formegreso .datosegreso').show(200);
            }
            else{
                $('.formegreso .datosegreso').hide(200);             
            }
            
            debugger;
            $.ajax({
                    type    :   "POST",
                    url     :   carpeta+"/ajax-cargar-tipo-concepto",
                    data    :   {
                                    _token  : _token,
                                    tipo_gasto_id : tipo_gasto_id,
                                },
                    success: function (data) {
                        $(".formegreso .tipoconcepto").html(data);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        }              
    });
});