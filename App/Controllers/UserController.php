<?php

namespace App\Controllers;

require_once __DIR__ . '/../../public/vendor/autoload.php';
use App\Model\User;
use App\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class UserController{

    var $file_path;

    public function __construct(){
        $this->file_path = dirname(__DIR__);
        $this->file_path = str_replace('/App', '/public/assets/html', $this->file_path);
        $this->db = Database::connect();
        $this->userModel = new User($this->db);
    }


    public function check_infos(string $email, string $password){

        if (filter_var($email, FILTER_VALIDATE_EMAIL)){
            if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password)) {
                return false;
            }
        }else{
            return false;}

        return true;
    }


    public function mailer(){

        $mail = new PHPMailer(true);

        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->isSMTP();
        $mail->SMTPAuth = true;

        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Username = 'esrablk9@gmail.com';
        $mail->Password = 'dupr rmfm riuu vwrr ';

        $mail->isHtml(true);

        return $mail;

    }


    public function activate_account(){

        if ($this->userModel->activate_account())
            include $this->file_path . '/account_activated.html';
        else
            echo "Error";
    }



    public function register(){

        $this->file_path = $this->file_path . '/register.html';

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            include $this->file_path;
            return ;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($this->check_infos($email, $password)){

                $password = password_hash($password, PASSWORD_DEFAULT);

                $activation_token = bin2hex(random_bytes(16));
                $activation_token_hash = hash("sha256", $activation_token);

                $result = $this->userModel->register($name, $password, $email, $activation_token_hash);

                $mail = $this->mailer();

                if ($result === 1){
                    $mail->setFrom('esrablk9@gmail.com', 'camagru');
                    $mail->addAddress($email);
                    $mail->Subject = "Account Activation";
                    $mail->Body = <<<END
                    Click <a href="http://localhost:8000/activate_account?token=$activation_token">here</a>
                    to activate your account.
                    END;

                    try {
                        $mail->send();
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
                    }

                    $message = "Signup success. Please check your email to activate your account.";
                }elseif ($result === -1){
                    $message =  "Error: Duplicate username";
                }elseif ($result === -2){
                    $message =  "Error: Saving";
                }

            }else{
                $message = "Not safety values for register";
            }
            include $this->file_path;

        }


    }


    public function login(){

        $message = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST"){

            $result = $this->userModel->login($_POST['username'], $_POST['password']);

            if ($result == -1){
                $message = "Please activate your account first!";
            }elseif($result == 0){
                $message = "Invalid credentials!";
            }else{
                session_start();
                $_SESSION['user'] = $result['username'];
                $_SESSION['id'] = $result['id'];
                $_SESSION['password'] = $result['password'];
                $_SESSION['email'] = $result['email'];
                header("Location: /editing");
                exit;
            }

        }


        include $this->file_path . '/login.html';
    }


    public function reset_password_mail(){

        if ($_SERVER["REQUEST_METHOD"] === "GET"){

            $token = $_GET["token"];
            $result = $this->userModel->reset_password_mail($token);

            if ($result === -1)
                die("Token not found!");
            elseif ($result === -2)
                die("Token has expired!");
        }


        if ($_SERVER["REQUEST_METHOD"] === "POST"){

            $token = $_POST["token"];
            $password = $_POST["password"];
            $password_confirmation = $_POST["password_confirmation"];
            $message = "";


            if (strlen($password) < 8)
                $message = "Password must be at least 8 character";
            elseif (!preg_match("/[a-z]/i", $password))
                $message = "Password must contain at least one letter";
            elseif (!preg_match("/[0-9]/", $password))
                $message = "Password must contain at least one number";
            elseif ($password !== $password_confirmation)
                $message = "Passwords must match";
            else{
                $result = $this->userModel->process_reset_password($token, $password);
                if ($result === -1)
                    echo "Token not found!";
                elseif ($result === -2)
                    echo "Token has expired!";
                else
                    $message = "Password updated. You can now login.";
            }
        }
        include $this->file_path . '/reset_password_mail.html';
    }


    public function reset_password(){

        $message = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST"){

            $email = $_POST['email'];

            $token = $this->userModel->reset_password_hashes($email);
            if ($token){

                $mail = $this->mailer();

                $mail->setFrom('esrablk9@gmail.com', 'camagru');
                $mail->addAddress($email);
                $mail->Subject = "Reset Password";
                $mail->Body = <<<END
                Click <a href="http://localhost:8000/reset_password_mail?token=$token">here</a>
                to activate your account.
                END;

                try {
                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
                }

                $message = "Email send!";

            }else{
                $message = "Password hashing error!";
            }
        }
        include $this->file_path . '/reset_password.html';
    }


    public function logout(){

        session_start();
        session_destroy();
        header("Location: /login");

    }


    public function features(){

        session_start();
        if(!isset($_SESSION['user'])){
            header("Location: /login");
            exit;
        }
        
        $username = $_SESSION['user'];
        $password = $_SESSION['password'];
        $email = $_SESSION['email'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_username = $_POST['username'];
            $new_password = $_POST['password'];
            $new_email = $_POST['email'];

            error_log($username . " " . $password . " " . $email);
            error_log($new_username . " " . $new_password . " " . $new_email);

            if ($new_username && $new_password && $new_email){
                if (password_verify($new_password, $password) && $new_username == $username && $new_email == $email){
                    $message = "Everything is same nothing updated!";
                }else{
                    if ($this->check_infos($new_email, $new_password)){
                        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $this->userModel->updateInfos($new_username, $new_password, $new_email, $_SESSION['id']);
                        $message = "Sucessfully updated";
                    }else{
                        $message = "Not valid parameters";
                    }
                }
            }else{
                $message = "No empty value nothing updated!";
            }

        }

        include $this->file_path . '/features.html';
    }

}


?>