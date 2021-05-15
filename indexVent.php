<?php 

include 'bd/BD.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD']=='GET'){
    if(isset($_GET['id'])){
        $query="select * from venta where id=".$_GET['id'];
        $resultado=metodoGet($query);
        echo json_encode($resultado->fetch(PDO::FETCH_ASSOC));
    }else{
        $query="select venta.id, nombre, apellido, direccion, fecha_de_entrega, hora_de_entrega, producto, venta.cantidad, fecha_de_pedido, id_cliente, id_producto
        from venta 
        join cliente on (venta.id_cliente = cliente.id) 
        join inventario on (venta.id_producto = inventario.id)
        where entregado = 0
        order by fecha_de_entrega, hora_de_entrega asc";
        $resultado=metodoGet($query);
        echo json_encode($resultado->fetchAll());
    }
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='POST'){
    unset($_POST['METHOD']);
    $id_producto=$_POST['id_producto'];
    $id_cliente=$_POST['id_cliente'];
    $cantidad=$_POST['cantidad'];
    // $fecha_de_pedido=$_POST['fecha_de_pedido'];
    $fecha_de_entrega=$_POST['fecha_de_entrega'];
    $hora_de_entrega=$_POST['hora_de_entrega'];
    $query="insert into venta(id_producto, id_cliente, cantidad, fecha_de_pedido, fecha_de_entrega, hora_de_entrega) values ('$id_producto', '$id_cliente', '$cantidad', curdate(), '$fecha_de_entrega', '$hora_de_entrega')";
    $queryAutoIncrement="select venta.id, nombre, apellido, direccion, fecha_de_entrega, hora_de_entrega, producto, venta.cantidad, fecha_de_pedido, id_cliente, id_producto
        from venta 
        join cliente on (venta.id_cliente = cliente.id) 
        join inventario on (venta.id_producto = inventario.id)
        WHERE venta.id = ( SELECT MAX(venta.id) FROM venta)
        order by fecha_de_entrega, hora_de_entrega asc
    ";
    $query2="update inventario set cantidad = cantidad - $cantidad where id = $id_producto";
    $resultado=metodoPost($query, $queryAutoIncrement);
    $ejecutaResultado=metodoPost($query2, $queryAutoIncrement);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='PUT'){
    if(isset($_GET['entregado'])){
        $id=$_GET['id'];
        $query="UPDATE venta set entregado = 1 where id=$id";
        $resultado=metodoPut($query);
    }else{
        unset($_POST['METHOD']);
        $id=$_GET['id'];
        $id_producto=$_POST['id_producto'];
        $id_cliente=$_POST['id_cliente'];
        $cantidad=$_POST['cantidad'];
        // $fecha_de_pedido=$_POST['fecha_de_pedido'];
        $fecha_de_entrega=$_POST['fecha_de_entrega'];
        $hora_de_entrega=$_POST['hora_de_entrega'];
        // $query="UPDATE venta SET cantidad='$cantidad', fecha_de_pedido='$fecha_de_pedido', fecha_de_entrega='$fecha_de_entrega', hora_de_entrega='$hora_de_entrega' WHERE id='$id'";
        $query="UPDATE venta SET id_producto='$id_producto', id_cliente='$id_cliente', cantidad='$cantidad', fecha_de_entrega='$fecha_de_entrega', hora_de_entrega='$hora_de_entrega' WHERE id='$id'";
        $queryAutoIncrement="select venta.id, nombre, apellido, direccion, fecha_de_entrega, hora_de_entrega, producto, venta.cantidad, fecha_de_pedido, id_cliente, id_producto
        from venta 
        join cliente on (venta.id_cliente = cliente.id) 
        join inventario on (venta.id_producto = inventario.id)
        WHERE venta.id = $id;
        order by fecha_de_entrega, hora_de_entrega asc
        ";
        $queryCantidad="SELECT cantidad from venta where id=$id";
        $cantidadVentaQuery=metodoGet($queryCantidad)->fetch(PDO::FETCH_ASSOC);
        $cantidadVenta=$cantidadVentaQuery['cantidad'];
        $resultado=metodoPost($query, $queryAutoIncrement);
        $cantidadDiferencia=($cantidadVenta-$cantidad);
        $queryUdateInv="update inventario set cantidad = cantidad + $cantidadDiferencia where id = $id_producto";
        $ejecutaQuery=metodoPut($queryUdateInv);
        echo json_encode($resultado);
    }
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='DELETE'){
    unset($_POST['METHOD']);
    $id=$_GET['id'];
    $queryCantidad="SELECT cantidad, id_producto from venta where id=$id";
    $cantidadVentaQuery=metodoGet($queryCantidad)->fetch(PDO::FETCH_ASSOC);
    $id_producto=$cantidadVentaQuery['id_producto'];
    $cantidadVenta=$cantidadVentaQuery['cantidad'];
    $queryUdateInv="update inventario set cantidad = cantidad + $cantidadVenta where id = $id_producto";
    $ejecutaQuery=metodoPut($queryUdateInv);
    $query="DELETE FROM venta WHERE id='$id'";
    $resultado=metodoDelete($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

header("HTTP/1.1 400 Bad Request");

?>