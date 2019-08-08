$(document).ready(function(){
	load(1);
});

function load(page){
	var q= $("#q").val();
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'./ajax/buscar_clientes.php?action=ajax&page='+page+'&q='+q,
		 beforeSend: function(objeto){
		 $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
	  },
		success:function(data){
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');

		}
	})
}

function eliminar (id){
	var q= $("#q").val();
	if (confirm("Realmente deseas eliminar el cliente")){
		$.ajax({
			type: "GET",
			url: "./ajax/buscar_clientes.php",
			data: "id="+id,"q":q,
			beforeSend: function(objeto){
				$("#resultados").html("Mensaje: Cargando...");
			  },
			success: function(datos){
			$("#resultados").html(datos);
			load(1);
		}
		});
	}
}

$("#guardar_cliente").submit(function(event) {
  	$('#guardar_datos').attr("disabled", true);

 	var parametros = $(this).serialize();
	 $.ajax({
		type: "POST",
		url: "ajax/nuevo_cliente.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados_ajax").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);
		}
	});
  	event.preventDefault();
});

$("#editar_cliente").submit(function( event ) {
	$('#actualizar_datos').attr("disabled", true);

 	var parametros = $(this).serialize();
	 $.ajax({
		type: "POST",
		url: "ajax/editar_cliente.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax2").html("Mensaje: Cargando...");
		 },
		success: function(datos){
			$("#resultados_ajax2").html(datos);
			$('#actualizar_datos').attr("disabled", false);
			load(1);
	  	}
	});
  	event.preventDefault();
});

function obtener_datos(id){
	var id_cliente 					= $("#id_cliente_"+id).val();
	var nombre_cliente 				= $("#nombre_cliente_"+id).val();
	var nombre_comercial_cliente 	= $("#nombre_comercial_cliente_"+id).val();
	var tipo_cedula_cliente 		= $("#tipo_cedula_cliente_"+id).val();
	var cedula_cliente 				= $("#cedula_cliente_"+id).val();
	var ubicacion_cliente 			= $("#ubicacion_cliente_"+id).val();
	var direccion_cliente 			= $("#direccion_cliente_"+id).val();
	var telefono_cod_cliente 		= $("#telefono_cod_cliente_"+id).val();
	var telefono_cliente 			= $("#telefono_cliente_"+id).val();
	var telefono_fax_cod_cliente 	= $("#telefono_fax_cod_cliente_"+id).val();
	var telefono_fax_cliente 		= $("#telefono_fax_cliente_"+id).val();
	var email_cliente 				= $("#email_cliente_"+id).val();
	var id_moneda 					= $("#id_moneda_"+id).val();
	var estado_cliente 				= $("#estado_cliente_"+id).val();

	$("#mod_id_cliente").val(id_cliente);
	$("#mod_nombre_cliente").val(nombre_cliente);
	$("#mod_nombre_comercial_cliente").val(nombre_comercial_cliente);
	$("#mod_tipo_cedula_cliente").val(tipo_cedula_cliente);
	$("#mod_cedula_cliente").val(cedula_cliente);

	$("#mod_ubicacion_cliente").val(ubicacion_cliente);
	$('#mod_ubicacion_cliente').trigger('change');

	$("#mod_direccion_cliente").val(direccion_cliente);
	$("#mod_telefono_cod_cliente").val(telefono_cod_cliente);
	$("#mod_telefono_cliente").val(telefono_cliente);
	$("#mod_telefono_fax_cod_cliente").val(telefono_fax_cod_cliente);
	$("#mod_telefono_fax_cliente").val(telefono_fax_cliente);
	$("#mod_email_cliente").val(email_cliente);
	$("#mod_id_moneda").val(id_moneda);
	$("#mod_estado_cliente").val(estado_cliente);
}


