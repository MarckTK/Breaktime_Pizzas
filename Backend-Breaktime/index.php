<?php

include 'config.php';
session_start();
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    if(isset($_SESSION['admin_id'])){
    $admin_id = $_SESSION['admin_id'];
    }else{
    $user_id = '';
    }
};

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass'] );
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `user` WHERE name = ? AND email = ?");
    $select_user->execute([$name, $email]);

    if($select_user->rowCount() > 0){
        $message[] = '¡El correo electrónico ya existe!';
    }else{
        if($pass != $cpass){
            $message[] = '¡Confirmar contraseña no coincide!';
        }else{
            $insert_user = $conn->prepare("INSERT INTO `user`(name, email, password) VALUES(?,?,?)");
            $insert_user->execute([$name, $email, $cpass]);
            $message[] = 'Se ha registrado correctamente ¡Ya puede conectarse!';
        }
    }

}

if(isset($_POST['update_qty'])){
    $cart_id = $_POST['cart_id'];
    $qty = $_POST['qty'];
    $qty = filter_var($qty, FILTER_SANITIZE_STRING);
    $update_qty = $conn->prepare("UPDATE `cart` SET quantity = ? WHERE id = ?");
    $update_qty->execute([$qty, $cart_id]);
    $message[] = '¡Carrito actualizado!';
}

