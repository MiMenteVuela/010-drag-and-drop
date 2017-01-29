Dropzone.autoDiscover = false;
	
new Dropzone('#form_imagen', {
	autoProcessQueue:true,
	paramName: "imagen-subida",
	uploadMultiple: false,
	addRemoveLinks: true,
	maxFiles: 1,
	acceptedFiles: "image/jpeg,image/jpg,image/png,image/gif",
	parallelUploads: 1,
	init: function() {
		"use strict";
		var myDropzone = this;
	  
		myDropzone.on("success", function(files,response) {
		  var respuesta = response;
		  if (respuesta.estado == "error") {
			  alert(respuesta.mensaje);
		  }
		});
	}
});