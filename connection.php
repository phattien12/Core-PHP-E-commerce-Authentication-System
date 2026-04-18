<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    require 'vendor/autoload.php';
    define('HOST', "127.0.0.1");
    define("PASS", "");
    define("USER", "root");
    define("DB", "LAB08");
    function open_database(){
        $conn = new mysqli(HOST, USER, PASS, DB);
        if($conn->connect_error){
            die();
        }
        return $conn;
    }

    function login($username, $password){
        $conn = open_database();
        $sql = "select * from account where username = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param("s", $username);
        if(!$stm->execute()){
            return array("code" => 1, "error" => "Error while inserting");
        }
        $result = $stm->get_result();
        $data =  $result->fetch_assoc();
        if(!$data){
            return array("code" => 1, "error" => "User does not exist");
        }
        $hashed_password = $data['password'];
        if(!password_verify($password, $hashed_password)){
            return array("code" => 2, "error" => "Wrong password");
        }
        else if($data['activated'] == 0){
            return array("code" => 3, "error" => "This account is not activated");
        }
        else{
            return array("code" => 0, "data" => $data, "msg" => "Login successfully");
        }
    }

    function create_account($username, $password, $email, $firstname, $lastname){
        if(is_email_exist($email)){
            return array("code" => 1, "error" => "Email has already been existed");
        }

        $conn = open_database();
        $sql = "insert into account (username, firstname, lastname, email, password, activate_token) values (?,?,?,?,?,?)";
        $token =md5( $username.'+'.rand(0, 100));
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stm = $conn->prepare($sql);
        $stm->bind_param("ssssss", $username, $firstname, $lastname, $email, $hashedPassword, $token);
        
        if(!$stm->execute()){
            return array("code" => 2, "error" => "Cannot execute the command");
        }
        send_activation_email($email, $token);
        return array("code" => 0, "msg" => "Account created successfully, login with the same credentials");
    }

    function is_email_exist($email){
        $conn = open_database();
        $sql = "select * from account where email = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param("s", $email);
        if(!$stm->execute()){
            return null;
        }
        $res = $stm->get_result();
        if($res->num_rows > 0){
            return true;
        }
        return false;
    }

    function send_activation_email($email, $token){
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'tai.nguyenphuong12@gmail.com';                     //SMTP username
            $mail->Password   = 'cwhznnxkqtctwyba';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom('tai.nguyenphuong12@gmail.com', 'Admin web');
            $mail->addAddress( $email, 'Recipient');     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Account verification';
            $mail->Body    = "Click <a href='http://localhost/activate.php?email=$email&token=$token'>this link</a> to activate your account";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function activate_account($email, $token){
        $conn = open_database();
        $sql = "select * from account where email = ? and activate_token = ? and activated = 0";

        $stm = $conn->prepare($sql);
        $stm->bind_param("ss", $email, $token);
        if(!$stm->execute()){
            return array("code" => 1, "error" => "Cannot access to the database");
        }
        $res = $stm->get_result();
        if($res->num_rows == 0){
            return array("code" => 2, "error" => "Email or token are not found");
        }

        $sql = "update account set activated = 1, activate_token = '' where email = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param("s", $email);

        if(!$stm->execute()){
            return array("code" => 1, "error" => "Cannot access to the database");
        }

        return array("code" => 0, "msg" => "Account activated successfully");
    }

    function send_reset_email($email, $token){
        $mail = new PHPMailer(true);
        
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'tai.nguyenphuong12@gmail.com';                     //SMTP username
            $mail->Password   = 'cwhznnxkqtctwyba';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        
            //Recipients
            $mail->setFrom('tai.nguyenphuong12@gmail.com', 'Admin web');
            $mail->addAddress( $email, 'Recipient');     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Reset account password';
            $mail->Body    = "Click <a href='http://localhost/reset_password.php?email=$email&token=$token'>this link</a> to reset your account password";
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function reset_password($email){
        $conn = open_database();
        if(!is_email_exist($email)){
            return;
        }
        $token = md5($email."+".random_int(1000,2000));
        $exp_on = time()+3600*24;
        $sql = "update reset_token set token = ?, expire_on = ? where email = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param("sis",$token, $exp_on, $email);

        if(!$stm->execute()){
            return array("code" => 1 , "error" => "Cannot access to the database");
        }

        if($stm->affected_rows == 0){
            $sql = "insert into reset_token values (?,?,?)";
            $stm = $conn->prepare($sql);

            $stm->bind_param("ssi", $email, $token, $exp_on);

            if(!$stm->execute()){
                return array("code" => 1 , "error" => "Cannot access to the database");
            }
        }

        //send reset password mail
        $data = send_reset_email($email, $token);
        return array("code" => 0, "data" => $data);
    }

    function reset_email_password($email, $token, $password){
        $conn = open_database();
        $sql = "update account set password = ? where email = ? and activated = 1";
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

        $stm = $conn->prepare($sql);
        $stm->bind_param("ss", $hashed_pass, $email);

        if(!$stm->execute()){
            return array("code" => 1, "error" => "Cannot access the database");
        }
        if($stm->affected_rows === 0){
            return array("code" => 2, "error" => "Email is not existed or is not activated");
        }

        $sql = "delete from reset_token where token = ? and email = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ss", $token, $email);
        if(!$stm->execute()){
            return array("code" => 1, "error" => "Cannot access the database");
        }

        return array("code" => 0, "msg" => "Password changed successfully");
    }

    function email_token_exists($email, $token){
        $conn = open_database();
        $sql = "select * from reset_token where email = ? and token = ?";
        $stm = $conn->prepare($sql);
        $stm->bind_param("ss", $email, $token);

        if(!$stm->execute()){
            return null;
        }

        $res = $stm->get_result();
        if($res->num_rows == 1){
            return true;
        }
        return false;
    }
    
    function fetch_product(){
        $conn = open_database();
        $sql = "select * from product";

        $stm = $conn->prepare($sql);

        if(!$stm->execute()){
            return array("code" => 1, "error" => "Could not connect to the database");
        }

        $res = $stm->get_result();
        if(!$res){
            return array("code" => 2, "error" => "There are no products");
        }

        return array("code" => 0, "data" => $res);
    }

    function delete_product($id){
        $conn = open_database();
        $sql = "delete from product where id = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param("i",$id);

        if(!$stm->execute()){
            return array("code" => 1, "error" => "Could not connect to the database");
        }

        return array("code" => 0, "msg" => "Delete product completed");
    }

    function edit_product($id, $name, $price, $desc){
        $conn = open_database();
        $sql = "update product set name = ?, price = ?, description = ? where id = ?";

        $stm = $conn->prepare($sql);
        $stm->bind_param("siss",$name, $price, $desc, $id);

        if(!$stm->execute()){
            return array("code" => 1, "error" => "Could not connect to the database");
        }

        if($stm->affected_rows == 0){
            return array("code" => 2, "error" => "Product does not exist");
        }

        return array("code" => 0, "msg" => "Update successfully");
    }

    function add_product($name, $price, $desc, $image, $temp_img){
        $conn = open_database();
        
        $sql = "insert into product (name, price, description, image) values (?,?,?,?)";

        $stm = $conn->prepare($sql);
        $stm->bind_param("siss", $name, $price, $desc, $image);

        if(!$stm->execute()){
            return array("code" => 1, "error" => "Could not access the database");
        }

        $local_image = "images/".$image;
        move_uploaded_file($temp_img, $local_image);

        return array("code" => 0, "msg" => "Product added successfully");
    }
?>