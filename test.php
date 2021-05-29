<?php
session_start();

if(isset($_POST['logout'])){
    unset($_SESSION['user_ID']);
    unset($_SESSION['attempt_ID']);
    header('Location: https://ghordetest.000webhostapp.com/adrejLox/index.php');
}

function OnLoadPage(){
    //$_SESSION['attempt_ID']
    $test_id = json_decode(file_get_contents('http://18.192.116.51:84/api/Authorization/TestAttempt/' . $_SESSION['attempt_ID']))->testId;
    
    $questions = json_decode(file_get_contents('http://18.192.116.51:84/api/Process/MixQuestions?testId=' . $test_id));
    
    $string_to_return = '
    <center>
        <form method="post">';
                
    for($i = 0; $i<count($questions); $i++){
        $string_to_return .= '
        <table class="table">
            <thead>
                <tr>
                    <th scope="col" colspan="2">
                        <h3>';
        $string_to_return .= json_decode(file_get_contents('http://18.192.116.51:84/api/Crud/Question/' . $questions[$i]->id))->text;
        
        $list_of_variants = $questions[$i]->answersIds;
        $string_to_return .= '
                        </h3>
                    </td>
                </tr>
            </thead>
            <tbody>';
                
        for($j = 0; $j<count($list_of_variants); $j++){
            $string_to_return .= '
                <tr>
                    <th scope="row">
                        <input type="checkbox" class="form-check-input" name="'.$questions[$i]->id.'*'.$list_of_variants[$j].'">
                    </th>
                    <td>';
            $string_to_return .=  json_decode(file_get_contents('http://18.192.116.51:84/api/Crud/Answer/' . $list_of_variants[$j]))->text;
            $string_to_return .= '
                    </td>
                </tr>';
        }
        $string_to_return .= '
            </tbody>
        </table>';
    }
    $string_to_return .= '
                        <center>
                            <button type="submit" value="'. $test_id .'" name="endTest" class="btn btn-lg btn-success">
                                Закончить
                            </button>
                        </center>
        </form>
    </center>';
    return $string_to_return;
}

if(isset($_POST['endTest'])){
    $test_id = json_decode(file_get_contents('http://18.192.116.51:84/api/Authorization/TestAttempt/' . $_SESSION['attempt_ID']))->testId;
    
    $questions = json_decode(file_get_contents('http://18.192.116.51:84/api/Process/MixQuestions?testId=' . $test_id));
    $grade = 100/count($questions);
    $mark = 0.0;
    $strtort = '';
    
    for($i = 0; $i<count($questions); $i++){
         $list_of_variants = $questions[$i]->answersIds;
         $upGrade == false;
         for($j = 0; $j<=count($list_of_variants); $j++){
             $name_of_checkbox = $questions[$i]->id .'*'. $list_of_variants[$j];
             if($j==count($list_of_variants)){
                 $mark = $mark + $grade;
                 break;
             }
             if(json_decode(file_get_contents('http://18.192.116.51:84/api/Crud/Answer/' . $list_of_variants[$j]))->isCorrect != isset($_POST[$name_of_checkbox])){
                 break;
             }
         }
    }
    
    $data = json_decode(file_get_contents("http://18.192.116.51:84/api/Authorization/TestAttempt/". $_SESSION['attempt_ID']));
    $data->testGrade = var_dump(round($mark));
    $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
    $curl = curl_init('http://18.192.116.51:84/api/Authorization/SaveAttemptToUser?userId='. $_SESSION['user_ID']);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);
    header('Location: https://ghordetest.000webhostapp.com/adrejLox/user_page.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.css" rel="stylesheet" />

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark d-none d-lg-block" style="z-index: 2000;">
        <div class="container-fluid">
            <div class="names navbar-brand nav-link">
                <?=json_decode(file_get_contents('http://18.192.116.51:84/api/Authorization/User/'.$_SESSION['user_ID']))->firstName . ' ' . json_decode(file_get_contents('http://18.192.116.51:84/api/Authorization/User/'.$_SESSION['user_ID']))->lastName;?>
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
            <?=OnLoadPage();?>
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
            color: rgb(17, 16, 16);
            text-align: center;
            padding: 50px;
            background-color: white;
        }
        
        .tbs1 {
            margin-bottom: 50px;
        }
    </style>

    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.js"></script>
</body>

</html>