<?php
require_once('../../utils/show_errors.php');
require_once('../../utils/check_form.php');
require_once('../../database/db_functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$error) {
    $conn = connect_database();

    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);
    $hash_password = md5(mysqli_real_escape_string($conn, $password));

    $sql = "INSERT INTO $table_users
            (user_name, user_email, user_password) VALUES
            ('$username', '$email', '$hash_password');";

    $success = false;
    if (mysqli_query($conn, $sql)) {
        $success = true;
    }
    else {
        $username_error = mysqli_error($conn);
        $error = true;
    }

    // login
    session_start();
    $sql = "SELECT user_id, user_name, user_email, user_password, user_is_admin FROM $table_users WHERE user_email = '$email';";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["user_name"] = $user["user_name"];
    $_SESSION["user_email"] = $user["user_email"];
    $_SESSION["user_is_admin"] = $user["user_is_admin"];
    $_SESSION["remember"] = $remember;
    if ($remember) {
        setcookie("remember", "true", time() + (3600 * 24 * 7), "/");
    } else {
        setcookie("remember", "false", time() + 3600, "/");
    }
    disconnect_db($conn);
    header("Location: " . '../blog/blog.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./register.css">
    <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js"
        integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY="
        crossorigin="anonymous"></script>
    <script
        src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js">
    </script>
    <script
        src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js">
    </script>
    <script src="./register.js"></script>

    <title>Registrar</title>
</head>

<body>
    <p class="aviso-registro">Faça o registro para acessar o blog do xxMarceloo</p>
    <form id="register-form"
        action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="imgcontainer">
            <img src="../../img/avatar.png" alt="Avatar" class="avatar">
        </div>

        <div class="container">
            <div class="form-group">
                <label for="username"><b>Usuário</b></label>
                <input type="text" placeholder="Insira o usuário.." name="username"
                    value="<?php if (!empty($username)) echo $username; ?>"
                    class="form-control <?php if(!empty($username_error)){echo "has-error";}?>"
                    id="username" required>

                <?php if (!empty($username_error)): ?>
                <span class="help-block"><?php echo $username_error ?></span>
                <?php endIf; ?>
            </div>

            <div class="form-group">
                <label for="email"><b>Email</b></label>
                <input type="email" placeholder="Insira o email.." name="email"
                    value="<?php if (!empty($email)) echo $email; ?>"
                    class="form-control <?php if(!empty($email_error) || !empty($invalid_email_error)){echo "has-error";}?>"
                    id="email" required>

                <?php if (!empty($email_error)): ?>
                <span class="help-block"><?php echo $email_error ?></span>
                <?php endIf; ?>

                <?php if (!empty($invalid_email_error)): ?>
                <span
                    class="help-block"><?php echo $invalid_email_error ?></span>
                <?php endIf; ?>
            </div>

            <div class="form-group">
                <label for="password"><b>Senha</b></label>
                <input type="password" placeholder="Insira a senha.."
                    name="password"
                    value="<?php if (!empty($password)) echo $password; ?>"
                    class="form-control <?php if(!empty($password_error)){echo "has-error";}?>"
                    id="password" required>

                <?php if (!empty($password_error)): ?>
                <span class="help-block"><?php echo $password_error ?></span>
                <?php endIf; ?>
            </div>

            <div class="form-group">
                <label for="password-confirmation"><b>Confirme a senha</b></label>
                <input type="password" placeholder="Confirme a senha.."
                    name="password-confirmation"
                    value="<?php if (!empty($password_confirmation)) echo $password_confirmation; ?>"
                    class="form-control <?php if(!empty($password_confirmation_error) || !empty($password_mismatch_error)){echo "has-error";}?>"
                    id="password-confirmation" required>

                <?php if (!empty($password_confirmation_error)): ?>
                <span
                    class="help-block"><?php echo $password_confirmation_error ?></span>
                <?php endIf; ?>

                <?php if (!empty($password_mismatch_error)): ?>
                <span
                    class="help-block"><?php echo $password_mismatch_error ?></span>
                <?php endIf; ?>
            </div>

            <div id="js-errors"></div>

            <div class="submit-register">
                <label>
                    <input type="checkbox" checked="checked" name="remember">
                    Mantenha conectado
                </label>
                <button type="submit" class="btn btn-success">Registrar</button>
            </div>
        </div>

        <div class="container" style="display: flex">
            <span class="register">Já tem uma conta?&nbsp;<a
                    href="../login/login.php">
                    Login!</a></span>
        </div>
    </form>
</body>

</html>