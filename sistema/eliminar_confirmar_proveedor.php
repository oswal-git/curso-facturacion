<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();

	include_once "../conexion.php"; 

	if ( !empty($_POST))
	{
		$alerta ="";

		$codproveedor = $_POST['codproveedor'];

		$sql = "UPDATE proveedor SET estatus = 0 WHERE codproveedor = $codproveedor";
		// echo $sql . "<br>";
		// exit;
		$query_delete = mysqli_query($conexion, $sql);
		if ( $query_delete )
		{
			header('Location: lista_proveedores.php');
		} else
		{
			echo "Error al eliminar el proveedor.";
		}
	}	
	// Recuperar datos
	// 
	if ( empty($_REQUEST['id']) or $_REQUEST['id'] == 1 )
	{
		header('Location: lista_proveedores.php');
	} else
	{
		$codproveedor = $_REQUEST['id'];

		$sql = "SELECT c.* FROM proveedor c WHERE codproveedor = '$codproveedor'";
		$query = mysqli_query($conexion, $sql );
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: lista_proveedores.php');
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
	<title>Eliminar Proveedor</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="data_delete"><i class="fas fa-user-times fa-7x" style="color: #e66262; margin-bottom: 20px;"></i>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Proveedor: <span><?php echo $datos['proveedor']; ?></span></p>
			<p>Contacto: <span><?php echo $datos['contacto']; ?></span></p>
			<p>Telefono: <span><?php echo $datos['telefono']; ?></span></p>
			<p>Dirección: <span><?php echo $datos['direccion']; ?></span></p>

			<form action="" method="post">
				<input type="hidden" name="codproveedor" value="<?php echo $datos['codproveedor']; ?>">
				<a href="lista_proveedores.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
			</form>

		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>