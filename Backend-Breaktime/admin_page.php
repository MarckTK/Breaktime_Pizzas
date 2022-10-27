<?php

include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="shortcut icon" type="image/x-icon" href="Img/img_logo/Logo Borde.png" />
    <!-- Fuente de Estilo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Enlace con CSS -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php' ?>
    <section class="dashboard">
        <h1 class="heading">Panel de Control</h1>
        <div class="box-container">
            <div class="box">
                <?php
                    $total_pendings = 0;
                    $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                    $select_pendings->execute(['Pendiente']);
                    if($select_pendings->rowCount() > 0){
                        while($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)){
                        $total_pendings += $fetch_pendings['total_price'];
                        }
                    }
                ?>
                <h3>S/<?= $total_pendings; ?>-</h3>
                <p>Pedidos Pendientes</p>
                <a href="admin_orders.php" class="btn">Ver Pedidos</a>
            </div>

            <div class="box">
                <?php
                    $total_completes = 0;
                    $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                    $select_completes->execute(['Completado']);
                    if($select_completes->rowCount() > 0){
                        while($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)){
                            $total_completes += $fetch_completes['total_price'];
                        }
                    }
                ?>
                <h3>S/<?= $total_completes; ?>-</h3>
                <p>Pedidos Completados</p>
                <a href="admin_orders.php" class="btn">Ver Pedidos</a>
            </div>

            <div class="box">
                <?php
                    $select_orders = $conn->prepare("SELECT * FROM `orders`");
                    $select_orders-> execute();
                    $number_of_orders = $select_orders->rowCount()
                ?>
                <h3><?= $number_of_orders; ?></h3>
                <p>Pedidos Realizados</p>
                <a href="admin_orders.php" class="btn">Ver Pedidos</a>
            </div>

            <div class="box">
                <?php
                    $select_products = $conn->prepare("SELECT * FROM `products`");
                    $select_products->execute();
                    $number_of_products = $select_products->rowCount()
                ?>
                <h3><?= $number_of_products; ?></h3>
                <p>Añadir Productos</p>
                <a href="admin_products.php" class="btn">Ver Productos</a>
            </div>

            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `user`");
                $select_users->execute();
                $number_of_users = $select_users->rowCount()
                ?>
                <h3><?= $number_of_users; ?></h3>
                <p>Usuarios Normales</p>
                <a href="users_accounts.php" class="btn">Ver Usuarios</a>
            </div>

            <div class="box">
                <?php
                $select_admins = $conn->prepare("SELECT * FROM `admin`");
                $select_admins->execute();
                $number_of_admins = $select_admins->rowCount()
                ?>
                <h3><?= $number_of_admins; ?></h3>
                <p>Administradores</p>
                <a href="admin_accounts.php" class="btn">Ver Administradores</a>
            </div>
        </div>
    </section>

    <!-- Enlace con Js -->
    <script src="js/admin_script.js"></script>
</body>

</html>