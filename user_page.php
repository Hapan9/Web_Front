<?php
session_start();
if(isset($_POST['logout']) || !isset($_SESSION['user_ID'])){
    unset($_SESSION['user_ID']);
    unset($_SESSION['attempt_ID']);
    header('Location: http://3.135.244.197:228/index.php');
}

if(isset($_POST['startTest'])){
    if(json_decode(file_get_contents("http://18.223.196.57:84/api/Authorization/GetUserAttempts?id=". $_POST['startTest']))->testState == 1){
        header("Refresh:0");
    }
    else{
        $_SESSION['attempt_ID'] = $_POST['startTest'];
        $data = json_decode(file_get_contents("http://18.223.196.57:84/api/Authorization/TestAttempt/". $_POST['startTest']));
        $data->testState = 1;
        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
        $curl = curl_init('http://18.223.196.57:84/api/Authorization/SaveAttemptToUser?userId='. $_SESSION['user_ID']);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($curl);
        header('Location: http://3.135.244.197:228/test.php');
    }
}

function GetAllAttempts(){
    $attempts_array = json_decode(file_get_contents("http://18.223.196.57:84/api/Authorization/GetUserAttempts?id=". $_SESSION['user_ID']));
    $strint_to_return = '
    <form method="post">
        <table class="table">
            <thead>
                  <tr>
                    <th scope="col">№</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Grade</th>
                  </tr>
            </thead>
            <tbody>';
    for($i = 0; $i<count($attempts_array); $i++){
        $strint_to_return .= '
            <tr>
                <th scope="row">'. htmlspecialchars($i+1) .'</th>
                <td>';
                    $strint_to_return .= htmlspecialchars(json_decode(file_get_contents("http://18.223.196.57:84/api/Crud/Test/" . $attempts_array[$i]->testId))->name);
        $strint_to_return .= '
                </td>
                <td>';
        if($attempts_array[$i]->testState==1){
            $strint_to_return .= '
                    Done
                </td>
                <td>';
            $strint_to_return .= $attempts_array[$i]->testGrade;
        }
        else{
            $strint_to_return .='
                    Not done
                </td>
                <td>
                    <center>
                        <button value="'.htmlspecialchars($attempts_array[$i]->id).'" name="startTest" class="btn btn-success form-control">
                            Start test
                        </button>
                    </center>';
        }
        $strint_to_return .= '
                </td>
            </tr>';
    }
    $strint_to_return .= '
            </tbody>
        </table>
    </form>';
    
            
    return $strint_to_return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
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

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark d-none d-lg-block" style="z-index: 2000;">
        <div class="container-fluid">
            <div class="names navbar-brand nav-link">
                <?=json_decode(file_get_contents('http://18.223.196.57:84/api/Authorization/User/'.$_SESSION['user_ID']))->firstName . ' ' . json_decode(file_get_contents('http://18.223.196.57:84/api/Authorization/User/'.$_SESSION['user_ID']))->lastName;?>
            </div>
            <div class="logout navbar-brand nav-link">
                <form style="display: inline" method="post">
                    <input type="submit" name="logout" value="Выйти" class="btn btn-primary btn-lg">
                </form>
            </div>
        </div>

    </nav>

<section class="sec container">
    <div class="inner row mt-5 mb-5">
        <?=GetAllAttempts();?>
    </div>
</section>
        
    


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
        background-color: white;
        }
</style>

<!-- MDB -->
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.js"
></script>
</body>
</html>
