<?php
require_once("./header.php");
require_once("./db.php");

if(!isset($_GET['success'])) {
    if(isset($_SESSION['user'])) {
        header("Location: ./");
        exit;
    }
}

if (isset($_POST['login123'])) {
    $email = safeData($_POST['email']);
    $password = safeData($_POST['password']);
    $errs = [];

    if (empty($email)) {
        array_push($errs, "Please write your email address");
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errs, "Invalid email address");
    } else {
        $crrEmail = db::conn()->real_escape_string($email);
    }

    if (empty($password)) {
        array_push($errs, "Please write your password");
    } else {
        $crrPass = db::conn()->real_escape_string($password);
    }

    if(isset($crrEmail) && isset($crrPass)){
        $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$crrEmail'";
        $checkEmail = db::conn()->query($checkEmailQuery);
        if($checkEmail->num_rows == 1) {
            $getUserData = $checkEmail->fetch_object();
            if(!password_verify($crrPass, $getUserData->password)){
                array_push($errs, "Invalid Email or Password");
            }else {
                $_SESSION['user'] = $getUserData;
                header("Location: log-in.php?success=1");
                exit;
            }
        }else {
            array_push($errs, "Invalid Email or Password");
        }
    }
}

?>
<div class="container">
    <div class="row">
        <div class="col-md-5 p-4 border rounded shadow mx-auto my-5">
            <?php
            if (isset($errs) && count($errs) > 0):
                foreach ($errs as $err):
            ?>
                <div class="alert alert-danger alert-dismissible">
                    <?= $err ?>
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php
                endforeach;
            endif
            ?>
            <h1 class="fs-3 mb-3">Login form</h1>
            <?php 
            if (isset($_GET['success'])) { 
                ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Login Successfull
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <script>
                    setTimeout(() => {
                        location.href = "./"
                    }, 2000)
                </script>
            <?php } ?>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="email">
                        Email Address :
                    </label>
                    <input type="text" class="form-control" id="email" name="email" value="<?= $email ?? null ?>">
                </div>
                <div class="mb-3">
                    <label for="pass">
                        Password :
                    </label>
                    <input type="password" class="form-control" id="pass" name="password">
                </div>
                <div class="mb-3">
                    <label for="showPass" class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="showPass"> Show Password
                    </label>
                </div>
                <div class="mb-3">
                    <button class="btn btn-dark" name="login123">Log in</button>
                </div>
                <div class="mb-3">
                    <div class="mb-3 small">
                        Don't have accoint? <a href="./register.php">Register Here</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const showPass = document.getElementById("showPass");
    const pass = document.getElementById("pass");

    showPass.addEventListener("change", e => {
        if (e.target.checked) {
            pass.type = "text";
        } else {
            pass.type = "password";
        }
    })
</script>

<?php
require_once("./footer.php");
?>