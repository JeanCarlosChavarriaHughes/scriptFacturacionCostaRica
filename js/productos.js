		$(document).ready(function(){
			load(1);
		});

		function load(page){
			var q= $("#q").val();
			$("#loader").fadeIn('slow');
			$.ajax({
				url:'./ajax/buscar_productos.php?action=ajax&page='+page+'&q='+q,
				 beforeSend: function(objeto){
				 $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
			  },
				success:function(data){
					$(".outer_div").html(data).fadeIn('slow');
					$('#loader').html('');

				}
			})
		}



		function eliminar (id)
		{
			var q= $("#q").val();
			if (confirm("Realmente deseas eliminar el producto")){
			$.ajax({
	        type: "GET",
	        url: "./ajax/buscar_productos.php",
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

		$('#codigoImpuesto').change(function(){
			var codigoImpuesto = $('#codigoImpuesto').val();
			$.ajax({
		        type: "GET",
		        url: "./ajax/ValidateCodigoImpuesto.php",
		        data: "codigoImpuesto="+codigoImpuesto,
				beforeSend: function(objeto){
					$("#ValidateCodigoImpuesto").html("Mensaje: Cargando...");
				},
		        success: function(datos){
					$("#ValidateCodigoImpuesto").html(datos);
				}
			});
		});

		$('#mod_codigoImpuesto').change(function(){
			var mod_codigoImpuesto = $('#mod_codigoImpuesto').val();
			$.ajax({
		        type: "GET",
		        url: "./ajax/mod_ValidateCodigoImpuesto.php",
		        data: "mod_codigoImpuesto="+mod_codigoImpuesto,
				beforeSend: function(objeto){
					$("#mod_ValidateCodigoImpuesto").html("Mensaje: Cargando...");
				},
		        success: function(datos){
					$("#mod_ValidateCodigoImpuesto").html(datos);
				}
			});
		});





