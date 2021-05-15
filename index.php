<?php 

include 'bd/BD.php';

header('Access-Control-Allow-Origin: *');

if($_SERVER['REQUEST_METHOD']=='GET'){
    if(isset($_GET['id'])){
        $query="select * from inventario where id=".$_GET['id'];
        $resultado=metodoGet($query);
        echo json_encode($resultado->fetch(PDO::FETCH_ASSOC));
    }else{
        $query="select * from inventario";
        $resultado=metodoGet($query);
        echo json_encode($resultado->fetchAll());
    }
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='POST'){
    unset($_POST['METHOD']);
    $producto=$_POST['producto'];
    $codigo=$_POST['codigo'];
    $precioa=$_POST['precioa'];
    $preciob=$_POST['preciob'];
    $precioc=$_POST['precioc'];
    $costo=$_POST['costo'];
    $cantidad=$_POST['cantidad'];
    $query="insert into inventario(producto, codigo, precioa, preciob, precioc, costo, cantidad) values ('$producto', '$codigo', '$precioa', '$preciob', '$precioc', '$costo', '$cantidad')";
    $queryAutoIncrement="select MAX(id) as id from inventario";
    $resultado=metodoPost($query, $queryAutoIncrement);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='PUT'){
    unset($_POST['METHOD']);
    $id=$_GET['id'];
    $producto=$_POST['producto'];
    $codigo=$_POST['codigo'];
    $precioa=$_POST['precioa'];
    $preciob=$_POST['preciob'];
    $precioc=$_POST['precioc'];
    $costo=$_POST['costo'];
    $cantidad=$_POST['cantidad'];
    $query="UPDATE inventario SET producto='$producto', codigo='$codigo', precioa='$precioa', preciob='$preciob', precioc='$precioc', costo='$costo', cantidad='$cantidad' WHERE id='$id'";
    $resultado=metodoPut($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

if($_POST['METHOD']=='DELETE'){
    unset($_POST['METHOD']);
    $id=$_GET['id'];
    $query="DELETE FROM inventario WHERE id='$id'";
    $resultado=metodoDelete($query);
    echo json_encode($resultado);
    header("HTTP/1.1 200 OK");
    exit();
}

header("HTTP/1.1 400 Bad Request");

?>