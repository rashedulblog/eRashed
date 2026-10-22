<?php  
    require_once("./header.php");
    require_once("./db.php");

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    if (isset($_POST['changePassword'])) {
        $oldPassword = safeData($_POST['oldPassword']);
        $newPassword = safeData($_POST['newPassword']);
        $confirmNewPassword = safeData($_POST['confirmNewPassword']);

        if (empty($oldPassword)) {
            $errOldPassword = "Please write your old password";
        } elseif (!password_verify($oldPassword, $_SESSION['user']->password)) {
            $errOldPassword = "Old password is incorrect";
        }

        if (empty($newPassword)) {
            $errNewPassword = "Please write your new password";
        } elseif (strlen($newPassword) < 8) {
            $errNewPassword = "New password must be at least 8 characters long";
        }

        if (empty($confirmNewPassword)) {
            $errConfirmNewPassword = "Please confirm your new password";
        } elseif ($newPassword !== $confirmNewPassword) {
            $errConfirmNewPassword = "Passwords do not match";
        }

        if (!isset($errOldPassword) && !isset($errNewPassword) && !isset($errConfirmNewPassword)) {
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE `users` SET `password` = '$hashedNewPassword' WHERE `id` = {$_SESSION['user']->id}";
            if (db::conn()->query($updateQuery)) {
                $_SESSION['user']->password = $hashedNewPassword;
                header("Location: change-password.php?changePassword=true");
                exit;
            } else {
                header("Location: change-password.php?changePassword=false");
                exit;
            }
        }
    }

?>
<div class="container">
    <div class="row min-vh-100">
        <div class="col-md-5 m-auto border rounded shadow p-4">
            <h1 class="mb-3">Change Password</h1>
            <?php if (isset($_GET['changePassword']) && $_GET['changePassword'] == 'true') { ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Password changed successfully.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } else if (isset($_GET['changePassword']) && $_GET['changePassword'] == 'false') { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    Something went wrong.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="oldPassword">Old Password</label>
                    <input type="password" name="oldPassword" class="form-control <?= isset($errOldPassword) ? "is-invalid" : null ?>" id="oldPassword">
                    <div class="invalid-feedback"><?= $errOldPassword ?? null ?></div>
                </div>
                <div class="mb-3">
                    <label for="newPassword">New Password</label>
                    <input type="password" name="newPassword" class="form-control <?= isset($errNewPassword) ? "is-invalid" : null ?>" id="newPassword">
                    <div class="invalid-feedback"><?= $errNewPassword ?? null ?></div>
                </div>
                <div class="mb-3">
                    <label for="confirmNewPassword">Confirm New Password</label>
                    <input type="password" name="confirmNewPassword" class="form-control <?= isset($errConfirmNewPassword) ? "is-invalid" : null ?>" id="confirmNewPassword">
                    <div class="invalid-feedback"><?= $errConfirmNewPassword ?? null ?></div>
                </div>
                <div class="mb-3">
                    <button type="submit" name="changePassword" class="btn btn-dark">Change Password</button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php  
    require_once("./footer.php");
?>
    

    