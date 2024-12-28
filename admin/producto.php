<?php
include 'db.php';

function agregarProducto($nombre, $precio, $stock, $fecha_caducidad, $codigo_barra, $categoria_id, $proveedor_ids, $usuario_actual) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE codigo_barra = ?");
        $stmt->execute([$codigo_barra]);
        if ($stmt->fetchColumn() > 0) {
            return "El producto con este código de barras ya está registrado.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, stock, fecha_caducidad, codigo_barra, categoria_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $precio, $stock, $fecha_caducidad, $codigo_barra, $categoria_id]);
            $producto_id = $pdo->lastInsertId();

            // Insertar proveedores
            foreach ($proveedor_ids as $proveedor_id) {
                $stmt = $pdo->prepare("INSERT INTO productos_has_proveedor (productos_id, proveedor_id) VALUES (?, ?)");
                $stmt->execute([$producto_id, $proveedor_id]);
            }

            // Registrar movimiento
            $stmt = $pdo->prepare("INSERT INTO historial_movimientos (id_producto, fecha_movimiento, cantidad, tipo_movimiento, usuario) VALUES (?, NOW(), ?, 'agregación', ?)");
            $stmt->execute([$producto_id, $stock, $usuario_actual]);
            return "Producto agregado exitosamente.";
        }
    } catch (PDOException $e) {
        return "Error al agregar producto: " . $e->getMessage();
    }
}

function actualizarProducto($id_productos, $nombre, $precio, $stock, $fecha_caducidad, $codigo_barra, $categoria_id, $proveedor_ids, $usuario_actual) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, precio=?, stock=?, fecha_caducidad=?, codigo_barra=?, categoria_id=? WHERE id_productos=?");
        $stmt->execute([$nombre, $precio, $stock, $fecha_caducidad, $codigo_barra, $categoria_id, $id_productos]);

        // Actualizar proveedores
        $stmt = $pdo->prepare("DELETE FROM productos_has_proveedor WHERE productos_id = ?");
        $stmt->execute([$id_productos]);
        foreach ($proveedor_ids as $proveedor_id) {
            $stmt = $pdo->prepare("INSERT INTO productos_has_proveedor (productos_id, proveedor_id) VALUES (?, ?)");
            $stmt->execute([$id_productos, $proveedor_id]);
        }

        // Registrar movimiento
        $stmt = $pdo->prepare("INSERT INTO historial_movimientos (id_producto, fecha_movimiento, cantidad, tipo_movimiento, usuario) VALUES (?, NOW(), ?, 'actualización', ?)");
        $stmt->execute([$id_productos, $stock, $usuario_actual]);

        return "Producto actualizado exitosamente.";
    } catch (PDOException $e) {
        return "Error al actualizar producto: " . $e->getMessage();
    }
}

function eliminarProducto($id_productos) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id_productos = ?");
        $stmt->execute([$id_productos]);

        if ($stmt->rowCount() > 0) {
            return "Producto eliminado exitosamente.";
        } else {
            return "Error al eliminar el producto. No se encontró el producto.";
        }
    } catch (PDOException $e) {
        return "Error al eliminar producto: " . $e->getMessage();
    }
}


?>
