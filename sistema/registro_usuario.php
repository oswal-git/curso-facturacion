<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	if ( $_SESSION['rol'] != 1) 
	{
		header("location: ./");
	}
	include_once "../conexion.php"; 

	// Lista de roles
	$sql = "SELECT * FROM rol";
	$query_rol = mysqli_query($conexion, $sql );
	$resultado_rol = mysqli_num_rows($query_rol);
	$opciones_rol = '';
	
	if ( $resultado_rol > 0 )
	{
		while ( $rol = mysqli_fetch_array($query_rol) )
		{
			$opciones_rol .= '<option value="'.$rol['idrol'].'">' . $rol['rol'] . '</option>';
		}
	}	
	// Fin lista de roles

	if ( !empty($_POST))
	{
		$alerta ="";
		if ( empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave']) || empty($_POST['rol']) )
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$nombre = $_POST['nombre'];
			$correo = $_POST['correo'];
			$usuario = $_POST['usuario'];
			$clave = md5($_POST['clave']);
			$rol = $_POST['rol'];

			$sql = "SELECT * FROM usuario WHERE usuario = '$usuario' or correo = '$correo'";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El correo o el usuario ya existe.</p>';
			} else
			{
				$sql = "INSERT INTO usuario (nombre, correo, usuario, clave, rol) VALUES ('$nombre', '$correo', '$usuario', '$clave', '$rol')";
				// echo $sql . "<br>";
				$query_insert = mysqli_query($conexion, $sql);
				if ( $query_insert )
				{
					$alerta = '<p class="msg_ok">Usuario creado correctamente.</p>';
				} else
				{
					$alerta = '<p class="msg_error">Error al crear el usuario.</p>';
				}
			}

		}
	}

	mysqli_close($conexion);

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Registro Uuario</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="fas fa-user-plus"></i> Registro usuario</h1>
			<hr>
			<div <?php echo ( empty($alerta) ? '' : 'class="alerta"' ); ?>><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>
			<form action="" method="post" accept-charset="utf-8">
				<label for="nombre">Nombre</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo">
				<label for="correo">Correo electrónico</label>
				<input type="email" name="correo" id="correo" placeholder="Correo electrónico">
				<label for="usuario">Usuario</label>
				<input type="text" name="usuario" id="usuario" placeholder="Usuario">
				<label for="clave">Clave</label>
				<input type="password" name="clave" id="clave" placeholder="Clave de acceso">
				<label for="rol">Tipo de Usuario</label>
				<select name="rol" id="rol">
					<?php echo $opciones_rol; ?>
				</select>
				<button type="submit" class="btn_save"><i class="far fa-save"></i> Crear usuario</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>