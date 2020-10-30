<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	include_once "../conexion.php"; 
	
	$proveedor_busq = empty($_REQUEST['proveedor']) 
				  	? empty($_REQUEST['busq_prov']) 
				  		? "" 
			  			: strtolower($_REQUEST['busq_prov']) 
			  		: strtolower($_REQUEST['proveedor']);
	$busqueda = empty($_REQUEST['busqueda']) ? "" : strtolower($_REQUEST['busqueda']);
	$seleccion = empty($_REQUEST['cuales']) ? "activos" : $_REQUEST['cuales'];

	// Lista de proveedores
	$sql = "SELECT * FROM proveedor ORDER BY proveedor ASC";
	$query_prov = mysqli_query($conexion, $sql );
	$resultado_prov = mysqli_num_rows($query_prov);
	$opciones_prov = '';
	
	if ( $resultado_prov > 0 )
	{
		if ( $proveedor_busq == 0 )
		{
			$opciones_prov .= '<option value="" selected> Proveedores</option>';
		} else
		{
			$opciones_prov .= '<option value=""> Proveedores</option>';
		}

		while ( $prov = mysqli_fetch_array($query_prov) )
		{
			if ( $proveedor_busq == $prov['codproveedor'] )
			{
				$opciones_prov .= '<option value="'.$prov['codproveedor'].'" selected>' . $prov['proveedor'] . '</option>';
			} else
			{
				$opciones_prov .= '<option value="'.$prov['codproveedor'].'">' . $prov['proveedor'] . '</option>';
			}
		}
	}	
	// Fin lista de proveedores
	// 	

	$sql_registros = "SELECT sum(case when estatus = 0 then 0 else 1 end ) as total_registros_ok
				        , sum(case when estatus = 0 then 1 else 0 end ) as total_registros_ko 
				   FROM ( SELECT p.codproducto
						     , p.proveedor
						     , p.descripcion
						     , p.precio
						     , p.existencia
						     , p.usuario_id
						     , p.foto
						     , p.fechaalta
						     , p.estatus
						     , u.usuario
						     , pr.proveedor as nom_proveedor 
				          FROM producto p 
				          LEFT OUTER JOIN usuario u 
				             ON ( p.usuario_id = u.idusuario ) 
				          LEFT OUTER JOIN proveedor pr 
				             ON ( p.proveedor = pr.codproveedor )
						WHERE LOWER(CONCAT('P:', p.codproducto, p.proveedor, p.descripcion, p.precio, p.existencia, p.usuario_id, p.foto, u.usuario  , pr.proveedor )) LIKE '%$busqueda%' ";
	$sql_registros .= ( $proveedor_busq == "" ? "" : "		  AND pr.codproveedor = $proveedor_busq ");
	$sql_registros .= "		        ) A ";

	// echo $sql_registros . '<br>';
	$query_registros = mysqli_query($conexion, $sql_registros);
	$res_registros = mysqli_fetch_array($query_registros);

	$total_registros_ok = $res_registros['total_registros_ok'];
	$total_registros_ko = $res_registros['total_registros_ko'];
	$total_registros = ($seleccion == "inactivos" ? $total_registros_ko : $total_registros_ok );

	$reg_por_pagina = 10;

	// Botón de selección de activos/inactivos
	$eleccion_tg  = "";
	// if ( $total_registros_ko )
	// {
		$eleccion_tg  = '	<div class="form_select">';
		$eleccion_tg .= '		<a href="?cuales='.($seleccion == "inactivos" ? "activos" : "inactivos" );
		$eleccion_tg .= '&busqueda=' . $busqueda;
		$eleccion_tg .= '&busq_prov=' . $proveedor_busq .'"';
		$eleccion_tg .= '		   class="btn_select ' . ( $total_registros_ko ? "" : "noBloque" );
		$eleccion_tg .= '        ">';
		$eleccion_tg .=  		($seleccion == "inactivos" ? "Ver activos" : "Ver inactivos");
		$eleccion_tg .= '		</a>';
		$eleccion_tg .= '	</div>';		
	// }

	$seleccion_pag = ($seleccion == "inactivos" ? "&cuales=inactivos" : "" );

	if ( empty($_REQUEST['pagina']))
	{
		$pagina = 5;
	} else
	{
		$pagina = $_REQUEST['pagina'];
	}

	$total_paginas = ceil($total_registros / $reg_por_pagina);
	if ( $pagina < 1 ) $pagina = 1;
	if ( $pagina > $total_paginas ) $pagina = $total_paginas;
	$desde = ($pagina - 1) * $reg_por_pagina;
	// echo $desde . '<br>';
	// echo $total_registros . '<br>';
	// echo $total_paginas . '<br>';
	$paginadores = "";
	if ( $total_paginas > 1 )
	{
		for ($i=1; $i <= $total_paginas; $i++) 
		{ 
			if ( $i == $pagina )
			{
				$paginadores .= '<li class="pageSelected">'.$i.'</li>';
			} else
			{
				$paginadores .= '<li><a href="?pagina='.$i.'&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq .'">'.$i.'</a></li>';
			}					 	
		}						
	}

	$sql = "SELECT p.codproducto
			   , p.proveedor
			   , p.descripcion
			   , p.precio
			   , p.existencia
			   , p.usuario_id
			   , p.foto
			   , p.fechaalta
			   , p.estatus
			   , u.usuario
			   , pr.proveedor as nom_proveedor 
	        FROM producto p 
	        LEFT OUTER JOIN usuario u 
	           ON ( p.usuario_id = u.idusuario ) 
	        LEFT OUTER JOIN proveedor pr 
	           ON ( p.proveedor = pr.codproveedor )";
     $sql .= ($seleccion == "inactivos" 
     	 ? " WHERE p.estatus = 0 " 
     	 : " WHERE p.estatus != 0 " );
	$sql .= ( $proveedor_busq == "" 
		 ? "" 
		 : "	AND pr.codproveedor = $proveedor_busq ");
	$sql .= " AND LOWER(CONCAT('P:', p.codproducto, p.proveedor, p.descripcion, p.precio, p.existencia, p.usuario_id, p.foto, u.usuario, pr.proveedor )) LIKE '%$busqueda%'
	        ORDER BY descripcion ASC
	        LIMIT $desde, $reg_por_pagina";					           
	// echo $sql . '<br>';
	$query = mysqli_query($conexion, $sql);
	$resultado = 0;
	if ( $query )
	{
		$resultado = mysqli_num_rows($query);
	}

	$tabla = "";
	if ( $resultado > 0 )
	{
		while ( $datos = mysqli_fetch_array($query) )
		{
			$formato = 'Y-m-d H:i:s';
			$fecha = DateTime::createFromFormat($formato, $datos['fechaalta']);

			if ( $datos['estatus'] )
			{
				$estado  = '		<i class="fas fa-check-circle fa-lg" style="color: green;"></i></a>';
				$estado_accion  = '';
			} else
			{
				$estado  = '		<i class="fas fa-minus-circle fa-lg" style="color: red;"></a>';
				$estado_accion = '		<a class="link_activa act_producto" producto="'.$datos['codproducto'].'" href="#"><i class="fas fa-check-circle"></i> Activar</a>';
				// $estado_accion = '		<a href="editar_producto.php?id='.$datos['codproducto'].'&estado=activar" class="link_activa"><i class="fas fa-check-circle" style="color: green;"></i> Activar</a>';
			}

			if ( $datos['foto'] != 'img_producto.jpg')
			{
				$foto = 'img/uploads/'. $datos['foto'];
			} else
			{
				$foto = 'img/'. $datos['foto'];
			}

			$fila  = '<tr class="fila'.$datos['codproducto'].'">';
			$fila .= '	<td style="text-align: center;">'.$datos['codproducto'].'</td>';
			$fila .= '	<td>'.$datos['descripcion'].'</td>';
			$fila .= '	<td>'.$datos['nom_proveedor'].'</td>';
			$fila .= '	<td class="celPrecio" style="text-align: right;">'.$datos['precio'].'</td>';
			$fila .= '	<td class="celExistencia" style="text-align: right;">'.$datos['existencia'].'</td>';
			$fila .= '	<td style="text-align: center;">'.$fecha->format("d-m-Y").'</td>';
			$fila .= '	<td style="text-align: center;">'.$estado.'</td>';
			$fila .= '	<td>'.$datos['usuario'].'</td>';
			$fila .= '	<td class="img_producto"><img src="'.$foto.'" alt="'.$datos['descripcion'].'"></td>';
			$fila .= '	<td class="acciones_producto">';

			if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 )
			{
				if ( $estado_accion == "" )
				{
					$fila .= '		<a class="link_add add_producto" producto="'.$datos['codproducto'].'" href="#"><i class="fas fa-plus"></i> Agregar</a>';
					$fila .= '		|';
					$fila .= '		<a href="editar_producto.php?id='.$datos['codproducto'].'" class="link_edit"><i class="far fa-edit"></i> Editar</a>';
					$fila .= '		|';
					$fila .= '		<a class="link_del del_producto" producto="'.$datos['codproducto'].'" href="#"><i class="far fa-trash-alt"></i> Eliminar</a>';
				} else 
				{
					if ( $_SESSION['rol'] == 1 ) 
					{
						$fila .= $estado_accion;
					}
				}
			}
			$fila .= '	</td>';
			$fila .= '</tr>';
			$tabla .= $fila;
		}
	}	
	mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php include_once("includes/scripts.php") ?>
	<title>Lista de Productos</title>
