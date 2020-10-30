<?php 
	if ( ( session_status() === PHP_SESSION_ACTIVE ? FALSE : TRUE ) ) session_start();
	include_once "../conexion.php"; 
	include_once "includes/funciones.php";

	// print_r($_POST);
	// exit;

	if ( !empty($_POST))
	{
		// Extraer datos prodctos
		if ( $_POST['accion'] == 'infoProducto' )
		{
			$producto_id = $_POST['producto'];
			$activar = ( empty($_POST['evento']) ? false : ( $_POST['evento'] == 'activaProducto' ? true : false ) );

			$sql_registros = "SELECT p.codproducto, p.descripcion, p.existencia, p.precio
				             FROM producto p 
			                  WHERE p.codproducto = $producto_id "; 
			 $sql_registros .= ( $activar ? "" : "                   AND p.estatus != 0 " );
			// echo $sql_registros . '<br>';
			$query_registros = mysqli_query($conexion, $sql_registros);
			$res_registros = 0;
			$res_registros = mysqli_num_rows($query_registros);
			mysqli_close($conexion);

			if ( $res_registros > 0 ){
				$datos = mysqli_fetch_assoc($query_registros);
				echo json_encode($datos, JSON_UNESCAPED_UNICODE);
				exit;
			}

			echo "error";
		}

		// Agrega datos prodctos a entradas
		if ( $_POST['accion'] == 'addProducto' )
		{
			if ( !empty($_POST['producto_id']) ||  !empty($_POST['cantidad']) || !empty($_POST['precio']) )
			{
				$producto_id = $_POST['producto_id'];
				$cantidad = $_POST['cantidad'];
				$precio = $_POST['precio'];
				$usuario_id = $_SESSION['idUsuario'];

				$sql_entradas = "INSERT INTO entradas ( codproducto, cantidad, precio, usuario_id )
					             VALUES  ( $producto_id, $cantidad, $precio, $usuario_id ) ";
				// echo $sql_entradas . '<br>';
				$query_entradas = mysqli_query($conexion, $sql_entradas);
				
				if ( $query_entradas )
				{
					//Ejecutar procedimiento almacenado
					$query_upd = mysqli_query($conexion, "CALL actualizar_precio_producto($cantidad, $precio, $producto_id )");
					$resultado_pro = mysqli_num_rows($query_upd);

					if ( $resultado_pro > 0 )
					{
						$datos = mysqli_fetch_assoc($query_upd);
						$datos['producto_id'] = $producto_id;
						echo json_encode($datos, JSON_UNESCAPED_UNICODE);
						// exit;							
					} else
					{
						echo "error";
					}
				}
				mysqli_close($conexion);
			} else
			{
				echo "error";
			}
			exit;
		}

		// Agrega datos prodctos a entradas
		if ( $_POST['accion'] == 'actProducto' )
		{
			if ( !empty($_POST['producto_id']) )
			{
				$producto_id = $_POST['producto_id'];
				$cantidad = $_POST['cantidad'];
				$precio = $_POST['precio'];
				$usuario_id = $_SESSION['idUsuario'];

				$sql = "UPDATE producto SET estatus = 1 WHERE codproducto = $producto_id";
				// echo $sql . '<br>'; exit;
				$query_activa = mysqli_query($conexion, $sql);
				// print_r($query_activa) . '<br>'; exit;
				
				if ( $query_activa )
				{
					// echo "ok";
					echo json_encode('ok', JSON_UNESCAPED_UNICODE);
				} else
				{
					echo "error";
				}
				mysqli_close($conexion);
			} else
			{
				echo "error";
			}
			exit;
		}

		// Borra productos
		if ( $_POST['accion'] == 'delProducto' )
		{
			if ( !empty($_POST['producto_id']) )
			{
				$producto_id = $_POST['producto_id'];
				$cantidad = ( (float)$_POST['cantidad'] > 0 ? ( -1 * (float)$_POST['cantidad'] ) : 0 );
				$precio = $_POST['precio'];
				$usuario_id = $_SESSION['idUsuario'];
				// echo 'producto_id: ' . $producto_id . '<br>';
				// echo 'cantidad: ' . $cantidad . '<br>';
				// echo '----------: ' . '<br>';

				$sql_valida = "SELECT count(*) as inactivo FROM producto WHERE codproducto = $producto_id AND estatus = 1";
				$query_valida = mysqli_query($conexion, $sql_valida);

				if ( $query_valida )
				{
					$resultado = mysqli_fetch_assoc($query_valida);
					// echo 'resultado: ' . $resultado['inactivo'] . '<br>'; exit;
					if ( $resultado['inactivo'] != 1 )
					{
						echo "error";
						exit;
					}
				}

				$sql = "UPDATE producto SET estatus = 0 WHERE codproducto = $producto_id";
				// echo $sql . '<br>';
				$query_delete = mysqli_query($conexion, $sql);
				// print_r($query_delete) . '<br>';
				
				if ( $query_delete )
				{
					$sql_entradas = "INSERT INTO entradas ( codproducto, cantidad, precio, usuario_id )
						             VALUES  ( $producto_id, $cantidad, $precio, $usuario_id ) ";
					// echo $sql_entradas . '<br>';
					$query_entradas = mysqli_query($conexion, $sql_entradas);
					
					if ( $query_entradas )
					{

						//Ejecutar procedimiento almacenado
						$query_upd = mysqli_query($conexion, "CALL actualizar_precio_producto($cantidad, $precio, $producto_id )");
						// echo 'query_upd: ' . '<br>';
						// var_dump($query_upd) . '<br>';
						// echo '----------: ' . '<br>';
						$resultado_pro = mysqli_num_rows($query_upd);
						// echo 'resultado_pro: ' . '<br>';
						// var_dump($resultado_pro) . '<br>';
						// echo '----------: ' . '<br>';

						if ( $resultado_pro > 0 )
						{
							$datos = mysqli_fetch_assoc($query_upd);
							$datos['producto_id'] = $producto_id;
							// echo 'datos: ' . '<br>';
							// print_r($datos);
							echo json_encode($datos, JSON_UNESCAPED_UNICODE);
							// exit;							
						} else
						{
							echo "error";
						}
					}
				}
				mysqli_close($conexion);
			} else
			{
				echo "error";
			}
			exit;
		}

		// Buscar cliente
		if ( $_POST['accion'] == 'buscarCliente' )
		{
			if ( !empty($_POST['cliente']) )
			{
				// $nif = '%' . $_POST['cliente'] . '%' ;
				$nif = $_POST['cliente'];

				$sql_cliente = "SELECT c.*
				                FROM cliente c 
			                     WHERE c.nit like '$nif' 
			                       AND c.estatus = 1 "; 
				// echo $sql_cliente . '<br>';
				$query_cliente = mysqli_query($conexion, $sql_cliente);
				mysqli_close($conexion);
				$res_cliente = 0;
				$res_cliente = mysqli_num_rows($query_cliente);
				// echo $res_cliente . '<br>';

				$datos = 0;
				if ( $res_cliente > 0 )
				{
					$datos = mysqli_fetch_assoc($query_cliente);
				}
				echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			}
		}

		// registrar clientes - Ventas
		if ( $_POST['accion'] == 'addCliente' )
		{
			$nif = $_POST['nif_cliente'];
			$nombre = $_POST['nom_cliente'];
			$telefono = $_POST['tel_cliente'];
			$direccion = $_POST['dir_cliente'];
			$usuario_id = $_SESSION['idUsuario'];

			if ( validateNif($nif) || validateNie($nif) || validateCif ($nif) )
			{
				$sql = "INSERT INTO cliente (nit, nombre, telefono, direccion, usuario_id) VALUES ('$nif', '$nombre', '$telefono', '$direccion', '$usuario_id')";
				// echo $sql . "<br>";
				$query_insert = mysqli_query($conexion, $sql);
				if ( $query_insert )
				{
					$codCliente = mysqli_insert_id($conexion);
					// $msg = $codCliente;
					$retorno['msg'] = $codCliente;
					$retorno['desc'] = 'Alta correcta';
				} else
				{
					// $msg = 'error';
					$retorno['msg'] = 'error';
					$retorno['desc'] = 'Error en el alta.';
				}
				// echo $retorno['msg'];
				// exit;
			}
			else
				{
					$retorno['msg'] = 'error';
					$retorno['desc'] = 'NIF erróneo.';
					// echo $retorno['msg'];
					// echo json_encode($retorno);
					// exit;
				}
			
			echo json_encode($retorno);
		}

		// Añadir detalle producto - Ventas
		if ( $_POST['accion'] == 'addProductoDetalle' )
		{
			if ( empty($_POST['producto']) || empty($_POST['cantidad'])  )
			{
				$datos['resultado'] = 'error';
				$datos['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$codproducto = $_POST['producto'];
					$cantidad = $_POST['cantidad'];
					$usuario_id = $_SESSION['idUsuario'];
					$token_usuario = md5($_SESSION['idUsuario']);

					$sql_iva = "SELECT iva
				                 FROM configuracion c 
				                 WHERE id = 1 "; 
					// echo $sql_iva . "\n\r";
					$query_iva = mysqli_query($conexion, $sql_iva);
					$res_iva = 0;
					$res_iva = mysqli_num_rows($query_iva);
					// echo $res_iva . "\n\r";

					//Ejecutar procedimiento almacenado
					$call = "CALL add_detalle_temp($codproducto, $cantidad, '$token_usuario' )";
					// echo 'call: ' . $call . "\n\r";
					$query_temp = mysqli_query($conexion, $call);
					mysqli_close($conexion);
					$resultado_tmp = 0;
					$resultado_tmp = mysqli_num_rows($query_temp);
					// echo 'resultado_tmp:' . "\n\r";
					// echo 'nº resultado_tmp: ' . $resultado_tmp . "\n\r";

					$datos_detalle_tmp = 0;

					$detalle_tabla = '';
					$sub_total = 0;
					$iva = 0;
					$total = 0;

					if ( $resultado_tmp > 0 )
					{
						if ( $res_iva > 0 )
						{
							$info_iva = mysqli_fetch_assoc($query_iva);
							$iva = $info_iva['iva'];
							$detalle_tabla  = '';
							$detalle_totales  = '';

							while ( $datos_detalle_tmp = mysqli_fetch_assoc($query_temp) )
							{
								$precio_total = round($datos_detalle_tmp['cantidad'] * $datos_detalle_tmp['precio_venta'], 2);
								$sub_total = round($sub_total * $precio_total, 2);
								$total = round($total + $precio_total, 2);

								$detalle_tabla .= '<tr>';
								$detalle_tabla .= '	<td>' . $datos_detalle_tmp['codproducto'] . '</td>';
								$detalle_tabla .= '	<td colspan="2">' . $datos_detalle_tmp['descripcion'] . '</td>';
								$detalle_tabla .= '	<td  class="textcenter">' . $datos_detalle_tmp['cantidad'] . '</td>';
								$detalle_tabla .= '	<td  class="textright">' . $datos_detalle_tmp['precio_venta'] . '</td>';
								$detalle_tabla .= '	<td  class="textright">' . $precio_total . '</td>';
								$detalle_tabla .= '	<td class="">';
								$detalle_tabla .= '		<a href="#" class="link_del" onclick="event.preventDefault(); del_producto_detalle(' . $datos_detalle_tmp['correlativo'] . ');"><i class="fas fa-trash-alt"></i></a>';
								$detalle_tabla .= '	</td>';
								$detalle_tabla .= '<tr>';

							}

							$total_sin_iva = round($total / ( 1 + $iva / 100 ), 2);
							$impuesto = round($total - $total_sin_iva, 2);
							//$total = round($total_sin_iva + $impuesto, 2);

							$detalle_totales  = '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">SUBTOTAL.Q</td>';
							$detalle_totales .= '	<td class="textright">' . $total_sin_iva . '</td>';
							$detalle_totales .= '</tr>';
							$detalle_totales .= '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">IVA (21 %)</td>';
							$detalle_totales .= '	<td class="textright">' . $impuesto . '</td>';
							$detalle_totales .= '</tr>				';
							$detalle_totales .= '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">TOTAL.Q</td>';
							$detalle_totales .= '	<td class="textright">' . $total . '0</td>';
							$detalle_totales .= '</tr>								';
		
							$datos['detalle'] = $detalle_tabla;
							$datos['totales'] = $detalle_totales;

							$datos['resultado'] = 'ok';
							$datos['error'] = '';
						}
						else
							{
								$datos['resultado'] = 'error';
								$datos['error'] = 'Falta el IVA';								
							}
					} else
						{
							$datos['resultado'] = 'error';
							$datos['error'] = 'No hay registros detalle';
						}
				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Eliminar detalle producto - Ventas
		if ( $_POST['accion'] == 'delProductoDetalle' )
		{
			if ( empty($_POST['id_detalle']) )
			{
				$datos['resultado'] = 'error';
				$datos['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$id_detalle = $_POST['id_detalle'];
					$token_usuario = md5($_SESSION['idUsuario']);

					$sql_iva = "SELECT iva
				                 FROM configuracion c 
				                 WHERE id = 1 "; 
					// echo $sql_iva . "\n\r";
					$query_iva = mysqli_query($conexion, $sql_iva);
					$res_iva = 0;
					$res_iva = mysqli_num_rows($query_iva);
					// echo $res_iva . "\n\r";

					//Ejecutar procedimiento almacenado
					$call = "CALL del_detalle_temp($id_detalle, '$token_usuario' )";
					// echo 'call: ' . $call . "\n\r";
					$query_temp = mysqli_query($conexion, $call);
					mysqli_close($conexion);
					$resultado_tmp = 0;
					$resultado_tmp = mysqli_num_rows($query_temp);
					// echo 'resultado_tmp:' . "\n\r";
					// echo 'nº resultado_tmp: ' . $resultado_tmp . "\n\r";

					$datos_detalle_tmp = 0;

					$detalle_tabla = '';
					$sub_total = 0;
					$iva = 0;
					$total = 0;

					if ( $resultado_tmp > 0 )
					{
						if ( $res_iva > 0 )
						{
							$info_iva = mysqli_fetch_assoc($query_iva);
							$iva = $info_iva['iva'];
							$detalle_tabla  = '';
							$detalle_totales  = '';

							while ( $datos_detalle_tmp = mysqli_fetch_assoc($query_temp) )
							{
								$precio_total = round($datos_detalle_tmp['cantidad'] * $datos_detalle_tmp['precio_venta'], 2);
								$sub_total = round($sub_total * $precio_total, 2);
								$total = round($total + $precio_total, 2);

								$detalle_tabla .= '<tr>';
								$detalle_tabla .= '	<td>' . $datos_detalle_tmp['codproducto'] . '</td>';
								$detalle_tabla .= '	<td colspan="2">' . $datos_detalle_tmp['descripcion'] . '</td>';
								$detalle_tabla .= '	<td  class="textcenter">' . $datos_detalle_tmp['cantidad'] . '</td>';
								$detalle_tabla .= '	<td  class="textright">' . $datos_detalle_tmp['precio_venta'] . '</td>';
								$detalle_tabla .= '	<td  class="textright">' . $precio_total . '</td>';
								$detalle_tabla .= '	<td class="">';
								$detalle_tabla .= '		<a href="#" class="link_del" onclick="event.preventDefault(); del_producto_detalle(' . $datos_detalle_tmp['correlativo'] . ');"><i class="fas fa-trash-alt"></i></a>';
								$detalle_tabla .= '	</td>';
								$detalle_tabla .= '<tr>';

							}

							$total_sin_iva = round($total / ( 1 + $iva / 100 ), 2);
							$impuesto = round($total - $total_sin_iva, 2);
							//$total = round($total_sin_iva + $impuesto, 2);

							$detalle_totales  = '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">SUBTOTAL.Q</td>';
							$detalle_totales .= '	<td class="textright">' . $total_sin_iva . '</td>';
							$detalle_totales .= '</tr>';
							$detalle_totales .= '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">IVA (21 %)</td>';
							$detalle_totales .= '	<td class="textright">' . $impuesto . '</td>';
							$detalle_totales .= '</tr>				';
							$detalle_totales .= '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">TOTAL.Q</td>';
							$detalle_totales .= '	<td class="textright">' . $total . '0</td>';
							$detalle_totales .= '</tr>								';
		
							$datos['detalle'] = $detalle_tabla;
							$datos['totales'] = $detalle_totales;

							$datos['resultado'] = 'ok';
							$datos['error'] = '';
						}
						else
							{
								$datos['resultado'] = 'error';
								$datos['error'] = 'Falta el IVA';								
							}
					} else
						{
							$datos['resultado'] = 'error';
							$datos['error'] = 'No hay registros detalle';
						}
				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Anular venta - Venta
		if ( $_POST['accion'] == 'anularVenta' )
		{
			if ( empty($_POST['accion']) )
			{
				$datos['resultado'] = 'error';
				$datos['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$token_usuario = md5($_SESSION['idUsuario']);

					//Ejecutar procedimiento almacenado
					$sql_del = "DELETE FROM detalle_temp WHERE token_user = '$token_usuario' ";
					// echo 'sql_del: ' . $sql_del . "\n\r";
					$query_del = mysqli_query($conexion, $sql_del);
					mysqli_close($conexion);

					if ( $query_del > 0 )
					{
						$datos['resultado'] = 'ok';
						$datos['error'] = 'Venta anulada';								
					}
					else
						{
							$datos['resultado'] = 'error';
							$datos['error'] = 'No hay registros detalle';
						}
				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Procesar venta - Venta
		if ( $_POST['accion'] == 'procesarVenta' )
		{
			// print_r($_POST);
			if ( !is_numeric($_POST['codcliente']) )
			{
				$datos['resultado'] = 'error';
				$datos['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$codcliente = $_POST['codcliente'];
					$usuario = $_SESSION['idUsuario'];
					$token_usuario = md5($_SESSION['idUsuario']);

					$sql = "SELECT *
			                  FROM detalle_temp d 
			                  WHERE token_user = '$token_usuario' "; 
					// echo 'sql: ' . $sql . "\n\r";
				 	// exit;
					$query = mysqli_query($conexion, $sql);
					$res = 0;
					$res = mysqli_num_rows($query);
					// echo 'res: ' . $res . "\n\r"; 
					// exit;

					if ( $res > 0 )
					{
						// echo 'res in: ' . $res . "\n\r"; 
						//Ejecutar procedimiento almacenado
						$call = "CALL procesar_venta($usuario, $codcliente, '$token_usuario' )";
						// echo 'call: ' . $call . "\n\r";
						$query_venta = mysqli_query($conexion, $call);
						// echo 'query_venta: ' . "\n\r";
						// print_r($query_venta);
						mysqli_close($conexion);
						$resultado_tmp = 0;
						$resultado_tmp = mysqli_num_rows($query_venta);
						// echo 'resultado_tmp: ' . "\n\r";
						// print_r($resultado_tmp);
						// exit;

						if ( $resultado_tmp > 0 )
						{
							// echo 'detalle: ' . "\n\r";
							$datos['detalle'] = mysqli_fetch_assoc($query_venta);
							$datos['resultado'] = 'ok';
							$datos['error'] = 'Venta realizada';								
						}
						else
							{
								// echo 'fallo: ' . "\n\r";
								$datos['resultado'] = 'error';
								$datos['error'] = 'Proceso fallido';
							}
					}
					else
						{
							// echo 'No hay registros detalle: ' . "\n\r";
							$datos['resultado'] = 'error';
							$datos['error'] = 'No hay registros detalle';
						}

				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Añadir detalle producto - Ventas
		if ( $_POST['accion'] == 'buscaDetalles' )
		{
			if ( empty($_POST['accion']) )
			{
				$datos['resultado'] = 'error';
				$datos['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$usuario_id = $_SESSION['idUsuario'];
					$token_usuario = md5($_SESSION['idUsuario']);
					// echo 'token_usuario: ' . $token_usuario . "\n\r";

					$sql_iva = "SELECT iva
				                 FROM configuracion c 
				                 WHERE id = 1 "; 
					// echo $sql_iva . "\n\r";
					$query_iva = mysqli_query($conexion, $sql_iva);
					$res_iva = 0;
					$res_iva = mysqli_num_rows($query_iva);
					// echo $res_iva . "\n\r";

					//Ejecutar procedimiento almacenado
					$sql_temp = "SELECT tmp.correlativo, tmp.codproducto, p.descripcion, tmp.cantidad, tmp.precio_venta 
	   						   FROM detalle_temp tmp
	   						   INNER JOIN producto p
	   						      ON tmp.codproducto = p.codproducto
	   						   WHERE tmp.token_user = '" . $token_usuario . "'";
					// echo 'sql_temp: ' . $sql_temp . "\n\r";
					$query_temp = mysqli_query($conexion, $sql_temp);
					mysqli_close($conexion);
					$resultado_tmp = 0;
					$resultado_tmp = mysqli_num_rows($query_temp);
					// echo 'resultado_tmp:' . "\n\r";
					// echo 'nº resultado_tmp: ' . $resultado_tmp . "\n\r";

					$datos_detalle_tmp = 0;

					$detalle_tabla = '';
					$sub_total = 0;
					$iva = 0;
					$total = 0;

					if ( $resultado_tmp > 0 )
					{
						if ( $res_iva > 0 )
						{
							$info_iva = mysqli_fetch_assoc($query_iva);
							mysqli_close($conexion);
							$iva = $info_iva['iva'];
							$detalle_tabla  = '';
							$detalle_totales  = '';

							while ( $datos_detalle_tmp = mysqli_fetch_assoc($query_temp) )
							{
								$precio_total = round($datos_detalle_tmp['cantidad'] * $datos_detalle_tmp['precio_venta'], 2);
								$sub_total = round($sub_total * $precio_total, 2);
								$total = round($total + $precio_total, 2);

								$detalle_tabla .= '<tr>';
								$detalle_tabla .= '	<td>' . $datos_detalle_tmp['codproducto'] . '</td>';
								$detalle_tabla .= '	<td colspan="2">' . $datos_detalle_tmp['descripcion'] . '</td>';
								$detalle_tabla .= '	<td  class="textcenter">' . $datos_detalle_tmp['cantidad'] . '</td>';
								$detalle_tabla .= '	<td  class="textright">' . $datos_detalle_tmp['precio_venta'] . '</td>';
								$detalle_tabla .= '	<td  class="textright">' . $precio_total . '</td>';
								$detalle_tabla .= '	<td class="">';
								$detalle_tabla .= '		<a href="#" class="link_del" onclick="event.preventDefault(); del_producto_detalle(' . $datos_detalle_tmp['correlativo'] . ');"><i class="fas fa-trash-alt"></i></a>';
								$detalle_tabla .= '	</td>';
								$detalle_tabla .= '<tr>';

							}

							$total_sin_iva = round($total / ( 1 + $iva / 100 ), 2);
							$impuesto = round($total - $total_sin_iva, 2);
							//$total = round($total_sin_iva + $impuesto, 2);

							$detalle_totales  = '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">SUBTOTAL.Q</td>';
							$detalle_totales .= '	<td class="textright">' . $total_sin_iva . '</td>';
							$detalle_totales .= '</tr>';
							$detalle_totales .= '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">IVA (21 %)</td>';
							$detalle_totales .= '	<td class="textright">' . $impuesto . '</td>';
							$detalle_totales .= '</tr>				';
							$detalle_totales .= '<tr>';
							$detalle_totales .= '	<td colspan="5" class="textright">TOTAL.Q</td>';
							$detalle_totales .= '	<td class="textright">' . $total . '0</td>';
							$detalle_totales .= '</tr>								';
		
							$datos['detalle'] = $detalle_tabla;
							$datos['totales'] = $detalle_totales;

							$datos['resultado'] = 'ok';
							$datos['error'] = '';
						}
						else
							{
								$datos['resultado'] = 'error';
								$datos['error'] = 'Falta el IVA';								
							}
					} else
						{
							$datos['resultado'] = 'error';
							$datos['error'] = 'No hay registros detalle';
						}
				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}		

		// Información venta - Venta
		if ( $_POST['accion'] == 'infoFactura' )
		{
			if ( empty($_POST['nofactura']) )
			{
				$datos['resultado'] = 'error';
				$datos['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$nofactura = $_POST['nofactura'];
					$usuario = $_SESSION['idUsuario'];
					$token_usuario = md5($_SESSION['idUsuario']);

					$sql = "SELECT *
			                  FROM factura f 
			                  WHERE nofactura = $nofactura 
			                    AND estatus = 1 "; 
					// echo $sql . "\n\r";
					$query_factura = mysqli_query($conexion, $sql);
					$res = 0;
					$res = mysqli_num_rows($query_factura);
					// echo $res_iva . "\n\r";

					if ( $res > 0 )
					{
						$datos['detalle'] = mysqli_fetch_assoc($query_factura);
						mysqli_close($conexion);
						$datos['resultado'] = 'ok';
						$datos['error'] = 'Venta anulada';								
					}
					else
						{
							$datos['resultado'] = 'error';
							$datos['error'] = 'No hay registros detalle';
						}

				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Anular factura - Venta
		if ( $_POST['accion'] == 'anularFactura' )
		{
			$datos['salida']['resultado'] = '';
			$datos['salida']['error'] = '';								
			$datos['salida']['detalle'] = '';			

			if ( empty($_POST['nofactura']) )
			{
				$datos['salida']['resultado'] = 'error';
				$datos['salida']['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$nofactura = $_POST['nofactura'];

					$call = "CALL anular_factura($nofactura)"; 
					// echo $call . "\n\r";
					$query_anular = mysqli_query($conexion, $call);
					$res = 0;
					$res = mysqli_num_rows($query_anular);
					// echo $res . "\n\r";

					if ( $res > 0 )
					{
						$datos['salida']['resultado'] = 'ok';
						$datos['salida']['error'] = 'Venta anulada';								
						$datos['salida']['detalle'] = mysqli_fetch_assoc($query_anular);
						mysqli_close($conexion);
					}
					else
						{
							$datos['salida']['resultado'] = 'error';
							$datos['salida']['error'] = 'No hay registros detalle';
						}
				}

			echo json_encode($datos, JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Cambio clave - Usuarios
		if ( $_POST['accion'] == 'cambioClave' )
		{
			$datos['salida']['resultado'] = '';
			$datos['salida']['codigo'] = '';								
			$datos['salida']['mensaje'] = '';								
			$datos['salida']['detalle'] = '';			

			if ( empty($_POST['claveActual']) || empty($_POST['nuevaClave']) )
			{
				$datos['salida']['codigo'] = '01';
				$datos['salida']['resultado'] = 'error';
				$datos['salida']['error'] = 'Datos de entrada erróneos.';
			}
			else
				{
					$claveActual = md5($_POST['claveActual']);
					$nuevaClave = md5($_POST['nuevaClave']);
					$usuario = $_SESSION['idUsuario'];

					$sql_user = "SELECT * FROM usuario
					        WHERE clave = '$claveActual' AND idusuario = $usuario"; 
					// echo $sql_user . "\n\r";
					$query_user = mysqli_query($conexion, $sql_user);
					$res = 0;
					$res = mysqli_num_rows($query_user);
					// echo $res . "\n\r";

					if ( $res > 0 )
					{
						$sql_mod = "UPDATE usuario 
									SET clave = '$nuevaClave' 
								   WHERE idusuario = $usuario"; 
						// echo $sql_mod . "\n\r";
						$query_mod = mysqli_query($conexion, $sql_mod);

						if ( $query_mod )
						{
							$datos['salida']['resultado'] = 'ok';
							$datos['salida']['codigo'] = '00';
							$datos['salida']['mensaje'] = 'Clave modificada correctamente';								
						} else
							{
								$datos['salida']['resultado'] = 'error';
								$datos['salida']['codigo'] = '03';
								$datos['salida']['mensaje'] = 'Error al actualizar la contraseña';								
							}

					}
					else
						{
							$datos['salida']['resultado'] = 'error';
							$datos['salida']['codigo'] = '02';
							$datos['salida']['mensaje'] = 'La contraseña actual no es correcta';
						}
				}
			
			mysqli_close($conexion);

			echo json_encode($datos['salida'], JSON_UNESCAPED_UNICODE);
			exit;
		}

		// Actualizar datos empresa
		if ( $_POST['accion'] == 'modificarDatosEmpresa' )
		{
			$datos['salida']['resultado'] = '';
			$datos['salida']['codigo'] = '';								
			$datos['salida']['mensaje'] = '';								
			$datos['salida']['detalle'] = '';			

			if ( empty($_POST['txtNif']) || empty($_POST['txtNombre']) || empty($_POST['txtTelEmpresa']) || empty($_POST['txtEmailEmpresa']) || empty($_POST['txtDirEmpresa']) || empty($_POST['txtIva']) )
			{
				$datos['salida']['codigo'] = '01';
				$datos['salida']['resultado'] = 'error';
				$datos['salida']['mensaje'] = 'Todos los campos son obligatorios.';
			}
			else
				{
					$strNif = $_POST['txtNif'];
					$strNombre = $_POST['txtNombre'];
					$strRSocial = $_POST['txtRSocial'];
					$intTelEmpresa = $_POST['txtTelEmpresa'];
					$strEmailEmpresa = $_POST['txtEmailEmpresa'];
					$strDirEmpresa = $_POST['txtDirEmpresa'];
					$intIva = $_POST['txtIva'];

					$usuario = $_SESSION['idUsuario'];

					$sql_emp = "UPDATE configuracion 
								SET nif          =  '$strNif'
								   ,nombre       = '$strNombre'
								   ,razon_social = '$strRSocial'
								   ,telefono     =  $intTelEmpresa
								   ,email        = '$strEmailEmpresa'
								   ,direccion    = '$strDirEmpresa'
								   ,iva          =  $intIva
							   WHERE id = 1 "; 
					echo $sql_emp . "\n\r";
					$query_emp = mysqli_query($conexion, $sql_emp);
					echo $query_emp . "\n\r";

					if ( $query_emp )
					{						
						$datos['salida']['resultado'] = 'ok';
						$datos['salida']['codigo'] = '00';
						$datos['salida']['mensaje'] = 'Datos actualizados correctamente';								
					} else
						{
							$datos['salida']['resultado'] = 'error';
							$datos['salida']['codigo'] = '02';
							$datos['salida']['mensaje'] = 'Error al actualizar los datos';								
						}
				}
			
			mysqli_close($conexion);

			echo json_encode($datos['salida'], JSON_UNESCAPED_UNICODE);
			exit;
		}
	}

	exit;
 ?>