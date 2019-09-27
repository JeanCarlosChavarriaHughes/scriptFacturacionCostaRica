<?php
	/*session_start();
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: login.php");
		exit;
        }*/
	$active_facturas="active";
	$active_productos="";
	$active_clientes="";
	$active_usuarios="";
	$title="Pagar Factura | Sistema de Facturación";

	/* Connect To Database*/
	require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
    require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos

	if (isset($_GET['id_factura']))
	{
		$id_factura=intval($_GET['id_factura']);
		//$campos="clientes.id_cliente, clientes.nombre_cliente, clientes.telefono_cliente, clientes.email_cliente, facturas.id_vendedor, facturas.fecha_factura, facturas.condiciones, facturas.estado_factura, facturas.numero_factura,facturas.moneda";
        $campos="facturas.total_venta, facturas.moneda, facturas.pago_online, facturas.pago_online_id_trans, facturas.pago_online_time";
        $sql_factura=mysqli_query($con,"select $campos from facturas, clientes where facturas.id_cliente=clientes.id_cliente and id_factura='".$id_factura."'");
		$count=mysqli_num_rows($sql_factura);
		if ($count==1)
		{
				$rw_factura=mysqli_fetch_array($sql_factura);
				$total_venta=$rw_factura['total_venta'];
				$moneda = $rw_factura['moneda'];
				$_SESSION['id_factura']=$id_factura;
				// $_SESSION['numero_factura']=$numero_factura;
		}
		else
		{
			echo "Multiples facturas encontradas con el mismo id_factura: $id_factura \n";
			echo "Count: $count \n";
			//header("location: facturas.php");
			exit;
		}
	}
	else
	{
		echo "Ninguna factura encontradas con el id_factura: $id_factura";
		//header("location: facturas.php");
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("head.php");?>
  </head>
  <body>
    <div class="container">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4><i class='glyphicon glyphicon-shopping-cart'></i> Pagar Factura</h4>
            </div>
            <div class="panel-body">
            	<center>
            	<?php
            		if ($rw_factura['pago_online'] == 1){
            	?>
	            	<p>Tenga en cuenta que para esta factura ya se registra un pago anterior hecho con id de transacción <b>#<?php echo $rw_factura['pago_online_id_trans'] ?></b> hecho en <b><?php echo $rw_factura['pago_online_time'] ?></b></p>
	            	<hr width="50%">
	            <?php
	        		}
	            ?>
				<h3>Monto a Pagar: <b><?php echo $total_venta; ?></b></h3>
				<div id="paypal-button-container"></div>
				</center>
            </div>
        </div>
	</div>
	<hr>
	<?php
	    include("footer.php");
	?>
    <?php
		$client_id = getenv('SB_CLIENT_ID');
	?>
    <!-- PayPal Required -->
	<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $client_id; ?>&currency=USD"></script>
	<script>paypal.Buttons({
		createOrder: function(data, actions) {
			// Set up the transaction
			return actions.order.create({
				purchase_units: [{
				amount: {
					value: "<?php echo $total_venta; ?>"
				}
				}]
			});
		},
		onApprove: function(data, actions) {
			// Capture the funds from the transaction
			return actions.order.capture().then(function(details) {
				var form = new FormData();
				form.append("orderID",data.orderID);
				form.append("idFactura", <?php echo $id_factura; ?> );

		        fetch('/ajax/paypal/paypal-transaction-complete.php', {
		          method: 'post',
		          body: form
		        }).then(response => {
		        	console.log(response)
				  	if (response.ok) {
				  		alert('¡Muy bien! Este pago se procesó correctamente');
				  		window.location.replace("/pagar_factura.php?id_factura="+<?php echo $id_factura; ?>);
				  	}else if(response.error){
		  		  		alert('No estamos pudiendo procesar este pago ahora mismo. Por favor intente más tarde. Si el problema persiste, contacte con soporte.');
		  		  		window.location.replace("/pagar_factura.php?id_factura="+<?php echo $id_factura; ?>);
				  	}
				  	// return Promise.reject(Error('error'))
				}).catch(error => {
				 	return Promise.reject(Error(error.message))
				});
			});
		},
		onError: function (err) {
			alert('Parece que PayPal no puede procesar este pago ahora mismo. Por favor intente más tarde.');
		}
  	}).render('#paypal-button-container');</script>
  </body>
</html>