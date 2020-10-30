<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();	
	include_once "../conexion.php"; 

	// Lista de usuarios
	$sql = "SELECT * FROM usuario";
	$query_usu = mysqli_query($conexion, $sql );
	$resultado_usu = mysqli_num_rows($query_usu);
	$opciones_usu = '';
	
	if ( $resultado_usu > 0 )
	{
		while ( $usu = mysqli_fetch_array($query_usu) )
		{
			$opciones_usu .= '<option value="'.$usu['idusuario'].'">' . $usu['usuario'] . '</option>';
		}
	}
	// Fin lista de usuarios

	if ( !empty($_POST))
	{
		$alerta ="";
		if ( empty($_POST['nif']) || empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion']) || empty($_POST['usuario']) )
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$idcliente = $_POST['idcliente'];
			$nif = $_POST['nif'];
			$nombre = $_POST['nombre'];
			$telefono = $_POST['telefono'];
			$direccion = $_POST['direccion'];
			$usuario = $_POST['usuario'];

			$sql = "SELECT * FROM cliente 
				   WHERE ( ( nit = '$nif' AND idcliente != $idcliente ) )";
			// echo $sql . "<br>";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El NIF ya existe.</p>';
			} else
			{
				$sql = "UPDATE cliente 
				        SET nit = '$nif', 
				            nombre = '$nombre', 
				            telefono = '$telefono',
				            direccion = '$direccion',
				            usuario_id = '$usuario'
				         WHERE idcliente = $idcliente";
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
	// echo $_GET['id'] . "<br>";
	if ( !is_numeric($_GET['id']))
	{
		header('Location: lista_clientes.php');
		// exit;
	} else
	{
		$idcliente = $_GET['id'];

		$sql = "SELECT c.idcliente, c.nit, c.nombre, c.telefono, c.direccion, c.usuario_id, u.idusuario , u.usuario 
		        FROM cliente c 
		        INNER JOIN usuario u 
		           ON c.usuario_id = u.idusuario
	             WHERE idcliente = '$idcliente' and c.estatus != 0 ";
        	// echo $sql . "<br>";
		$query = mysqli_query($conexion, $sql );
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: lista_clientes.php');
			// exit;
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
	<title>Actualizar Cliente</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="far fa-edit"></i> Actualizar cliente</h1>
			<hr>
			<div class="alerta"><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>
			<form action="" method="post" accept-charset="utf-8">
				<input type="hidden" name="idcliente" value="<?php echo $datos['idcliente']; ?>">
				<label for="nif">NIF</label>
				<input type="text" name="nif" id="nif" placeholder="NIF" value ="<?php echo $datos['nit']; ?>">
				<label for="nombre">Nombre</label>
				<input type="text" name="nombre" id="nombre" placeholder="Nombre completo" value ="<?php echo $datos['nombre']; ?>">
				<label for="telefono">Teléfono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value ="<?php echo $datos['telefono']; ?>">
				<label for="direccion">Direccion</label>
				<input type="text" name="direccion" id="direccion" placeholder="Direccion completa" value ="<?php echo $datos['direccion']; ?>">
				<label for="usuario">Usuario</label>
				<select name="usuario" id="usuario" class="noPrimerItem">
					<option value="<?php echo $datos['idusuario']; ?>" select><?php echo $datos['usuario']; ?></option>
					<?php echo $opciones_usu; ?>
				</select>
				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar cliente</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>