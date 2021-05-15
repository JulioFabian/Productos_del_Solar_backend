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
        order by fecha_de_entrega, hora_de_entrega desc";
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
        order by fecha_de_entrega, hora_de_entrega desc
    ";
    $resultado=metodoPost($query, $queryAutoIncrement);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='PUT'){
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
    order by fecha_de_entrega, hora_de_entrega desc
    ";
    $resultado=metodoPost($query, $queryAutoIncrement);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='DELETE'){
    unset($_POST['METHOD']);
    $id=$_GET['id'];
    $query="DELETE FROM venta WHERE id='$id'";
    $resultado=metodoDelete($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

header("HTTP/1.1 400 Bad Request");

?>