</head>
<body>
	<?php include_once("includes/cabecera.php") ?>
	<section id="contenedor">

		<h1><i class="far fa-building fa-2x"></i> Lista de Productos</h1>
		<a href="registro_producto.php" class="btn_new"><i class="fas fa-user-plus"></i> Crear producto</a>

		<form action="buscar_producto.php" method="get" class="form_search">
			<input  type="hidden" id="busq_prov" name="busq_prov" value="<?php echo $proveedor_busq; ?>"> 
			<input  type="hidden" id="cuales"    name="cuales"    value="<?php echo $seleccion; ?>"> 
			<input  type="text"   id="busqueda"  name="busqueda"  value="<?php echo $busqueda; ?>" placeholder="Buscar"> 
			<button type="submit" class="btn_search"><i class="fas fa-search"></i></button>			
		</form>

		<?php echo $eleccion_tg; ?>

		<div class="contenedorTabla tablaProducto">
			<table>
				<!-- <caption>table title and/or explanatory text</caption> -->
				<thead>
					<tr>
						<th>ID</th>
						<th>Producto</th>
						<!-- <th>Proveedor</th> -->
						<th>
							<select name="proveedor" id="buscar_proveedor">
								<?php echo $opciones_prov; ?>
							</select>								
						</th>
						<th>Precio</th>
						<th>Existencias</th>
						<th>Fecha de Alta</th>
						<th>Estado</th>
						<th>Usuario</th>
						<th>Foto</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php echo $tabla; ?>
				</tbody>
			</table>
		</div>
		<div class="paginador">
			<ul>
				<?php echo ( $pagina <= 1 ? '' : '<li><a href="?pagina=1&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq .'"><i class="fas fa-step-backward"></i></a></li>' .
				                                 '<li><a href="?pagina='.($pagina - 1).'&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq .'"><i class="fas fa-caret-left fa-lg"></i></a></li>'); ?>
				<?php echo $paginadores; ?>
				<?php echo ( $pagina  >= $total_paginas ? '' : '<li><a href="?pagina='.($pagina + 1).'&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq .'"><i class="fas fa-caret-right fa-lg"></i></a></li>' .
				                                               '<li><a href="?pagina='. $total_paginas . '&busqueda='.$busqueda.$seleccion_pag.'&busq_prov=' . $proveedor_busq .'"><i class="fas fa-step-forward"></i></a></li>'); ?>
			</ul>
		</div>

	</section>
	<?php include_once("includes/pie.php") ?>
</body>
</html>