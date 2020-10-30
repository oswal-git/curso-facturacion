<?php 
	$alerta="";
	session_start();

	if ( !empty($_SESSION['activo']))
	{
		header('location: sistema');
	} else
	{
		if ( !empty($_POST))
		{
			if ( empty($_POST['usuario'] || $_POST['clave']))
			{
				$alerta = "Ingrese su usuario y su clave";
			} else 
			{
				require_once("conexion.php");
				$usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
				$clave = md5(mysqli_real_escape_string($conexion, $_POST['clave']));
				// echo $_POST['clave'];
				// echo $clave;
				$sql = "SELECT u.*, r.rol AS nom_rol
					        FROM usuario u 
					        LEFT OUTER JOIN rol r 
					           ON u.rol = r.idrol
					        WHERE usuario = '$usuario' AND clave = '$clave'";
				$query = mysqli_query($conexion, $sql);
					// "SELECT * FROM usuario WHERE usuario = '$usuario' AND clave = '$clave'");
				$resultado = mysqli_num_rows($query);

				if ( $resultado > 0 )
				{
					$datos = mysqli_fetch_array($query);
					mysqli_close($conexion);
					
					$_SESSION['activo'] = true;
					$_SESSION['idUsuario'] = $datos['idusuario'];
					$_SESSION['nombre'] = $datos['nombre'];
					$_SESSION['email'] = $datos['correo'];
					$_SESSION['usuario'] = $datos['usuario'];
					$_SESSION['rol'] = $datos['rol'];
					$_SESSION['nom_rol'] = $datos['nom_rol'];

					header('location: sistema');
				} else
				{
					$alerta = "El usuario o la clave es incorrecto";
					session_destroy();
				}
			}
		}
	}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximun-scale=1.0, minimum-scale=1.0">
	<title>Login | Sistema facturación</title>
	<link rel="stylesheet" type="text/css" href="css/login_style.css">
</head>
<body>
	<section id="contenedor">

		<form action="" method="post">
			<h3>Iniciar sesión</h3>
			<img src="img/login.jpg" alt="Login">

			<input type="text" name="usuario" placeholder="Usuario">
			<input type="password" name="clave" placeholder="Contraseña">
			<p class="alerta"><?php echo (isset($alerta) ? $alerta : ""); ?></p>
			<input type="submit" value="INGRESAR">
		</form>
	</section>
</body>
</html>