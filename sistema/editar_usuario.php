<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();
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
		if ( empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['rol']) )
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$idUsuario = $_POST['idUsuario'];
			$nombre = $_POST['nombre'];
			$correo = $_POST['correo'];
			$usuario = $_POST['usuario'];
			$clave = ( empty($_POST['clave']) ? "" : md5($_POST['clave']));
			$rol = $_POST['rol'];

			$sql = "SELECT * FROM usuario 
				   WHERE ( ( usuario = '$usuario' AND idusuario != $idUsuario ) OR 
				   		 ( correo = '$correo'   AND idusuario != $idUsuario )    )";
			// echo $sql . "<br>";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El correo o el usuario ya existe.</p>';
			} else
			{
				$sql = "UPDATE usuario 
				        SET nombre = '$nombre', 
				            correo = '$correo', 
				            usuario = '$usuario'," . 
				            ($clave == "" ? "" : "clave =  '$clave',") . 
				           "rol = '$rol'
				         WHERE idusuario = $idUsuario";
				// echo $sql . "<br>";
				// exit;
				$query_update = mysqli_query($conexion, $sql);
				if ( $query_update )
				{
					$alerta = '<p class="msg_ok">Usuario actualizado correctamente.</p>';
				} else
				{
					$alerta = '<p class="msg_error">Error al actualizar el usuario.</p>';
				}
			}

		}
	}

	// Recuperar datos
	// 
	echo $_GET['id'];
	if ( empty($_GET['id']))
	{
		header('Location: lista_usuarios.php');
	} else
	{
		$iduser = $_GET['id'];

		$sql = "SELECT u.*, r.rol as nom_rol 
			   FROM usuario u 
			   INNER JOIN rol r 
			      ON u.rol = r.idrol 
			   WHERE idusuario = '$iduser' and estatus != 0 ";
		$query = mysqli_query($conexion, $sql );
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: lista_usuarios.php');
		}
		$datos = mysqli_fetch_array($query);
		// print_r($datos);
		// echo "<br>";
	}

	mysqli_close($conexion);
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Actualizar Uuario</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="far fa-edit"></i> Actualizar usuario</h1>
			<hr>
			<div <?php echo ( empty($alerta) ? '' : 'class="alerta"' ); ?>><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>
			<form action="" method="post" accept-charset="utf-8">
				<input type="hidden" name="idUsuario" value="<?php echo $datos['idusuario']; ?>">
				<label for="nombre">Nombre</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value ="<?php echo $datos['nombre']; ?>">
				<label for="correo">Correo electrónico</label>
				<input type="email" name="correo" id="correo" placeholder="Correo electrónico" value ="<?php echo $datos['correo']; ?>">
				<label for="usuario">Usuario</label>
				<input type="text" name="usuario" id="usuario" placeholder="Usuario" value ="<?php echo $datos['usuario']; ?>">
				<label for="clave">Clave</label>
				<input type="password" name="clave" id="clave" placeholder="Clave de acceso">
				<label for="rol">Tipo de Usuario</label>
				<select name="rol" id="rol" class="noPrimerItem">
					<option value="<?php echo $datos['rol']; ?>" select><?php echo $datos['nom_rol']; ?></option>
					<?php echo $opciones_rol; ?>
				</select>
				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar usuario</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>