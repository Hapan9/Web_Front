<?php
session_start();
    if(isset($_POST['enter'])){
        $data = ["login" => $_POST['userlogin'], "password" => $_POST['userpassword']];
        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
        $curl = curl_init('http://18.223.196.57:84/api/Authorization/Login');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($curl);
        curl_close($curl);
        if(json_decode($result) == null){
            $_SESSION['user_ID'] = file_get_contents('http://18.223.196.57:84/api/Authorization/GetUserIdByLogin?login='. $_POST['userlogin']);
            if(
            json_decode(file_get_contents('http://18.223.196.57:84/api/Authorization/User/'.$_SESSION['user_ID']))->role == "admin"){
                header('Location: http://3.135.244.197:228/admin.php');
            }
            else{
                header('Location: http://3.135.244.197:228/user_page.php');
            }
            
        }
    }
    if(isset($_POST['registration'])){
        $data = ["login" => $_POST['userlogin'], "password" => $_POST['userpassword'], "firstName" => $_POST['user_first_name'], "lastName" => $_POST['user_last_name']];
        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
        $curl = curl_init('http://18.223.196.57:84/api/Authorization/Register');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($curl);
        curl_close($result);
        if(json_decode($result)->status == 400){
            print_r('Такой юзер уже есть');
        }
        else{
            print_r('зарегано');
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
        <!-- Font Awesome -->
        <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
        rel="stylesheet"
        />
        <!-- Google Fonts -->
        <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
        rel="stylesheet"
        />
        <!-- MDB -->
        <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.css"
        rel="stylesheet"
    />
</head>
<body >
    <section class="authorization col-md-4 offset-md-4" >
        <div class="inner">
            <form class="bg-white rounded shadow-5-strong p-5" method="post">

                <div class="form-outline mb-4">
                  <input type="text" id="form1Example1" class="form-control" minlength="4" name="userlogin" required/>
                  <label class="form-label" for="form1Example1">Login</label>
                </div>
    
                <div class="form-outline mb-4">
                  <input type="password" id="form1Example2" class="form-control" minlength="4" name="userpassword" required>
                  <label class="form-label" for="form1Example2">Password</label>
                </div>
                
                <div class="form-outline mb-4" id="form1Example3" style="display: none;">
                  <input type="text" class="form-control" minlength="4" name="user_first_name" id="first_name_input">
                  <label class="form-label" for="form1Example1">First name</label>
                </div>
                
                <div class="form-outline mb-4" id="form1Example4" style="display: none;">
                  <input type="text" class="form-control" minlength="4" name="user_last_name" id="last_name_input">
                  <label class="form-label" for="form1Example1">Last name</label>
                </div>
    
                <div class="row">
                    <button type="submit" class="btn btn-primary col-md-5" name="enter" id="sign_in_btn">Sign in</button>
                    <button type="submit" class="btn btn-primary col-md-5  offset-md-2" name="registration" id="reg_btn">Registration</button>
                </div>
              </form>
        </div>
    </section>

    <script>
        
        document.getElementById("reg_btn").onclick = () => {
            document.getElementById("form1Example3").style.display = "block";
            document.getElementById("form1Example4").style.display = "block";
            document.getElementById("first_name_input").required = true;
            document.getElementById("last_name_input").required = true;
        }
        
        document.getElementById("sign_in_btn").onclick = () => {
            document.getElementById("first_name_input").required = false;
            document.getElementById("last_name_input").required = false;
        }
    </script>
    
    <style>
        body {
            background: url("https://look.com.ua/pic/201602/1920x1080/look.com.ua-150005.jpg");
            background-size: cover cover;
            background-position: top;
            background-attachment:fixed;
            background-repeat: no-repeat;
        }
        
        body:after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 2;
        }
        
        .inner {
          position: relative;
          z-index: 3;
          color: #fff;
          text-align: center;
          padding: 50px;  
        }
    </style>

<!-- MDB -->
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.js"
></script>
</body>
</html>
