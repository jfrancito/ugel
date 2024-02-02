
$(document).ready(function(){
    var carpeta = $("#carpeta").val();
	
	// no react or anything
	let state = {};

	// state management
	function updateState(newState) {
		state = { ...state, ...newState };
		console.log(state);
	}


	$("#upload").change(function(e) {
		debugger;
		let files = document.getElementById("upload").files;
		console.log(files);
		let filesArr = Array.from(files);
		updateState({ files: files, filesArr: filesArr });

		renderFileList();
	});


	$("#uploadapafa").change(function(e) {
		debugger;
		let files = document.getElementById("uploadapafa").files;
		console.log(files);
		let filesArr = Array.from(files);
		updateState({ files: files, filesArr: filesArr });

		renderFileListapafa();
	});

	$(".files").on("click", "li > i", function(e) {
		debugger;
		let key = $(this)
			.parent()
			.attr("key");
		let curArr = state.filesArr;
		curArr.splice(key, 1);
		updateState({ filesArr: curArr });
		renderFileList();
	});


	$(".filesapafa").on("click", "li > i", function(e) {
		debugger;
		let key = $(this)
			.parent()
			.attr("key");
		let curArr = state.filesArr;
		curArr.splice(key, 1);
		updateState({ filesArr: curArr });
		renderFileListapafa();
	});


	$("#formagregararchivos").on("submit", function(e) {
		debugger;
		// e.preventDefault();	
		const ul = document.getElementById('larchivos');
		const liElements = ul.getElementsByTagName('li');
		const textArray = [];

		// Recorre cada <li> y agrega su texto al array
		for (let i = 0; i < liElements.length; i++) {
		    const li = liElements[i];
		    const text = li.querySelector('.nombre_file'); // Encuentra el <span> con la clase 'nombre_archivo' dentro del <li>
		    const texto = text.textContent;
		    textArray.push(texto);
		}
		$('#archivos').val(textArray);
		// var archivos = $('#archivos').val();
		// alerterror505ajax('files:  '+archivos);
		return true;
	});

	// render functions
	function renderFileList() {
		let fileMap = state.filesArr.map((file, index) => {
			let suffix = "bytes";
			let size = file.size;
			if (size >= 1024 && size < 1024000) {
				suffix = "KB";
				size = Math.round(size / 1024 * 100) / 100;
			} else if (size >= 1024000) {
				suffix = "MB";
				size = Math.round(size / 1024000 * 100) / 100;
			}

			return `<li key="${index}"><span class='nombre_file'>${
				file.name
			}</span> <span class="file-size">${size} ${suffix}</span><i class="mdi mdi-delete"></i></li>`;


		});
		$("#larchivos").html(fileMap);
	}

	// render functions
	function renderFileListapafa() {
		let fileMap = state.filesArr.map((file, index) => {
			let suffix = "bytes";
			let size = file.size;
			if (size >= 1024 && size < 1024000) {
				suffix = "KB";
				size = Math.round(size / 1024 * 100) / 100;
			} else if (size >= 1024000) {
				suffix = "MB";
				size = Math.round(size / 1024000 * 100) / 100;
			}

			return `<li key="${index}"><span class='nombre_file'>${
				file.name
			}</span> <span class="file-size">${size} ${suffix}</span><i class="mdi mdi-delete"></i></li>`;


		});
		$("#larchivosapafa").html(fileMap);
	}

 	
 	$('#btncancelar').on('click', function() {
 		debugger;
        // Clonar el elemento de carga de archivos
        $('#larchivos').empty();
	    // Limpiar el campo de carga de archivos
	    $('#upload').val('');
	        // Reemplazar el elemento de carga de archivos con uno nuevo vacío
	    state = {};
    });

	$('#btnclonar').on('click', function(event) {
 		debugger;
 		event.preventDefault();
        // Clonar el elemento de carga de archivos
 		var combolocal = $('#lote_id').select2('data');
		var local_id  =   '';
        var cadlocal  =   '';
        if(combolocal){
            local_id  	=   combolocal[0].id;
            cadlocal    =   combolocal[0].text;
        }
        if(local_id==''){
        	alerterrorajax('DEBE SELECCIONAR UN LOTE PARA CLONAR');
        	return false;
        }
 

    	$.confirm({
            title: '¿Confirma Emitir la Evaluacion?',
            content: 'Clonar Archivos del Lote : '+ cadlocal,
            buttons: {
                confirmar: function () {
                    abrircargando();
                    $( "#formclonararchivos" ).submit();
                },
                cancelar: function () {
                    $.alert('Se cancelo la Clonacon de Archivos');
                }
            }
        });

        // $.confirm({
        //     title: '¿Confirma Clonacion?',
        //     content: 'Clonar Archivos del Lote : '+ cadlocal,
        //     buttons: {
        //         confirmar: function () {
        //             abrircargando();
        //             $( "#formclonararchivos" ).submit();
        //         },
        //         cancelar: function () {
        //             $.alert('Se cancelo el clonar Archivos');
        // 			return false;
        //         }
        //     }
        // });
        // return false;

    });




});