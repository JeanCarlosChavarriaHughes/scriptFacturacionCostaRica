<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../../login.php");
	exit;
}
/* Connect To Database*/
include("../../config/db.php");
include("../../config/conexion.php");
	//Archivo de funciones PHP
include("../../funciones.php");

//Incluimos la clase de PHPMailer
//require_once('../../libraries/phpmailer/class.phpmailer.php');
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

//Load composer's autoloader
require '../../vendor/autoload.php';

$id_factura= intval($_GET['id_factura']);
$email= $_GET['email'];
$sql_count=mysqli_query($con,"select * from facturas where id_factura='".$id_factura."'");
$count=mysqli_num_rows($sql_count);
if ($count==0)
{
	echo "<script>alert('Factura no encontrada')</script>";
	echo "<script>window.close();</script>";
	exit;
}
$sql_factura=mysqli_query($con,"select * from facturas where id_factura='".$id_factura."'");
$rw_factura=mysqli_fetch_array($sql_factura);
$numero_factura=$rw_factura['numero_factura'];
$id_cliente=$rw_factura['id_cliente'];
$id_vendedor=$rw_factura['id_vendedor'];
$fecha_factura=$rw_factura['fecha_factura'];
$condiciones=$rw_factura['condiciones'];
$simbolo_moneda=get_row('perfil','moneda', 'id_perfil', 1);

$file_output=$_SERVER['DOCUMENT_ROOT'] . '/pdfTemporal/'.$numero_factura.'.pdf';
require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
ob_start();
include(dirname('__FILE__').'/res/ver_factura_html.php');
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
	//$html2pdf->Output('../../pdfTemporal/factura'.$numero_factura.'.pdf', 'F');
	$html2pdf->Output($file_output, 'F');	 
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

$mensajeDefault="Muchas gracias por su preferencia, adjuntamos la factura digital de su compra en este correo.";

