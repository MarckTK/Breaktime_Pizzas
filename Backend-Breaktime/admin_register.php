<?php

include 'config.php';
session_start();
$admin_id=$_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
};

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);
    $cpass = sha1($_POST['cpass']);
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admin` WHERE name=? ");
    $select_admin->execute([$name]);
    
    if($select_admin->rowCount() > 0){
        $message[] = '¡Nombre de ususario existente!';
    }else{
        if($pass = !$cpass){
            $message [] = '¡Confirmar contraseña no coincide!';
        }else{
            $insert_admin =$conn->prepare("INSERT INTO `admin` (name, password) VALUES(?.?)");
            $insert_admin->execute([$name,$cpass]);
            $message[]='Administrador registrado correctamente';
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
    <title>Iniciar Registro</title>
    <link rel="shortcut icon" type="image/x-icon" href="Img/img_logo/Logo Borde.png" />
    <!-- Fuente de Estilo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Enlace con CSS -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php'?>

    <section class="form-container">
        <form action="" method="post">
            <h3>Registrar ahora</h3>

            <input type="text" name="name" required placeholder="Ingrese usuario" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" required placeholder="Ingrese contraseña" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="cpass" required placeholder="Confirmar contraseña" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">

            <input type="submit" value="Registrarse" class="btn" name="register">
        </form>
    </section>


    <script src="js/admin_script.js"></script>

</body>

</html>