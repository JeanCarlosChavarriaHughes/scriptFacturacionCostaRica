<?php
	/*-------------------------*/
		//Includes
		include("../../is_logged.php");
		use Illuminate\Database\Capsule\Manager as Capsule;
		use Ajax\Helpers as Helpers;

		/* Connect To Database*/
		// include("../../config/db.php");
		// include("../../config/conexion.php");
		/*Archivo de funciones PHP*/
		include("../../funciones.php");
		/*Librería PDF*/
		require_once(dirname(__FILE__).'/../html2pdf.class.php');
	/*---------------------------*/

	//Recibe Variables por GET
	$id_cliente 	= intval($_GET['id_cliente']);
	$id_vendedor 	= intval($_GET['id_vendedor']);
	$condiciones 	= mysqli_real_escape_string($con,(strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));
	$moneda 		= $_GET['moneda'];
	$credito_dias 	= $_GET['plazo_credito_dias'];
	$medio_pago 	= $_GET['medio_pago'];
	$simbolo_moneda = $moneda;

	//Consulta si existen productos en la sesión actual
	$session_id = session_id();
	$sql_count=mysqli_query($con,"select * from tmp where tmp.session_id='".$session_id."' and tmp.moneda_tmp='".$moneda."'");
	$count=mysqli_num_rows($sql_count);
	if ($count == 0){
		echo "<script>alert('No hay productos agregados a la factura')</script>";
		echo "<script>window.close();</script>";
		exit;
	}

	//Define ID para la nueva factura
	$sql=mysqli_query($con, "select LAST_INSERT_ID(numero_factura) as last from facturas order by id_factura desc limit 0,1 ");
	$rw=mysqli_fetch_array($sql);
	$numero_factura=$rw['last']+1;

	/*Determina el id de la factura que se creará a continuación */
	$id_actual = (int) Capsule::table('facturas')->max('id_factura');
	$id_factura_creada = $id_actual + 1;

    // get the HTML
    ob_start();
    include(dirname('__FILE__').'/res/factura_html.php');
    $content = ob_get_clean();

    try
    {
        // init HTML2PDF
        $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
        // display the full page
        $html2pdf->pdf->SetDisplayMode('fullpage');
        // convert
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        // send the PDF

        /*Imprime el pdf en pantalla*/
        $html2pdf->Output('Factura'.$numero_factura.'.pdf', '');

        /*Guarda el pdf en una variable*/
        $pdfContent = $html2pdf->output('Factura'.$numero_factura.'.pdf', 'S');

        /*------------CREA, ENVÍA Y CONSULTA XML. Imprime en consola los resultados.-------------*/
    	//Instancia el Objeto
    	$helpers = new Helpers();

    	//Valida el estado del token
    	$helpers->validateTokenApi();

    	$xml = $helpers->createXmlFE($helpers, $id_factura_creada);
    	// echo "<script type='text/javascript'>console.log('XML CREADO:".json_encode($xml)."');</script>";

    	$xmlfirmado = $helpers->firmarXML($helpers, $xml->resp->xml,"FE");
    	// echo "<script type='text/javascript'>console.log('XML FIRMADO:".json_encode($xmlfirmado)."');</script>";

    	$enviofe = $helpers->envioHaciendaFE($helpers, $xmlfirmado->resp->xmlFirmado, $id_factura_creada);
    	// echo "<script type='text/javascript'>console.log('RESP ENVIO HACIENDA:".json_encode($enviofe)."');</script>";

    	$consultaenviofe = $helpers->consultaEnvioHaciendaFE($xml->resp->clave);
    	//echo "<script type='text/javascript'>console.log('RESP CONSULTA ENVIO:".json_encode($consultaenviofe)."');</script>";

		/*Envía copia al cliente*/
		$acuse = $consultaenviofe->resp->{'respuesta-xml'};
    	$helpers->sendEmailToReceptor($helpers, $xml->resp->clave, $xmlfirmado->resp->xmlFirmado, /* $xmlfirmado->resp->xmlFirmado */ $acuse, $pdfContent, $id_cliente);
    	/*-------------------------*/

    }catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }

