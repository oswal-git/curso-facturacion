<?php 
	$host = 'localhost';
	$usuario = 'root';
	$clave = '';
	$bd = 'facturacion';

	$conexion = @mysqli_connect($host, $usuario, $clave, $bd);

	if ( !$conexion)
	{
		echo "Error en la conexión.";
	} 
 ?>