if(isset($_GET['delete_cart_item'])){
    $delete_cart_id = $_GET['delete_cart_item'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
    $delete_cart_item->execute([$delete_cart_id]);
    header('location:index.php');
}

if(isset($_GET['logout'])){
    session_unset();
    session_destroy();
    header('location:index.php');
}

if(isset($_POST['add_to_cart'])){
    if($user_id == ''){
        $message[] = 'Por favor, inicie sesión primero';
    }else{

        $pid = $_POST['pid'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_POST['image'];
        $qty = $_POST['qty'];
        $qty = filter_var($qty, FILTER_SANITIZE_STRING);

        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
        $select_cart->execute([$user_id, $name]);

        if($select_cart->rowCount() > 0){
            $message[] = 'Ya está añadido al carrito';
        }else{
            $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
            $insert_cart->execute([$user_id, $pid, $name, $price, $qty, $image]);
            $message[] = '¡Añadido al carrito!';
        }
    }
}

if(isset($_POST['order'])){
    if($user_id == ''){
        $message[] = 'Por favor, inicie sesión primero';
    }else{
        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $number = $_POST['number'];
        $number = filter_var($number, FILTER_SANITIZE_STRING);
        $address = $_POST['flat'].', '.$_POST['street'].' - '.$_POST['pin_code'];
        $address = filter_var($address, FILTER_SANITIZE_STRING);
        $method = $_POST['method'];
        $method = filter_var($method, FILTER_SANITIZE_STRING);
        $total_price = $_POST['total_price'];
        $total_products = $_POST['total_products'];

        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $select_cart->execute([$user_id]);

        if($select_cart->rowCount() > 0){
            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $name, $number, $method, $address, $total_products, $total_price]);
            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart->execute([$user_id]);
            $message[] = '¡Orden realizado con éxito!';
        }else{
            $message[] = '¡Su carrito está vacío!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería Breaktime</title>
    <link rel="shortcut icon" type="image/x-icon" href="Img/img_logo/Logo Borde.png" />
    <!-- Fuente de Estilo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <!-- Enlace con Css -->
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>

    <?php
    if(isset($message)){
        foreach($message as $message){
            echo '
            <div class="message">
                <span>'.$message.'</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    }
    ?>

    <!-- Cabecera -->
    <header class="header">
        <section class="flex">
            <b>
                <a href="#home" class="logo"><span>Break</span>time</a>
            </b><!-- Se tiene que ajustar el Logo -->
            <nav class="navbar">
                <a href="#home">Inicio</a>
                <a href="#about">Nosotros</a>
                <a href="#menu">Menú</a>
                <a href="#order">Ordenar</a>
                <a href="#faq">Consultas</a>
            </nav>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="order-btn" class="fas fa-box"></div>
                <?php
                $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $count_cart_items->execute([$user_id]);
                $total_cart_items = $count_cart_items->rowCount();
                ?>
                <div id="cart-btn" class="fas fa-shopping-cart">
                    <span>(<?= $total_cart_items; ?>)</span>
                </div>
            </div>
            </div>
        </section>
    </header>
    <!-- Fin Cabecera -->

    <!-- Sesion Usuario -->
    <div class="user-account">
        <section>

            <!-- <div id="login-admin">
                <section class="form-container">
                    <form action="" method="post">
                        <a href="admin_login.php" class="btn">Página Administrativa</a>
                    </form>
                </section>
            </div> -->

            <div id="close-account"><span>Cerrar</span></div>

            <div class="user">
                <?php
                    $select_user = $conn->prepare("SELECT * FROM `user` WHERE id = ?");
                    $select_user->execute([$user_id]);
                    if($select_user->rowCount() > 0){
                    while($fetch_user = $select_user->fetch(PDO::FETCH_ASSOC)){
                        echo '<p>¡Bienvenido <span>'.$fetch_user['name'].'</span>!</p>';
                        echo '<a href="index.php?logout" class="btn">Cerrar Sesión</a>';
                    }
                    }else{
                    echo '<p><span>No te encuentras registrado :(</span></p>';
                    }
                ?>
            </div>

            <div class="display-orders">
                <?php
                $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart->execute([$user_id]);
                if($select_cart->rowCount() > 0){
                while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                    echo '<p>'.$fetch_cart['name'].' <span>('.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
                }
                }else{
                echo '<p><span>Su carrito se encuentra vacío</span></p>';
                }
                ?>
            </div>

            <div class="flex">
                <form action="user_login.php" method="post">
                    <h3>Iniciar sesión</h3>
                    <input type="email" name="email" required class="box" placeholder="Correo Electrónico"
                        maxlength="50">
                    <input type="password" name="pass" required class="box" placeholder="Contraseña" maxlength="20">
                    <input type="submit" value="Iniciar Sesión" name="login" class="btn">
                </form>

                <form action="" method="post">
                    <h3>Registrarse</h3>
                    <input type="text" name="name" oninput="this.value = this.value.replace(/\s/g, '')" required
                        class="box" placeholder="Ingrese su Nombre" maxlength="20">
                    <input type="email" name="email" required class="box" placeholder="Correo Electrónico"
                        maxlength="50">
                    <input type="password" name="pass" required class="box" placeholder="Contraseña" maxlength="20"
                        oninput="this.value = this.value.replace(/\s/g, '')">
                    <input type="password" name="cpass" required class="box" placeholder="Confirmar Contraseña"
                        maxlength="20" oninput="this.value = this.value.replace(/\s/g, '')">
                    <input type="submit" value="Registrarse" name="register" class="btn">
                </form>
            </div>
        </section>
    </div>

    <!-- ORDENES -->
    <div class="my-orders">
        <section>
            <div id="close-orders"><span>Cerrar</span></div>

            <h3 class="title"> Mis Órdenes </h3>

            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
            $select_orders->execute([$user_id]);
            if($select_orders->rowCount() > 0){
                while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){   
            ?>

            <div class="box">
                <p> Fecha de la Orden: <span><?= $fetch_orders['placed_on']; ?></span> </p>
                <p> Nombre: <span><?= $fetch_orders['name']; ?></span> </p>
                <p> Número: <span><?= $fetch_orders['number']; ?></span> </p>
                <p> Dirección: <span><?= $fetch_orders['address']; ?></span> </p>
                <p> Método de Pago: <span><?= $fetch_orders['method']; ?></span> </p>
                <p> Pedidos: <span><?= $fetch_orders['total_products']; ?></span> </p>
                <p> Precio Total: <span>S/<?= $fetch_orders['total_price']; ?></span> </p>
                <p> Estado de Pago: <span
                        style="color:<?php if($fetch_orders['payment_status'] == 'Pendiente'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span>
                </p>
            </div>

            <?php
                }
            }else{
                echo '<p class="empty">Aún no ha realizado un pedido</p>';
            }
            ?>
        </section>
    </div>

    <!-- CARRITO -->
    <div class="shopping-cart">
        <section>
            <div id="close-cart"><span>Cerrar</span></div>
            <?php
                $grand_total = 0;
                $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart->execute([$user_id]);
                if($select_cart->rowCount() > 0){
                    while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                    $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                    $grand_total += $sub_total; 
            ?>

            <div class="box">
                <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times"
                    onclick="return confirm('¿Eliminar este artículo del carrito?');"> </a>
                <img src="Img/img_subidas/<?= $fetch_cart['image']; ?>" alt="">
                <div class="content">
                    <p> <?= $fetch_cart['name']; ?> <span>(S/<?= $fetch_cart['price']; ?> x
                            <?= $fetch_cart['quantity']; ?>)</span></p>
                    <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                        <input type="number" name="qty" class="qty" min="1" max="99"
                            value="<?= $fetch_cart['quantity']; ?>"
                            onkeypress="if(this.value.length == 2) return false;">
                        <button type="submit" class="fas fa-edit" name="update_qty"></button>
                    </form>
                </div>
            </div>
            <?php
                }
            }else{
                echo '<p class="empty"><span>¡Su carrito se encuentra vacío!</span></p>';
            }
            ?>
            <div class="cart-total"> Costo Total: <span>S/<?= $grand_total; ?></span></div>
            <a href="#order" class="btn">Ordenar ahora</a>
        </section>
    </div>

    <!-- INICIO -->
    <div class="home-bg">
        <section class="home" id="home">
            <div class="slide-container">
                <div class="slide active">
                    <div class="image">
                        <img src="Img/img_fondo/home-img-1.png" alt="">
                    </div>
                    <div class="content">
                        <h3>Pizza casera de Pepperoni</h3>
                        <div class="fas fa-angle-left" onclick="prev()"></div>
                        <div class="fas fa-angle-right" onclick="next()"></div>
                    </div>
                </div>

                <div class="slide">
                    <div class="image">
                        <img src="Img/img_fondo/home-img-2.png" alt="">
                    </div>
                    <div class="content">
                        <h3>Pizza con Champiñones</h3>
                        <div class="fas fa-angle-left" onclick="prev()"></div>
                        <div class="fas fa-angle-right" onclick="next()"></div>
                    </div>
                </div>

                <div class="slide">
                    <div class="image">
                        <img src="Img/img_fondo/home-img-3.png" alt="">
                    </div>
                    <div class="content">
                        <h3>Mascarpone y Champiñones</h3>
                        <div class="fas fa-angle-left" onclick="prev()"></div>
                        <div class="fas fa-angle-right" onclick="next()"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- NOSOTROS -->
    <section class="about" id="about">
        <h1 class="heading">Sobre Nosotros</h1>
        <div class="box-container">
            <div class="box">
                <img src="Img/img_fondo/Preparacion.png" alt="">
                <h3>Buena Preparación</h3>
                <p>Contamos con cocineros profesionales que se dan el tiempo para realizar las mejores pizzas que podría
                    probar. Experimente sentirse en otro lugar.</p>
                <a href="#menu" class="btn">Nuestro Menú</a>
            </div>

            <div class="box">
                <img src="Img/img_fondo/Envio.png" alt="">
                <h3>Envío en 30 minutos</h3>
                <p>Pida su pizza Ya! y su pedido llegará en menos de 30 minutos. En caso de que su pizza no llegue a
                    tiempo, podrá obtenerlo completamente gratis.</p>
                <a href="#menu" class="btn">Nuestro Menú</a>
            </div>

            <div class="box">
                <img src="Img/img_fondo/Compartir.png" alt="">
                <h3>Disfrútalo en Compañía</h3>
                <p>No esperes mucho tiempo, aprovecha de nuestras pizzas especiales y comparte momentos agradables con
                    tu familia o mejores amigos.</p>
                <a href="#menu" class="btn">Nuestro Menú</a>
            </div>
        </div>
    </section>

    <!-- MENÚ -->
    <section id="menu" class="menu">
        <h1 class="heading">Nuestro Menú</h1>
        <div class="box-container">
            <?php
                $select_products = $conn->prepare("SELECT * FROM `products`");
                $select_products->execute();
                if($select_products->rowCount() > 0){
                    while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){    
            ?>
            <div class="box">
                <div class="price">S/<?= $fetch_products['price'] ?>-</div>
                <img src="Img/img_subidas/<?= $fetch_products['image'] ?>" alt="">
                <div class="name"><?= $fetch_products['name'] ?></div>
                <form action="" method="post">
                    <input type="hidden" name="pid" value="<?= $fetch_products['id'] ?>">
                    <input type="hidden" name="name" value="<?= $fetch_products['name'] ?>">
                    <input type="hidden" name="price" value="<?= $fetch_products['price'] ?>">
                    <input type="hidden" name="image" value="<?= $fetch_products['image'] ?>">
                    <input type="number" name="qty" class="qty" min="1" max="99"
                        onkeypress="if(this.value.length == 2) return false;" value="1">
                    <input type="submit" class="btn" name="add_to_cart" value="Añadir al carrito">
                </form>
            </div>
            <?php
                }
            }else{
                echo '<p class="empty">¡Aún no hay productos añadidos!</p>';
            }
            ?>
        </div>
    </section>

    <!-- ORDENAR -->
    <section class="order" id="order">
        <h1 class="heading">Ordenar Ahora</h1>
        <form action="" method="post">
            <div class="display-orders">
                <?php
                    $grand_total = 0;
                    $cart_item[] = '';
                    $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                    $select_cart->execute([$user_id]);
                    if($select_cart->rowCount() > 0){
                        while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                        $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                        $grand_total += $sub_total; 
                        $cart_item[] = '-'.$fetch_cart['name'].' (S/'.$fetch_cart['price'].' x '.$fetch_cart['quantity'].') ';
                        $total_products = implode($cart_item);
                        echo '<p>'.$fetch_cart['name'].' <span>(S/'.$fetch_cart['price'].' x '.$fetch_cart['quantity'].')</span></p>';
                        }
                    }else{
                        echo '<p class="empty"><span>¡Su carrito se encuentra vacío!</span></p>';
                    }
                ?>
            </div>

            <div class="grand-total"> Costo Total: <span>S/<?= $grand_total; ?></span></div>
            <input type="hidden" name="total_products" value="<?= $total_products; ?>">
            <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

            <div class="flex">
                <div class="inputBox">
                    <span>Nombre:</span>
                    <input type="text" name="name" class="box" required placeholder="Ingrese su nombre" maxlength="20">
                </div>
                <div class="inputBox">
                    <span>Número:</span>
                    <input type="number" name="number" class="box" required placeholder="Ingrese su número" min="0"
                        max="999999999" onkeypress="if(this.value.length == 9) return false;">
                </div>
                <div class="inputBox">
                    <span>Método de Pago</span>
                    <select name="method" class="box">
                        <option value="En efectivo">En efectivo</option>
                        <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                        <option value="Paypal">Paypal</option>
                        <option value="Yape">Yape</option>
                    </select>
                </div>
                <div class="inputBox">
                    <span>Dirección:</span>
                    <input type="text" name="flat" class="box" required placeholder="Donde lo ubicamos" maxlength="50">
                </div>
                <div class="inputBox">
                    <span>Referencia:</span>
                    <input type="text" name="street" class="box" required placeholder="Cerca a qué lugar"
                        maxlength="50">
                </div>
                <div class="inputBox">
                    <span>Código Postal:</span>
                    <input type="number" name="pin_code" class="box" required placeholder="ej. 04013" min="0"
                        max="99999" onkeypress="if(this.value.length == 5) return false;">
                </div>
            </div>
            <input type="submit" value="Ordenar Ahora" class="btn" name="order">
        </form>
    </section>

    <!-- CONSULTAS -->
    <section class="faq" id="faq">
        <h1 class="heading">Consultas</h1>
        <div class="accordion-container">
            <div class="accordion active">
                <div class="accordion-heading">
                    <span>¿Cómo puedo ordenar?</span>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accrodion-content">
                    Es muy facil, primero te registras en nuestra página para despúes ir a nuestra sección de Ordenar y
                    elegir su pizza. Una vez hecho eso, lo añades a su carrito y procedes a realizar la compra
                    rellenando sus datos.
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <span>¿Cómo me registro?</span>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accrodion-content">
                    Para poderse registrar es simple, se va en el ícono de usuario y completa los datos necesarios para
                    lograrse registrar en nuestra página y poder realizar sus compras.
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <span>¿Cuánto tiempo tarda la entrega?</span>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accrodion-content">
                    La entrega se realizará en menos de 30 minutos, en caso de que el pedido pase el tiempo estabecido,
                    su pizza será Gratis. (Esto dependerá en el lugar que viva)
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <span>¿Puedo ordenar grandes cantidades?</span>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accrodion-content">
                    Por su puesto, puede realizar la cantidad de pizzas que desee, el único limitante de su compra es su
                    billetera, no nosotros. Comparta su felicidad con sus seres más queridos.
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <span>¿Cómo me puedo contactar?</span>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accrodion-content">
                    Puede escribirnos a nuestro número que aparece abajo, de igual forma nos puede llamar o enviarnos un
                    correo electrónico para así contactarnos y brindarles un buen servicio.
                </p>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <div class="footer">
        <div class="box-container">
            <div class="box">
                <i class="fas fa-phone"></i>
                <h3>Números de Celular</h3>
                <p>+51 996034101</p>
                <p>(01) 412-1210</p>
            </div>

            <div class="box">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Local</h3>
                <p>Av. la Marina 2010, San Miguel 15088</p>
            </div>

            <div class="box">
                <i class="fas fa-clock"></i>
                <h3>Horario de Atención</h3>
                <p>08:00am hasta 11:30pm</p>
            </div>

            <div class="box">
                <i class="fas fa-envelope"></i>
                <h3>Correo Electrónico</h3>
                <p>breaktime@gmail.com</p>
                <p>breaktimestore@gmail.com</p>
            </div>
        </div>
        <div class="credit">
            &copy; copyright @ 2022 by <span>Mr. Breaktime</span> | Todos los derechos reservados!
        </div>
    </div>

    <!-- Enlace con Js -->
    <script src="./js/script.js"></script>
</body>

</html>