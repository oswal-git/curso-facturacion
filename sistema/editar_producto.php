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

	// Lista de proveedores
	$sql = "SELECT * FROM proveedor ORDER BY proveedor ASC";
	// echo $sql . "<br>";
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
	// Fin lista de productoes

	if ( !empty($_POST))
	{
		// print_r($_POST); echo "<br>";
		// print_r($_FILES); echo "<br>";
		// exit;
		$alerta ="";
		if ( empty($_POST['producto']) || empty($_POST['producto']) || empty($_POST['precio']) || !is_numeric($_POST['cantidad'] ))
		{
			$alerta = '<p class="msg_error">Todos los campos son obligatorios</p>';
		} else 
		{
			$codproducto = $_POST['codproducto'];
			$proveedor = $_POST['proveedor'];
			$descripcion = $_POST['producto'];
			$precio = $_POST['precio'];
			$existencia = $_POST['cantidad'];
			$usuario_id = $_POST['usuario'];
			$estado = ( empty($_POST['estado']) ? "" : $_POST['estado']);

			$imgProducto = $_POST['foto_actual'];
			$imgRemove = $_POST['foto_remove'];
			$nombre_foto = "";

			if ( !empty($_FILES) )
			{
				$foto = $_FILES['foto'];
				$nombre_foto = $foto['name'];
				$tipo_foto = $foto['type'];
				$url_temp_foto = $foto['tmp_name'];
				$tamano_foto = $foto['size'];
				echo "nombre_foto: " . $nombre_foto . "<br>";
			}
			if ( $nombre_foto != "" )
			{
				$destino = 'img/uploads/';
				$img_nombre = 'img_'.md5(date('d-m-Y H:m:s'));
				$imgProducto = $img_nombre . '.jpg';
				$src = $destino.$imgProducto;
			}  else
			{
				if ( $_POST['foto_actual'] != $_POST['foto_remove'] )
				{
					$imgProducto = 'img_producto.jpg';
				}
			}


			$sql = "SELECT * FROM producto WHERE descripcion = '$descripcion' and proveedor = '$proveedor' AND codproducto != $codproducto ";
			// echo $sql . "<br>";
			$query = mysqli_query($conexion, $sql );
			$resultado = mysqli_fetch_array($query);
			if ( $resultado > 0 )
			{
				$alerta = '<p class="msg_error">El producto ya existe para ese proveedor.</p>';
			} else
			{
				$sql = "UPDATE producto 
				        SET proveedor = '$proveedor', 
				            descripcion = '$descripcion', 
				            precio = '$precio', 
				            existencia = '$existencia',
				            foto = '$imgProducto',";
				$sql .= ( $estado == "" ? "" : " estatus = '$estado',");
				$sql .= "    usuario_id = '$usuario_id'
				         WHERE codproducto = $codproducto";
				// echo $sql . "<br>";
				// exit;
				$query_update = mysqli_query($conexion, $sql);
				if ( $query_update )
				{
					if ( ( $nombre_foto != '' && ( $_POST['foto_actual'] != 'img_producto.jpg' )) || ( $_POST['foto_actual'] != $_POST['foto_remove'] ) )
					{
						unlink('img/uploads/' . $_POST['foto_actual']);
					}

					if ( $nombre_foto != "" )
					{
						// echo $url_temp_foto . "<br>";
						move_uploaded_file($url_temp_foto, $src);
					}						

					$alerta = '<p class="msg_ok">Producto actualizado correctamente.</p>';
				} else
				{
					$alerta = '<p class="msg_error">Error al actualizar el producto.</p>';
				}
			}

		}
	}

	// Recuperar datos
	// 
	// echo $_GET['id']; exit;
	if ( empty($_REQUEST['id']) )
	{
		header('Location: buscar_producto.php');
	} else
	{
		$codproducto = $_REQUEST['id'];

		if ( !empty($_REQUEST['estado']) ) 
		{
			if ( $_SESSION['rol'] != 1 ) 
			{
				header("location: ./");
			}
		}
		
		$sql = "SELECT p.codproducto, p.proveedor, p.descripcion, p.precio, p.existencia, p.usuario_id, p.estatus, p.foto, u.idusuario , u.usuario, pr.proveedor as nom_proveedor
		        FROM producto p 
		        LEFT OUTER JOIN usuario u 
		           ON ( p.usuario_id = u.idusuario )
		        LEFT OUTER JOIN proveedor pr 
		           ON ( p.proveedor = pr.codproveedor )";
		$sql .= ( empty($_REQUEST['estado']) ? " WHERE p.estatus != 0" : "  WHERE p.estatus = 0");
	     $sql .= "  AND codproducto = '$codproducto'";
		// echo $sql . "<br>";
		// exit;
		$query = mysqli_query($conexion, $sql );
		$resultado = 0;
		$resultado = mysqli_num_rows($query);
		if ( $resultado != 1 )
		{
			header('Location: buscar_producto.php');
		}
		$datos = mysqli_fetch_array($query);

		$foto = '';
		$classRemove = 'noBloque';	

		if ( $datos['foto'] != 'img_producto.jpg'){
			// $foto = 'img/uploads/'. $datos['foto'];
			$foto = '<img id="img" src="' . 'img/uploads/' . $datos['foto'] . '" alt="' . $datos['descripcion'] .'">';
			$classRemove = '';
		} else
		{
			$foto = '';
			// $foto = 'img/'. $datos['foto'];
		}

		$activar = "";

		$h1 = ' Actualizar producto';
		if ( !empty($_REQUEST['estado']) ) 
		{
			$h1 = ' Activar producto';
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
	<title>Actualizar Producto</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">
		<div class="form-registro">
			<h1><i class="far fa-edit"></i><?php echo $h1; ?></h1>
			<div class="alerta"><?php echo ( empty($alerta) ? "" : $alerta ); ?></div>

			<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">

				<input type="hidden" name="codproducto" value="<?php echo $datos['codproducto']; ?>">
				<input type="hidden" id="foto_actual" name="foto_actual" value="<?php echo $datos['foto']; ?>">
				<input type="hidden" id="foto_remove" name="foto_remove" value="<?php echo $datos['foto']; ?>">

				<label for="proveedor">Proveedor</label>
				<select name="proveedor" id="proveedor" class="noPrimerItem">
					<option value="<?php echo $datos['proveedor']; ?>" select><?php echo $datos['nom_proveedor']; ?></option>
					<?php echo $opciones_prov; ?>
				</select>				

				<label for="producto">Producto</label>
				<input type="text" name="producto" id="producto" placeholder="Producto" value ="<?php echo $datos['descripcion']; ?>">

				<label for="precio">Precio</label>
				<input type="number" step="any" name="precio" id="precio" placeholder="Precio" value ="<?php echo $datos['precio']; ?>">

				<label for="cantidad">Cantidad</label>
				<input type="number" name="cantidad" id="cantidad" placeholder="Cantidad" value ="<?php echo $datos['existencia']; ?>">

				<?php echo $activar; ?>

				<div class="img_foto">
					<label for="foto">Foto</label>
					<div class="prevFoto">
						<span class="delFoto <?php echo $classRemove; ?>">X</span>
						<label for="foto"></label>
						<?php echo $foto; ?>
					</div>
					<div class="upimg">
						<input type="file" name="foto" id="foto">	
					</div>
					<div id="form_alerta"></div> 
				</div>

				<label for="usuario">Usuario</label>
				<select name="usuario" id="usuario" class="noPrimerItem">
					<option value="<?php echo $datos['idusuario']; ?>" select><?php echo $datos['usuario']; ?></option>
					<?php echo $opciones_usu; ?>
				</select>

				<button type="submit" class="btn_save"><i class="far fa-edit"></i> Actualizar producto</button>

			</form>
		</div>
	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>