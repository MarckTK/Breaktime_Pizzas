<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
    header('location:admin_login.php');
};

if(isset($_POST['add_product'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'Img/img_subidas/'.$image;

    $select_product = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_product->execute([$name]);

    if($select_product->rowCount() > 0){
        $message[] = '¡El nombre del producto ya existe!';
    }else{
        if($image_size > 2000000){
            $message[] = '¡La imagen es muy pesada!';
        }else{
            $insert_product = $conn->prepare("INSERT INTO `products`(name, price, image) VALUES(?,?,?)");
            $insert_product->execute([$name, $price, $image]);
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = '¡Nuevo producto añadido!';
        }
    }

}

if(isset($_GET['delete'])){

    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT image FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    unlink('Img/img_subidas/'.$fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);
    header('location:admin_products.php');

}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="shortcut icon" type="image/x-icon" href="Img/img_logo/Logo Borde.png" />
    <!-- Fuente de Estilo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Enlace con CSS -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'?>

    <section class="add-products">
        <h1 class="heading">Añadir Producto</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" class="box" required maxlength="100" placeholder="Nombre de Pizza" name="name">
            <input type="number" min="0" class="box" required max="9999999999" placeholder="Precio"
                onkeypress="if(this.value.length == 10) return false;" name="price">
            <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
            <input type="submit" value="Añadir Producto" class="btn" name="add_product">
        </form>
    </section>

    <section class="show-products">
        <h1 class="heading">Productos Añadidos</h1>
        <div class="box-container">
            <?php
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
            if($select_products->rowCount() > 0){
                while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
            ?>
            <div class="box">
                <div class="price">S/<span><?= $fetch_products['price']; ?></span>-</div>
                <img src="Img/img_subidas/<?= $fetch_products['image']; ?>" alt="">
                <div class="name"><?= $fetch_products['name']; ?></div>
                <div class="flex-btn">
                    <a href="admin_product_update.php?update=<?= $fetch_products['id']; ?>"
                        class="option-btn">Actualizar</a>
                    <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn"
                        onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
                </div>
            </div>

            <?php
            }
            }else{
                echo '<p class="empty">¡Aún no hay productos añadidos!</p>';
            }
            ?>
        </div>
    </section>


    <script src="js/admin_script.js"></script>

</body>

</html>