function msjDefault($msjDefault){
if(isset($_POST['message'])AND($msjDefault != $_POST['message'])){
	echo $_POST['message'];
} else {
	echo $msjDefault;
}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Enviar por email</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	
	<link rel="stylesheet" href="../../css/custom.css">
	<link rel=icon href='../../img/logo-icon.png' sizes="32x32" type="image/png">
</head>
<body> 
	<div class="container">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h4><i class='glyphicon glyphicon-envelope'></i> Enviar email </h4>
			</div>
			<div class="panel-body">

				<form class="form-horizontal" method="post">
					<div class="form-group">
						<label for="email" class="col-sm-3 control-label">Email del cliente:</label>
						<div class="col-sm-8">
							<input class="form-control input-sm" type="text" name="email" value="<?php echo $email; ?>" readonly>
						</div>
					</div>
					<div class="form-group">
						<label for="asunto" class="col-sm-3 control-label">Asunto</label>
						<div class="col-sm-8">
							<input class="form-control input-sm" type="text" name="asunto" value="Factura <?php echo $numero_factura; ?> - Gracias por su preferencia">
						</div>
					</div>
					<div class="form-group">
						<label for="mensaje" class="col-sm-3 control-label">Mensaje</label>
						<div class="col-sm-8">
						<textarea class="form-control input-sm" rows="5" name="message" cols="30"><?php msjDefault($mensajeDefault); ?></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" onclick="self.close()">Cerrar</button>
						<input type="submit" name="submit" value="Enviar correo" class="btn btn-primary">
					</div>

				</form>


			</div>
		</div>	
		
	</div>
	<hr>
</body>
</html>

<?php 

if(isset($_POST['submit'])){
	$subject = $_POST['asunto'];
	$body = $_POST['message'];
	$my_name = "Facturacion Digital Simple";//Agregue su nombre o asunto
	$my_mail = "demo@demo.com";//Agregue su propio email 
	$my_replyto = "info@imagineing.com";//El email para respuestas
	//$my_file = 'factura'.$numero_factura.'.pdf';
	//$file = "../../pdfTemporal/".$my_file;
	$file = $file_output;
	$filename = 'factura-'.$numero_factura.'.pdf';
	$correo = new PHPMailer(true); //Creamos una instancia en lugar usar mail()

	//Server settings
	// https://stackoverflow.com/questions/16048347/send-email-using-gmail-smtp-server-through-php-mailer/16048485#16048485
	$correo->SMTPDebug = 2; // debugging: 1 = errors and messages, 2 = messages only
	$correo->isSMTP();                                        // Set mailer to use SMTP
	$correo->Host = getenv('EMAIL_HOST');                           // Specify main and backup SMTP servers
	$correo->SMTPAuth = true;                                 // Enable SMTP authentication
	$correo->Username = getenv('EMAIL_USER');                   // SMTP username
	$correo->Password = getenv('EMAIL_PASS');                   // SMTP password
	$correo->SMTPSecure = 'tls';                              // Enable TLS encryption, `ssl` also accepted
	//$correo->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
	$correo->Port = 587;
	//$correo->Port = 465; //Trying to avoid PHPMailer error  PHP Fatal error:  Uncaught PHPMailer\PHPMailer\Exception: SMTP Error: Could not authenticate. in /app/vendor/phpmailer/phpmailer/src/PHPMailer.php:2037
	/*
	2021-02-25T20:53:30.380079+00:00 app[web.1]: Stack trace:
	2021-02-25T20:53:30.380216+00:00 app[web.1]: #0 /app/vendor/phpmailer/phpmailer/src/PHPMailer.php(1857): PHPMailer\PHPMailer\PHPMailer->smtpConnect()
	2021-02-25T20:53:30.380339+00:00 app[web.1]: #1 /app/vendor/phpmailer/phpmailer/src/PHPMailer.php(1600): PHPMailer\PHPMailer\PHPMailer->smtpSend()
	2021-02-25T20:53:30.380511+00:00 app[web.1]: #2 /app/vendor/phpmailer/phpmailer/src/PHPMailer.php(1436): PHPMailer\PHPMailer\PHPMailer->postSend()
	2021-02-25T20:53:30.380662+00:00 app[web.1]: #3 /app/pdf/documentos/archivo_temp_email.php(182): PHPMailer\PHPMailer\PHPMailer->send()
	2021-02-25T20:53:30.380684+00:00 app[web.1]: #4 {main}
	2021-02-25T20:53:30.380814+00:00 app[web.1]:   thrown in /app/vendor/phpmailer/phpmailer/src/PHPMailer.php on line 2037
	2021-02-25T20:53:30.381500+00:00 app[web.1]: 10.69.244.94 - - [25/Feb/2021:14:53:29 -0600] "POST /pdf/documentos/archivo_temp_email.php?id_factura=198&email=negrotico19@gmail.com HTTP/1.1" 500 2132 "https://factura-simple-develop.herokuapp.com/pdf/documentos/archivo_temp_email.php?id_factura=198&email=negrotico19@gmail.com" "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36
	2021-02-25T20:53:30.642627+00:00 heroku[router]: at=info method=GET path="/css/custom.css" host=factura-simple-develop.herokuapp.com request_id=d2603563-f331-4038-a88a-ea380bec7c4d fwd="186.26.118.248" dyno=web.1 connect=1ms service=1ms status=200 bytes=2757 protocol=https
	*/

	//Usamos el SetFrom para decirle al script quien envia el correo
	$correo->SetFrom($my_mail, $my_name);

	//Usamos el AddReplyTo para decirle al script a quien tiene que responder el correo
	$correo->AddReplyTo($my_replyto,$my_name);

	//Usamos el AddAddress para agregar un destinatario
	$correo->AddAddress($email, $email);

	//Ponemos el asunto del mensaje
	$correo->Subject = $subject;

	/*
	* Si deseamos enviar un correo con formato HTML utilizaremos MsgHTML:
	* $correo->MsgHTML("<strong>Mi Mensaje en HTML</strong>");
	* Si deseamos enviarlo en texto plano, haremos lo siguiente:
	* $correo->IsHTML(false);
	* $correo->Body = "Mi mensaje en Texto Plano";
	*/
	$correo->MsgHTML($body);
				

	//Si deseamos agregar un archivo adjunto utilizamos AddAttachment
	$correo->AddAttachment($file );

	//Enviamos el correo
	if(!$correo->Send()) {
		$resultado = "Error enviando el correo, por favor intente de nuevo.  Error:". $correo->ErrorInfo;
		echo '<div id="resultados"><div class="alert alert-warning alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
		<strong>Error!</strong> '.$resultado.'</div></div>';
	} else {
		$resultado = "Correo enviado con éxito!!";
		echo '<div id="resultados"><div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
		<strong>Aviso!</strong> '.$resultado.'</div></div>';
	}


}
?>
