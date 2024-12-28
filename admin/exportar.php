<?php
include "db.php";

// Si se está solicitando exportar a CSV o Excel
if (isset($_GET['formato'])) {
    // Obtener los productos desde la base de datos
    $query = "SELECT p.id_productos, p.nombre, p.precio, p.stock, p.fecha_caducidad, p.codigo_barra, 
                     c.nombre AS categoria, GROUP_CONCAT(pr.nombre_proveedor) AS proveedores
              FROM productos p 
              INNER JOIN categoria c ON p.categoria_id = c.id_categoria
              LEFT JOIN productos_has_proveedor php ON p.id_productos = php.productos_id
              LEFT JOIN proveedor pr ON php.proveedor_id = pr.id_proveedor
              LEFT JOIN categoria sc ON p.categoria_id = sc.id_categoria
              GROUP BY p.id_productos";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Limpiar cualquier buffer previo
    ob_end_clean(); 

    // Si el formato es CSV
    if ($_GET['formato'] == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="productos.csv"');

        $output = fopen('php://output', 'w');
        
        // Escribir los encabezados con todos los atributos
        fputcsv($output, ['Id', 'Nombre', 'Precio', 'Stock', 'Fecha de Caducidad', 'Código de Barra', 'Subcategoria', 'Proveedor(es)']);

        // Escribir los datos de los productos
        foreach ($productos as $producto) {
            fputcsv($output, [
                $producto['id_productos'],
                $producto['nombre'],
                $producto['precio'],
                $producto['stock'],
                $producto['fecha_caducidad'],
                $producto['codigo_barra'],
                $producto['categoria'], // Subcategoria
                $producto['proveedores'] // Proveedores
            ]);
        }

        fclose($output);
        exit;
    }

    // Si el formato es Excel (.xls)
    if ($_GET['formato'] == 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="productos.xls"');

        // Abrir el archivo de salida
        $output = fopen('php://output', 'w');

        // Escribir los encabezados con todos los atributos
        fputcsv($output, ['Id', 'Nombre', 'Precio', 'Stock', 'Fecha de Caducidad', 'Código de Barra', 'Subcategoria', 'Proveedor(es)'], "\t");

        // Escribir los datos de los productos
        foreach ($productos as $producto) {
            fputcsv($output, [
                $producto['id_productos'],
                $producto['nombre'],
                $producto['precio'],
                $producto['stock'],
                $producto['fecha_caducidad'],
                $producto['codigo_barra'],
                $producto['categoria'], // Subcategoria
                $producto['proveedores'] // Proveedores
            ], "\t");
        }

        fclose($output);
        exit;
    }
}
?>
