<?php

include 'config.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}

if(isset($_POST['update'])){

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $update_profile_name = $conn->prepare("UPDATE `admin` SET name = ? WHERE id = ?");
    $update_profile_name->execute([$name, $admin_id]);

    $prev_pass = $_POST['prev_pass'];
    $old_pass = sha1($_POST['old_pass']);
    $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
    $new_pass = sha1($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $confirm_pass = sha1($_POST['confirm_pass']);
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);
    $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

    if($old_pass != $empty_pass){
        if($old_pass != $prev_pass){
            $message[] = '¡Antigua contraseña no coincide!';
        }elseif($new_pass != $confirm_pass){
            $message[] = '¡Confirmar contraseña no coincide!';
        }else{
            if($new_pass != $empty_pass){
                $update_admin_pass = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
                $update_admin_pass->execute([$confirm_pass, $admin_id]);
                $message[] = '¡Contraseña actualizada con éxito!';
            }else{
                $message[] = '¡Por favor, introduzca nueva contraseña!';
            }
        }
    }else{
        $message[] = '¡Por favor, introduzca antigua contraseña!';
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Perfil</title>
    <link rel="shortcut icon" type="image/x-icon" href="Img/img_logo/Logo Borde.png" />
    <!-- Fuente de Estilo -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Enlace con CSS -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>

    <?php include 'admin_header.php' ?>

    <section class="form-container">
        <form action="" method="post">
            <h3>Actualizar Perfil</h3>
            <input type="hidden" name="prev_pass" value="<?= $fetch_profile['password']; ?>">
            <input type="text" name="name" value="<?= $fetch_profile['name']; ?>" required
                placeholder="enter your username" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="old_pass" placeholder="Antigua contraseña" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="new_pass" placeholder="Nueva contraseña" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="confirm_pass" placeholder="Confirmar contraseña" maxlength="20" class="box"
                oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Actualizar ahora" class="btn" name="update">
        </form>
    </section>


    <!-- Enlace con Js -->
    <script src="js/admin_script.js"></script>
</body>

</html>