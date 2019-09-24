
$(document).ready(function(){
	load(1);
});

function load(page){
	var moneda_id = document.getElementById('moneda').value;
	var q= $("#q").val();
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'./ajax/productos_factura.php?action=ajax&page='+page+'&q='+q+'&moneda='+moneda_id,
		beforeSend: function(objeto){
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success:function(data){
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');

		}
	});
}


$(function(){
	/*Verifica si la condicion es Crédito para mostrar input plazo en días*/
	$('#condiciones').change(function(){
		var condicion = $('#condiciones').val();
		if(condicion == 2){
			$('#plazo_credito_dias').val('');
			$('#plazo_credito').show();
		}else{
			$('#plazo_credito').hide();
			$('#plazo_credito_dias').val('');
		}
	});
});

function agregar (id)
{
	var precio_venta 	= document.getElementById('precio_venta_'+id).value;
	var cantidad 		= document.getElementById('cantidad_'+id).value;
	var moneda_id 		= document.getElementById('moneda').value;
	var descuento_desc  = document.getElementById('descuento_desc_'+id).value;
	var descuento_monto = document.getElementById('descuento_monto_'+id).value;

			if (descuento_monto != "" && descuento_desc == "")
			{
				alert('Agregue una descripción a este descuento.');
				document.getElementById('cantidad_'+id).focus();
				return false;
			}

			var total_venta = precio_venta * cantidad;
			if(descuento_monto > total_venta){
				alert('El descuento no puede ser mayor al total de la venta.');
				return false;
			}

			//Inicia validacion
			if (isNaN(cantidad))
			{
				alert('Esto no es un numero');
				document.getElementById('cantidad_'+id).focus();
				return false;
			}
			if (isNaN(precio_venta))
			{
				alert('Esto no es un numero');
				document.getElementById('precio_venta_'+id).focus();
				return false;
			}
			//Fin validacion
			$.ajax({
				type: "POST",
				url: "./ajax/agregar_facturacion.php",
				data: "id="+id+"&precio_venta="+precio_venta+"&cantidad="+cantidad+"&moneda="+moneda_id+"&descuento_desc="+descuento_desc+"&descuento_monto="+descuento_monto,
				beforeSend: function(objeto){
					$("#resultados").html("Mensaje: Cargando...");
				},
				success: function(datos){
					$("#resultados").html(datos);
					// if(moneda_id == 1){
					// 	detectImpuesto();
					// }else{
					// 	detectImpuestoColon();
					// }

				}
			});
}

function eliminar (id)
{
	var moneda_id = document.getElementById('moneda').value;

	$.ajax({
		type: "GET",
		url: "./ajax/agregar_facturacion.php",
		data: "id="+id+"&moneda="+moneda_id,
		beforeSend: function(objeto){
			$("#resultados").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados").html(datos);
			// if(moneda_id == 1){
			// 	detectImpuesto();
			// }else{
			// 	detectImpuestoColon();
			// }
		}
	});
	moneda.onchange = function (e) {
		moneda_id = document.getElementById('moneda').value;
		$.ajax({
			type: "GET",
			url: "./ajax/agregar_facturacion.php",
			data: "id="+id+"&moneda="+moneda_id,
			beforeSend: function(objeto){
				$("#resultados").html("Mensaje: Cargando...");
			},
			success: function(datos){
				$("#resultados").html(datos);
				// if(moneda_id == 1){
				// 	detectImpuesto();
				// }else{
				// 	detectImpuestoColon();
				// }
			}
		});
	}
}


$("#datos_factura").submit(function(){
	var id_cliente 			= $("#id_cliente").val();
	var id_vendedor 		= $("#id_vendedor").val();
	var condiciones 		= $("#condiciones").val();
	var medio_pago 			= $("#medio_pago").val();
	var plazo_credito_dias 	= $("#plazo_credito_dias").val();
	var moneda 				= $("#moneda").val();
	var send_link			= $("#send_link")[0].checked;

	if(send_link) {
		// ToDo
		// Enviar link de pago por PayPal al Correo para la factura que se acaba de crear
		// con un token válido por N horas (parametrizable en .env) por defecto 24 horas.
		// Debe poder abrir el link sin estar logueado en el sistema.
		// Ejemplo -> http://127.0.0.1:8096/pagar_factura.php?id_factura=105?hmac=
		// https://dev.to/pim/hmac-authentication-better-protection-for-your-api-4e0
		console.log("Enviar link de pago por PayPal al Correo para la factura que se acaba de crear")
	}

	if(moneda == ""){
		alert("Debes especificar la moneda de la factura.");
		$("#moneda").focus();
		return false;
	}

	if(condiciones == ""){
		alert("Debes especificar la condición de la factura.");
		$("#condiciones").focus();
		return false;
	}

	if ( (condiciones == 2 && plazo_credito_dias == "") || (isNaN(plazo_credito_dias)) ){
		alert("Debes especificar el plazo del crédito en días.");
		$("#plazo_credito_dias").focus();
		return false;
	}

	if (id_cliente == ""){
		alert("Debes seleccionar un cliente");
		$("#nombre_cliente").focus();
		return false;
	}

	VentanaCentrada('./pdf/documentos/factura_pdf.php?id_cliente='+id_cliente+'&id_vendedor='+id_vendedor+'&condiciones='+condiciones+'&moneda='+moneda+'&plazo_credito_dias='+plazo_credito_dias+'&medio_pago='+medio_pago,'Factura','','1024','768','true');
});

$( "#guardar_cliente" ).submit(function( event ) {
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

$( "#guardar_producto" ).submit(function( event ) {
	$('#guardar_datos').attr("disabled", true);

	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/nuevo_producto.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax_productos").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados_ajax_productos").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);
		}
	});
	event.preventDefault();
});