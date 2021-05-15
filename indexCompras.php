<?php 

include 'bd/BD.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD']=='GET'){
    if(isset($_GET['id'])){
        $query="select * from compra where id=".$_GET['id'];
        $resultado=metodoGet($query);
        echo json_encode($resultado->fetch(PDO::FETCH_ASSOC));
    }else{
        $query="select compra.id, nombre, fecha_de_compra, producto, compra.cantidad, id_proveedor, id_producto
        from compra 
        join proveedor on (compra.id_proveedor = proveedor.id) 
        join inventario on (compra.id_producto = inventario.id)
        order by fecha_de_compra asc
        limit 10";
        $resultado=metodoGet($query);
        echo json_encode($resultado->fetchAll());
    }
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='POST'){
    unset($_POST['METHOD']);
    $id_producto=$_POST['id_producto'];
    $id_proveedor=$_POST['id_proveedor'];
    $cantidad=$_POST['cantidad'];
    $fecha_de_compra=$_POST['fecha_de_compra'];
    $query="insert into compra(id_producto, id_proveedor, cantidad, fecha_de_compra) values ('$id_producto', '$id_proveedor', '$cantidad', '$fecha_de_compra')";
    $queryAutoIncrement="select compra.id, nombre, fecha_de_compra, producto, compra.cantidad, id_proveedor, id_producto
        from compra 
        join proveedor on (compra.id_proveedor = proveedor.id) 
        join inventario on (compra.id_producto = inventario.id)
        WHERE compra.id = ( SELECT MAX(compra.id) FROM compra)
        order by fecha_de_compra asc
        limit 10
    ";
    $query2="update inventario set cantidad = cantidad + $cantidad where id = $id_producto";
    $resultado=metodoPost($query, $queryAutoIncrement);
    $ejecutaResultado=metodoPost($query2, $queryAutoIncrement);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='PUT'){
    unset($_POST['METHOD']);
    $id=$_GET['id'];
    $id_producto=$_POST['id_producto'];
    $id_proveedor=$_POST['id_proveedor'];
    $cantidad=$_POST['cantidad'];
    $fecha_de_compra=$_POST['fecha_de_compra'];
    $query="UPDATE compra SET id_producto='$id_producto', id_proveedor='$id_proveedor', cantidad='$cantidad', fecha_de_compra='$fecha_de_compra' WHERE id='$id'";
    $queryAutoIncrement="select compra.id, nombre, fecha_de_compra, producto, compra.cantidad, id_proveedor, id_producto
    from compra 
    join proveedor on (compra.id_proveedor = proveedor.id) 
    join inventario on (compra.id_producto = inventario.id)
    WHERE compra.id = $id;
    order by fecha_de_compra asc
    limit 10
    ";
    $queryCantidad="SELECT cantidad from compra where id=$id";
    $cantidadVentaQuery=metodoGet($queryCantidad)->fetch(PDO::FETCH_ASSOC);
    $cantidadCompra=$cantidadVentaQuery['cantidad'];
    $resultado=metodoPost($query, $queryAutoIncrement);
    $cantidadDiferencia=($cantidadCompra-$cantidad);
    $queryUdateInv="update inventario set cantidad = cantidad - $cantidadDiferencia where id = $id_producto";
    $ejecutaQuery=metodoPut($queryUdateInv);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='DELETE'){
    unset($_POST['METHOD']);
    $id=$_GET['id'];
    $queryCantidad="SELECT cantidad, id_producto from compra where id=$id";
    $cantidadVentaQuery=metodoGet($queryCantidad)->fetch(PDO::FETCH_ASSOC);
    $id_producto=$cantidadVentaQuery['id_producto'];
    $cantidadCompra=$cantidadVentaQuery['cantidad'];
    $queryUdateInv="update inventario set cantidad = cantidad - $cantidadCompra where id = $id_producto";
    $ejecutaQuery=metodoPut($queryUdateInv);
    $query="DELETE FROM compra WHERE id='$id'";
    $resultado=metodoDelete($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

header("HTTP/1.1 400 Bad Request");

?>