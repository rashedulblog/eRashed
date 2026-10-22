<?php
require_once("./header.php");
require_once("./db.php");

if (isset($_POST['updateProfile'])) {
    $name = safeData($_POST['name']);
    $email = safeData($_POST['email']);
    $gender = safeData($_POST['gender']);
    $mobile = safeData($_POST['mobile']);
    $address = safeData($_POST['address']);

    if (empty($name)) {
        $errName = "Please write your name";
    } elseif (!preg_match('/^[A-Za-z.\- ]*$/', $name)) {
        $errName = "Inavalid name";
    } else {
        $crrName = db::conn()->real_escape_string($name);
    }

    if (empty($email)) {
        $errEmail = "Please write your email addrerss";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errEmail = "Inavalid email address";
    } else {
        if ($email != $_SESSION['user']->email) {
            $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = $email";
            $checkEmail = db::conn()->query($checkEmailQuery);
            if ($checkEmail->num_rows > 0) {
                $errEmail = "New email address iss already exicts in our database";
            } else {
                $crrEmail = db::conn()->real_escape_string($email);
            }
        }
        $crrEmail = db::conn()->real_escape_string($email);
    }

    if (empty($gender)) {
        $errGender = "Please select your gender";
    } elseif (!in_array($gender, ["Male", "Female"])) {
        $errGender = "Invalid gender";
    } else {
        $crrGender = db::conn()->real_escape_string($gender);
    }

    if (empty($mobile)) {
        $errMobile = "Please provide your mobile number";
    } elseif (!is_numeric($mobile) || strlen($mobile) < 10) {
        $errMobile = "Invalid moobile number";
    } else {
        $crrMobile = db::conn()->real_escape_string($mobile);
    }

    if (empty($address)) {
        $errAddress = "Please write your address";
    } elseif (strlen($mobile) < 10) {
        $errAddress = "Invalid address";
    } else {
        $crrAddress = db::conn()->real_escape_string($address);
    }

    if (isset($crrName) && isset($crrEmail) && isset($crrGender) && isset($crrMobile) && isset($crrAddress)) {
        $id = $_SESSION['user']->id;
        $updateProfileQuery = "UPDATE `users` SET `name` = '$crrName', `email` = '$crrEmail', `gender` = '$crrGender', `mobile` = '$crrMobile', `address` = '$crrAddress' WHERE `id` = $id";
        $updateProfile = db::conn()->query($updateProfileQuery);
        if ($updateProfile) {
            $_SESSION['user']->name =  $crrName;
            $_SESSION['user']->email =  $crrEmail;
            $_SESSION['user']->gender =  $crrGender;
            $_SESSION['user']->mobile =  $crrMobile;
            $_SESSION['user']->address =  $crrAddress;
            echo "<script>location.href = './profile.php?profileUpdate=true'</script>";
        } else {
            echo "<script>location.href = './profile.php?profileUpdate=false'</script>";
        }
    }
}

if (isset($_POST['upload123'])) {
    $fileName = $_FILES['picture']['name'];
    $tmpName = $_FILES['picture']['tmp_name'];

    if (empty($fileName)) {
        $errFile = "Please select a file";
    } elseif (!getimagesize($tmpName)) {
        $errFile = "Invalid image file";
    } else {
        if (!empty($_SESSION['user']->picture)) {
            unlink("./assets/images/".$_SESSION['user']->picture);
        }
        $newName = uniqid().rand(100000, 999999).time().$fileName;
        $destination = "./assets/images/".$newName;
        if(move_uploaded_file($tmpName, $destination)) {
            $_SESSION['user']->picture = $newName;
            $id = $_SESSION['user']->id;
            $updateDbQuery = "UPDATE `users` SET `picture` = '$newName' WHERE `id` = $id";
            $updateDb = db::conn()->query($updateDbQuery);
            if($updateDb) {
                echo "<script>location.href = './profile.php?upload=true'</script>";
            }else {
            echo "<script>location.href = './profile.php?upload=false'</script>";
            }
        } else {
            echo "<script>location.href = './profile.php?upload=false'</script>";
        }
    }
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            <?php if (isset($_GET['profileUpdate']) && $_GET['profileUpdate'] == 'true') { ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Profile updates successfully.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } else if (isset($_GET['profileUpdate']) && $_GET['profileUpdate'] == 'false') { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    Something went wrong.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <h1 class="mb-3">My Profile</h1>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="name">Name</label>
                    <input type="text" name="name" value="<?= $_SESSION['user']->name ?? null ?>" class="form-control <?= isset($errName) ? "is-invalid" : null ?>" id="name">
                    <div class="invalid-feedback"><?= $errName ?? null ?></div>
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="text" name="email" value="<?= $_SESSION['user']->email ?? null ?>" class="form-control <?= isset($errEmail) ? "is-invalid" : null ?>" id="email">
                    <div class="invalid-feedback"><?= $errEmail ?? null ?></div>
                </div>
                <div class="mb-0 <?= isset($errGender) ? "border border-danger rounded p-3" : null ?>">
                    <label for="" class="d-block">Gender</label>
                    <label for="male" class="me-4">
                        <input type="radio" name="gender" value="Male" id="male" class="form-check-inline" <?= isset($_SESSION['user']->gender) && $_SESSION['user']->gender === "Male" ? "checked" : null  ?>>Male
                    </label>
                    <label for="female">
                        <input type="radio" name="gender" value="female" id="female" class="form-check-inline" <?= isset($_SESSION['user']->gender) && $_SESSION['user']->gender === "Female" ? "checked" : null  ?>>Female
                    </label>
                </div>
                <div class="text-danger small mb-3"><?= $errGender ?? null ?></div>
                <div class="mb-3">
                    <label for="mobile">Mobile</label>
                    <input type="text" name="mobile" value="<?= $_SESSION['user']->mobile ?? null ?>" class="form-control <?= isset($errMobile) ? "is-invalid" : null ?>" id="mobile">
                    <div class="invalid-feedback"><?= $errMobile ?? null ?></div>
                </div>
                <div class="mb-3">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control <?= isset($errAddress) ? "is-invalid" : null ?>" style="resize: none;"><?= $_SESSION['user']->address ?? null ?></textarea>
                    <div class="invalid-feedback"><?= $errAddress ?? null ?></div>
                </div>
                <div class="mb-3">
                    <button type="submit" name="updateProfile" class="btn btn-dark">Update</button>
                </div>
            </form>
        </div>
        <div class="col-md-6 text-center border rounded shadow p-4">
            <?php if (isset($_GET['upload']) && $_GET['upload'] == 'true') { ?>
                <div class="alert alert-success alert-dismissible fade show">
                    New profile image uploaded successfully.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } else if (isset($_GET['upload']) && $_GET['upload'] == 'false') { ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    Something went wrong on profile image.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>
            <img src="<?= empty($_SESSION['user']->picture) ? "./assets/images/demo_profile_pic.jpg" : "./assets/images/".$_SESSION['user']->picture ?>" alt="" class="img-fluid img-thumbnail rounded-circle w-50 mb-4">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="pp">Change profile picture</label>
                </div>
                <div class="mb-3">
                    <input type="file" class="form-control <?= isset($errFile) ? "is-invalid" : null ?>" id="pp" name="picture">
                    <div class="invalid-feedback">
                        <?= $errFile ?? null ?>
                    </div>
                </div>
                <button type="submit" name="upload123" class="btn btn-dark">Upload</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once("./footer.php");
?>