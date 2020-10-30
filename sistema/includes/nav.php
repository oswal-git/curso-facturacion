		<nav>
			<ul>
				<li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>

				<?php 
					if ( $_SESSION['rol'] == 1) {
						$nav_usuarios  = '	<li class="principal">';	
						$nav_usuarios .= '		<a href="#"><i class="fas fa-users"></i> Usuarios <span class="flecha"><i class="fas fa-angle-down"></i></span></a>';
						$nav_usuarios .= '		<ul>';
						$nav_usuarios .= '			<li><a href="registro_usuario.php"><i class="fas fa-user-plus"></i> Nuevo Usuario</a></li>';
						$nav_usuarios .= '			<li><a href="lista_usuarios.php"><i class="fas fa-users"></i> Lista de Usuarios</a></li>';
						$nav_usuarios .= '		</ul>';
						$nav_usuarios .= '	</li>';
						echo $nav_usuarios;
					}
				 ?>
				<li class="principal">
					<a href="#"><i class="fas fa-user-alt"></i> Clientes <span class="flecha"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="registro_cliente.php"><i class="fas fa-user-plus"></i> Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php"><i class="far fa-list-alt"></i> Lista de Clientes</a></li>
					</ul>
				</li>
				<?php 
					if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 ) {
						$nav_usuarios  = '	<li class="principal">';	
						$nav_usuarios .= '		<a href="#"><i class="far fa-building"></i> Proveedores <span class="flecha"><i class="fas fa-angle-down"></i></span></a>';
						$nav_usuarios .= '		<ul>';
						$nav_usuarios .= '			<li><a href="registro_proveedor.php"><i class="fas fa-plus"></i> Nuevo Proveedor</a></li>';
						$nav_usuarios .= '			<li><a href="lista_proveedores.php"><i class="far fa-list-alt"></i> Lista de Proveedores</a></li>';
						$nav_usuarios .= '		</ul>';
						$nav_usuarios .= '	</li>';
						echo $nav_usuarios;
					}
				 ?>
				<?php 
					$nav_usuarios  = '	<li class="principal">';	
					$nav_usuarios .= '		<a href="#"><i class="fas fa-cubes"></i> Productos <span class="flecha"><i class="fas fa-angle-down"></i></span></a>';
					$nav_usuarios .= '		<ul>';
					if ( $_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 ) 
					{
						$nav_usuarios .= '			<li><a href="registro_producto.php"><i class="fas fa-plus"></i> Nuevo Producto</a></li>';
					}
					$nav_usuarios .= '			<li><a href="buscar_producto.php"><i class="fas fa-cube"></i> Lista de Productos</a></li>';
					$nav_usuarios .= '		</ul>';
					$nav_usuarios .= '	</li>';
					echo $nav_usuarios;
				 ?>				 

				<li class="principal">
					<a href="#">Ventas <span class="flecha"><i class="fas fa-angle-down"></i></span></a>
					<ul>
						<li><a href="nueva_venta.php"><i class="fas fa-plus"></i> Nueva Venta</a></li>
						<li><a href="ventas.php"><i class="far fa-newspaper"></i> Ventas</a></li>
					</ul>
				</li>
			</ul>
		</nav>