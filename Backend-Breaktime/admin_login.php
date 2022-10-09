<?php

    include 'config.php';
    session_start();
    if(isset($_POST['login'])){
        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $pass = sha1($_POST['pass']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name=? AND password=?");
        $select_admin->execute([$name, $pass]);
        $row = $select_admin->fetch(PDO::FETCH_ASSOC);

    if($select_admin->rowCount() > 0){
        $_SESSION['admin_id'] = $row['id'];
        header('location:admin_page.php');
    }else{
        $message[] = '¡Usuario o contraseña incorrecta!';
    }
    }

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Administración</title>
    <link rel="shortcut icon" type="image/x-icon" href="Img/img_logo/Logo Borde.png" />
    <!-- Fuente de Estilo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Enlace con CSS -->
    <link rel="stylesheet" href="css/admin_style.css">
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

    <section class="form-container">
        <form action="" method="post">
            <h3>Iniciar Sesión</h3>
            <p>Usuario determinado= <span>admin</span><br> Contraseña= <span>12345</span></p>
            <input type="text" name="name" required placeholder="Ingrese su Usuario" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" required placeholder="Ingrese su Contraseña" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Iniciar Sesión" class="btn" name="login">
        </form>
    </section>
</body>

</html>