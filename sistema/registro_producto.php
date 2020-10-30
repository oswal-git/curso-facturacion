<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	include_once "../conexion.php"; 
	include_once("includes/funciones.php");

	if ( $_SESSION['rol'] != 1 &&  $_SESSION['rol'] != 2 ) 
	{
		header("location: ./");
	}

	// Lista de proveedores
	$sql = "SELECT * FROM proveedor ORDER BY proveedor ASC";
	$query_prov = mysqli_query($conexion, $sql );
	$resultado_prov = mysqli_num_rows($query_prov);
	$opciones_prov = '';
	
	if ( $resultado_prov > 0 )
	{
		while ( $prov = mysqli_fetch_array($query_prov) )
		{
			$opciones_prov .= '<option value="'.$prov['codproveedor'].'">' . $prov['proveedor'] . '</option>';
		}
	}	
	// Fin lista de proveedores

	if ( !empty($_POST))
	{
		$alerta ="";
		if ( empty($_POST['proveedor']) || empty($_POST['producto']) || empty($_POST['precio']) || empty($_POST['cantidad'] ))
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$proveedor = $_POST['proveedor'];
			$descripcion = $_POST['producto'];
			$precio = $_POST['precio'];
			$existencia = $_POST['cantidad'];
			$usuario_id = $_SESSION['idUsuario'];

			$foto = $_FILES['foto'];
			$nombre_foto = $foto['name'];
			$tipo_foto = $foto['type'];
			$url_temp_foto = $foto['tmp_name'];
			$tamano_foto = $foto['size'];

			$imgProducto = 'img_producto.jpg';

			if ( $nombre_foto != "" )
			{
				$destino = 'img/uploads/';
				$img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
				$imgProducto = $img_nombre . '.jpg';
				$src = $destino.$imgProducto;
			}

			$sql = "SELECT * FROM producto WHERE descripcion = '$descripcion' and proveedor = '$proveedor' ";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El producto de ese proveedor ya existe.</p>';
			} else
			{
				$sql = "INSERT INTO producto (proveedor, 
										descripcion, 
										precio, 
										existencia, 
										usuario_id,
										foto) 
				        VALUES ('$proveedor', 
				        		 '$descripcion', 
				        		 '$precio', 
				        		 '$existencia', 
				        		 '$usuario_id',
				        		 '$imgProducto')";
				// echo $sql . "<br>";
				$query_insert = mysqli_query($conexion, $sql);
				if ( $query_insert )
				{
					if ( $nombre_foto != "" )
					{
						echo $url_temp_foto . "<br>";
						move_uploaded_file($url_temp_foto, $src);
					}					
					$alerta = '<p class="msg_ok">Producto almacenado correctamente.</p>';
				} else
				{
					$alerta = '<p class="msg_error">Error al guardar el producto.</p>';
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
	<title>Registro Proveedor</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="fas fa-cubes"></i> Registro Producto</h1>
			<hr>
			<div class="alerta"><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>

			<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">

				<label for="proveedor">Proveedor</label>
				<select name="proveedor" id="proveedor">
					<?php echo $opciones_prov; ?>
				</select>				
				<label for="producto">Producto</label>
				<input type="text" name="producto" id="producto" placeholder="Producto">

				<label for="precio">Precio</label>
				<input type="number" name="precio" id="precio" placeholder="Precio">

				<label for="cantidad">Cantidad</label>
				<input type="number" name="cantidad" id="cantidad" placeholder="Cantidad">

				<div class="img_foto">
					<label for="foto">Foto</label>
					<div class="prevFoto">
						<span class="delFoto noBloque">X</span>
						<label for="foto"></label>
					</div>
					<div class="upimg">
						<input type="file" name="foto" id="foto">
					</div>
					<div id="form_alerta"></div> 
				</div>

				<button type="submit" class="btn_save"><i class="far fa-save fa-lg"></i> Guardar producto</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>
