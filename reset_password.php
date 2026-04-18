<?php
    require_once("connection.php");
    $error = '';
    $post_error = '';
    $msg = '';
    $email = '';
    $pass = '';
    $pass_confirm = '';
    $token = '';
    $display_email = filter_input(INPUT_GET, FILTER_SANITIZE_EMAIL);
    if(isset($_GET['email']) && isset($_GET['token'])){
        $display_email = $_GET['email'];
        $token = $_GET['token'];
        if(!filter_var($display_email, FILTER_SANITIZE_EMAIL)){
            $error = "This is not a valid email address";
        }
        else if(strlen($token) != 32){
            $error = "This is not a valid token";
        }
        else if(!email_token_exists($display_email, $token)){
            $error = "Invalid email or token";
        }
        else{
            if (isset($_POST['email']) && isset($_POST['pass']) &&
            isset($_POST['pass-confirm'])) {
                $email = $_POST['email'];
                $pass = $_POST['pass'];
                $pass_confirm = $_POST['pass-confirm'];
        
                if (empty($email)) {
                    $post_error = 'Please enter your email';
                }
                else if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                    $post_error = 'This is not a valid email address';
                }
                else if (empty($pass)) {
                    $post_error = 'Please enter your password';
                }
                else if (strlen($pass) < 6) {
                    $post_error = 'Password must have at least 6 characters';
                }
                else if ($pass != $pass_confirm) {
                    $post_error = 'Password does not match';
                }
                else {
                    $res = reset_email_password($email, $token, $pass);
                    if($res['code'] == 0){
                        $msg = $res['msg'];
                    }
                    else{
                        $error = $res['error'];
                    }
                }
            }
            else {
                $post_error = '';
            }
        }
    }
    else{
        $error = "Invalid email or token";
    }
    
?>
<DOCTYPE html>
<html lang="en">
<head>
    <title>Reset user password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h3 class="text-center text-secondary mt-5 mb-3">Reset Password</h3>
            <?php
                if(!empty($error)){
                    ?>
                        <div class='alert alert-danger text-center'><?=$error?></div>
                    <?php 
                }
                else if(!empty($msg)){
                    ?>
                        <div class='alert alert-success text-center'><?=$msg?></div>
                    <?php
                }
                else{
                    ?>
                    <form novalidate method="post" action="" class="border rounded w-100 mb-5 mx-auto px-3 pt-3 bg-light">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input readonly value="<?=$display_email?>" name="email" id="email" type="text" class="form-control" placeholder="Email address">
                        </div>
                        <div class="form-group">
                            <label for="pass">Password</label>
                            <input  value="<?= $pass?>" name="pass" required class="form-control" type="password" placeholder="Password" id="pass">
                            <div class="invalid-feedback">Password is not valid.</div>
                        </div>
                        <div class="form-group">
                            <label for="pass2">Confirm Password</label>
                            <input value="<?= $pass_confirm?>" name="pass-confirm" required class="form-control" type="password" placeholder="Confirm Password" id="pass2">
                            <div class="invalid-feedback">Password is not valid.</div>
                        </div>
                        <div class="form-group">
                            <?php
                                if(!empty($post_error)){
                                    ?>
                                    <div class='alert alert-danger text-center'><?=$post_error?></div>
                                    <?php
                                }
                            ?>
                            <button class="btn btn-success px-5">Change password</button>
                        </div>
                    </form>
                    <?php
                }
            ?>
        </div>
    </div>
</div>

</body>
</html>
