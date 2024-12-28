<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" type="text/css" href="nav.css">
    <title>Menú - Sistema de Gestión de Inventario</title>
    <style>
        /* Estilos del snackbar */
        #snackbar {
            font-family: 'Arial';
            visibility: hidden;
            min-width: 250px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 10px;
            /* Mover el snackbar hacia la izquierda */
            bottom: 30px;
            /* Mantener la posición inferior */
            font-size: 20px;
        }

        /* Mostrar el snackbar cuando se añada la clase 'show' */
        #snackbar.show {
            font-family: 'Arial', sans-serif;
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        /* Animación de aparición */
        @keyframes fadein {
            from {
                bottom: 0;
                opacity: 0;
            }

            to {
                bottom: 30px;
                opacity: 1;
            }
        }

        /* Animación de desaparición */
        @keyframes fadeout {
            from {
                bottom: 30px;
                opacity: 1;
            }

            to {
                bottom: 0;
                opacity: 0;
            }
        }
    </style>
</head>

<body class="body-with-scrollbar">
    <!-- Barra de navegación -->
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php" class="active">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="gestion_local.php">Locales</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <script src="js/navbar.js"></script>

    <header>
        <h1>Bienvenido al Sistema de Gestión de Inventario</h1>
        <p>Administra tus productos, monitorea su stock, y mantén todo bajo control con nuestra plataforma.</p>
    </header>

    <section id="features">
        <h2>Características Principales</h2>
        <ul>
            <li><strong>Inventario en Tiempo Real:</strong> Consulta y gestiona el stock de tus productos de forma actualizada.</li>
            <li><strong>Alertas Automáticas:</strong> Recibe notificaciones cuando el stock de un producto esté bajo o cuando un producto esté por caducar.</li>
            <li><strong>Organización por Categorías:</strong> Clasifica tus productos por categorías y subcategorías para una gestión más sencilla.</li>
            <li><strong>Gestión de Proveedores:</strong> Lleva un registro detallado de tus proveedores y sus productos asociados.</li>
            <li><strong>Seguridad:</strong> Acceso seguro mediante cuentas de usuario, con opción de cerrar sesión y crear nuevas cuentas.</li>
        </ul>
    </section>

    <section id="quick-links">
        <h2>Accesos Rápidos</h2>
        <div class="links">
            <a href="index.php" class="button">Ir a Inventario</a>
            <a href="alertas.php" class="button">Ver Alertas</a>
            <a href="categorias.php" class="button">Gestionar Categorías</a>
            <a href="proveedores.php" class="button">Ver Proveedores</a>
        </div>
    </section>

    <!-- Snackbar -->
    <div id="snackbar">Entraste como administrador</div>

    <footer></footer>

    <script>
        // Función para mostrar el snackbar
        function showSnackbar() {
            var snackbar = document.getElementById("snackbar");
            snackbar.className = "show";
            setTimeout(function() {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000); // El snackbar se oculta después de 3 segundos
        }

        // Mostrar el snackbar al cargar la página
        window.onload = function() {
            showSnackbar();
        };
    </script>

</body>

</html>