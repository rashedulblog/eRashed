<?php
require_once("./header.php");
require_once("./db.php");

if (isset($_SESSION['user'])) {
    header("Location: ./");
    exit;
}


if (isset($_POST['reg123'])) {
    $name = safeData($_POST["name"]);
    $email = safeData($_POST["email"]);
    $password = safeData($_POST["password"]);
    $confirmPassword = safeData($_POST["confirmPassword"]);
    $agree = safeData($_POST["agree"] ?? null);
    $result = safeData($_POST["result"]);
    $Num1 = safeData($_POST["num1"]);
    $Num2 = safeData($_POST["num2"]);

    if (!isset($agree) || $agree !== "yes") {
        $errAgree = "Please aggre with terms & condition";
    } else {
        if (empty($name)) {
            $errName = "Please write your name";
        } elseif (!preg_match("/^[A-Za-z.\- ]*$/", $name)) {
            $errName = "Invalid name";
        } else {
            $crrName = db::conn()->real_escape_string($name);
        }

        if (empty($email)) {
            $errEmail = "Please write your name";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errEmail = "Invalid email address";
        } else {
            $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
            $checkEmail = db::conn()->query($checkEmailQuery);
            if ($checkEmail->num_rows > 0) {
                $errEmail = "Email address already exicts";
            } else {
                $crrEmail = db::conn()->real_escape_string($email);
            }
        }

        if (empty($password)) {
            $errPass = "Please write your password";
        } else if (strlen($password) < 8) {
            $errPass = "Please write a strong password";
        } else {
            $crrPass =  db::conn()->real_escape_string($password);
        }

        if (empty($confirmPassword)) {
            $errConPass = "Please confirm your password";
        } else if ($confirmPassword !== $password) {
            $errConPass = "Password didnot matched";
        } else {
            $crrConPass =  db::conn()->real_escape_string($confirmPassword);
        }

        if ((int)$result != ((int)$Num1 + (int)$Num2)) {
            $errRes = "Wrong Answer";
        } else {
            $crrRes = $result;
        }

        if (isset($crrName) && isset($crrEmail) && isset($crrPass) && isset($crrConPass) && isset($crrRes)) {
            $pass = password_hash($crrPass, PASSWORD_DEFAULT);
            $query = "INSERT INTO `users` (`name`, `email`, `password`) VALUES ('$crrName', '$crrEmail', '$pass')";
            if (db::conn()->query($query)) {
                header("Location: register.php?success=1");
                exit;
            } else {
                $isSuccess = false;
            }
        }
    }
}

$num1 = rand(1, 9);
$num2 = rand(1, 9);
?>
<div class="container">
    <div class="row">
        <div class="col-md-5 p-4 border rounded shadow mx-auto my-5">
            <h1 class="fs-3 mb-3">Registration form</h1>
            <?php if (isset($errAgree)) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $errAgree ?>
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Registration successfull! please <a href="./log-in.php">Log in</a> Now
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <?php if (isset($isSuccess) && $isSuccess === false) { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    Database Problem. please try again latter.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <form action="" method="post">
                <div class="mb-3">
                    <input type="text" placeholder="Your Name" class="form-control <?= isset($errName) ? "is-invalid" : null ?>" name="name" value="<?= $name ?? null ?>">
                    <div class="invalid-feedback">
                        <?= $errName ?? null ?>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" placeholder="Email Address" class="form-control <?= isset($errEmail) ? "is-invalid" : null ?>" name="email" value="<?= $email ?? null ?>">
                    <div class="invalid-feedback">
                        <?= $errEmail ?? null ?>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="password" placeholder="Password" class="form-control <?= isset($errPass) ? "is-invalid" : null ?>" id="pass" name="password">
                    <div class="invalid-feedback">
                        <?= $errPass ?? null ?>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="password" placeholder="Confirm Password" class="form-control <?= isset($errConPass) ? "is-invalid" : null ?>" id="cPass" name="confirmPassword">
                    <div class="invalid-feedback">
                        <?= $errConPass ?? null ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="showPass" class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="showPass"> Show Password
                    </label>
                </div>
                <div class="mb-3">
                    <label for="agree" class="form-check-label">
                        <input type="checkbox" class="form-check-input" id="agree" name="agree" value="yes"> <span class="small">I agree with <a href="./terms-n-condition.php">terms & condition</a></span>
                    </label>
                </div>
                <div class="mb-3 d-flex align-items-center gap-2 px-1">
                    <input type="hidden" value="<?= $num1 ?>" name="num1">
                    <input type="hidden" value="<?= $num2 ?>" name="num2">
                    <?= $num1 ?> + <?= $num2 ?> =
                    <input type="text" class="form-control" style="width: 80px;" name="result">
                    <div class="small text-danger">
                        <?= $errRes ?? null ?>
                    </div>
                </div>
                <div class="mb-3">
                    <button type="submit" name="reg123" class="btn btn-dark">Register</button>
                </div>
                <div class="mb-3 small">
                    Already have accoint? <a href="./log-in.php">Login Here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const showPass = document.getElementById("showPass");
    const pass = document.getElementById("pass");
    const cPass = document.getElementById("cPass");

    showPass.addEventListener("change", e => {
        if (e.target.checked) {
            pass.type = "text";
            cPass.type = "text";
        } else {
            pass.type = "password";
            cPass.type = "password";
        }
    })
</script>

<?php
require_once("./footer.php");
?>