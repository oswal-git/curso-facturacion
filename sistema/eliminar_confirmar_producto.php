<?php 
	if ( (session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE) ) session_start();

	include_once "../conexion.php"; 

	if ( !empty($_POST))
	{
		$alerta ="";

		$codproducto = $_POST['codproducto'];

		$sql = "UPDATE producto SET estatus = 0 WHERE codproducto = $codproducto";
		// echo $sql . "<br>";
		// exit;
		$query_delete = mysqli_query($conexion, $sql);
		if ( $query_delete )
		{
			header('Location: buscar_producto.php');
		} else
		{
			echo "Error al eliminar el producto.";
		}
	}	
	// Recuperar datos
	// 
	if ( empty($_REQUEST['id']) or $_REQUEST['id'] == 1 )
	{
		header('Location: buscar_producto.php');
	} else
	{
		$codproducto = $_REQUEST['id'];

		$sql = "SELECT p.* FROM producto p WHERE codproducto = '$codproducto'";
		$query = mysqli_query($conexion, $sql );
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: buscar_producto.php');
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
	<title>Eliminar Producto</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="data_delete"><i class="fas fa-user-times fa-7x" style="color: #e66262; margin-bottom: 20px;"></i>
			<h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Producto: <span><?php echo $datos['descripcion']; ?></span></p>
			<p>Proveedor: <span><?php echo $datos['proveedor']; ?></span></p>
			<p>Precio: <span><?php echo $datos['precio']; ?></span></p>
			<p>Existencias: <span><?php echo $datos['existencia']; ?></span></p>

			<form action="" method="post">
				<input type="hidden" name="codproducto" value="<?php echo $datos['codproducto']; ?>">
				<a href="lista_productos.php" class="btn_cancel"><i class="fas fa-ban"></i> Cancelar</a>
				<button type="submit" class="btn_ok"><i class="far fa-trash-alt"></i> Eliminar</button>
			</form>

		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>