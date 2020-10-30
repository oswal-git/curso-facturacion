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
		if ( empty($_POST['proveedor']) || empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion']) )
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$codproveedor = $_POST['codproveedor'];
			$proveedor = $_POST['proveedor'];
			$contacto = $_POST['contacto'];
			$telefono = $_POST['telefono'];
			$direccion = $_POST['direccion'];
			$estado = ( empty($_POST['estado']) ? "" : $_POST['estado']);
			// echo $estado . "<br>";
			$usuario_id = $_POST['usuario'];

			$sql = "SELECT * FROM proveedor WHERE proveedor = '$proveedor' AND codproveedor != $codproveedor ";
			// echo $sql . "<br>";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El proveedor ya existe.</p>';
			} else
			{
				$sql = "UPDATE proveedor 
				        SET proveedor = '$proveedor', 
				            contacto = '$contacto', 
				            telefono = '$telefono',
				            direccion = '$direccion',";
				$sql .= ( $estado == "" ? "" : " estatus = '$estado',");
				$sql .= "    usuario_id = '$usuario_id'
				         WHERE codproveedor = $codproveedor";
				// echo $sql . "<br>";
				// exit;
				$query_update = mysqli_query($conexion, $sql);
				if ( $query_update )
				{
					$alerta = '<p class="msg_ok">Proveedor actualizado correctamente.</p>';
				} else
				{
					$alerta = '<p class="msg_error">Error al actualizar el proveedor.</p>';
				}
			}

		}
	}

	// Recuperar datos
	// 
	// echo $_GET['id'];
	if ( empty($_GET['id']))
	{
		header('Location: lista_proveedores.php');
	} else
	{
		$codproveedor = $_GET['id'];

		if ( !empty($_GET['estado']) ) 
		{
			if ( $_SESSION['rol'] != 1 ) 
			{
				header("location: ./");
			}
		}
		
		$sql = "SELECT p.codproveedor, p.proveedor, p.contacto, p.telefono, p.direccion, p.usuario_id, p.estatus, u.idusuario , u.usuario 
		        FROM proveedor p 
		        INNER JOIN usuario u 
		           ON p.usuario_id = u.idusuario ";
		$sql .= ( empty($_GET['estado']) ? " WHERE p.estatus != 0" : " WHERE p.estatus = 0");
	     $sql .= "  AND codproveedor = '$codproveedor'";
		// echo $sql . "<br>";
		$query = mysqli_query($conexion, $sql );
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: lista_proveedores.php');
		}
		$datos = mysqli_fetch_array($query);

		$activar = "";

		$h1 = ' Actualizar proveedor';
		if ( !empty($_GET['estado']) ) 
		{
			$h1 = ' Activar proveedor';
			$activar  = '<label for="estado">Estado</label>';
			$activar .= '<select name="estado" id="estado">';
			$activar .= '	<option value="1" select>Activo</option>';
			$activar .= '	<option value="0">Inactivo</option>';
			$activar .= '</select>';
		}
		// print_r($datos);
		// echo htmlspecialchars($activar, ENT_QUOTES); 
	}

	mysqli_close($conexion);
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Actualizar Proveedor</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="far fa-edit"></i><?php echo $h1; ?></h1>
			<div class="alerta"><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>
			<form action="" method="post" accept-charset="utf-8">
				<input type="hidden" name="codproveedor" value="<?php echo $datos['codproveedor']; ?>">
				<label for="proveedor">Proveedor</label>
				<input type="text" name="proveedor" id="proveedor" placeholder="Proveedor" value ="<?php echo $datos['proveedor']; ?>">
				<label for="contacto">Contacto</label>
				<input type="text" name="contacto" id="contacto" placeholder="Contacto" value ="<?php echo $datos['contacto']; ?>">
				<label for="telefono">Teléfono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono" value ="<?php echo $datos['telefono']; ?>">
				<label for="direccion">Direccion</label>
				<input type="text" name="direccion" id="direccion" placeholder="Direccion completa" value ="<?php echo $datos['direccion']; ?>">
				<?php echo $activar; ?>
				<label for="usuario">Usuario</label>
				<select name="usuario" id="usuario" class="noPrimerItem">
					<option value="<?php echo $datos['idusuario']; ?>" select><?php echo $datos['usuario']; ?></option>
					<?php echo $opciones_usu; ?>
				</select>
				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar proveedor</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>