<?php
session_start();

if(isset($_POST['logout'])){
    unset($_SESSION['user_ID']);
    unset($_SESSION['attempt_ID']);
    header('Location: https://ghordetest.000webhostapp.com/adrejLox/index.php');
}

if(isset($_POST['create_new_theme'])){
    
    $data = ["id" => "00000000-0000-0000-0000-000000000000", "name" => $_POST['new_theme_name']];
    $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
    $curl = curl_init('http://18.193.128.69:84/api/Crud/TestTheme');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);
    curl_close($curl);
    header("Refresh:0");
}

if(isset($_POST['create_test_btn'])){
    if(!($_POST['create_test_btn']>=0)){
        header("Refresh:0");
    }
    
    $data = ["id"=> "00000000-0000-0000-0000-000000000000", "name"=> $_POST['test_name'], "description"=> $_POST['test_description'], "questionsIds"=> [], "testThemeId"=> $_POST['selected_theme']];
    $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
    $curl = curl_init('http://18.193.128.69:84/api/Crud/Test');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);
    curl_close($curl);
    $testid = substr($result, 1, -1);
    $questions_ids = array();
    
    for($i = 0; $i<$_POST['create_test_btn']; $i++){
        $data = ["id" => "00000000-0000-0000-0000-000000000000", "text" => $_POST['question_' . $i], "answersIds" => [], "testId" => $testid];
        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
        $curl = curl_init('http://18.193.128.69:84/api/Crud/Question');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($curl);
        curl_close($curl);
        
        $question_id = substr($result, 1, -1);
        array_push($questions_ids, $question_id);
        $answers_ids = array();
        for($j = 0; $j<$_POST['all_answers_'. $i]; $j++){
            $data = ["id" => "00000000-0000-0000-0000-000000000000", "text" => $_POST['answer_text_' . $i . '_' . $j], "isCorrect" => isset($_POST['answer_checkbox_' . $i . '_' . $j]), "questionId" => $question_id];
            $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
            $curl = curl_init('http://18.193.128.69:84/api/Crud/Answer');
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
               'Content-Type: application/json',
               'Content-Length: ' . strlen($data_string))
            );
            $result = curl_exec($curl);
            curl_close($curl);
            
            array_push($answers_ids, substr($result, 1, -1));
        }
        
        $data = json_decode(file_get_contents("http://18.193.128.69:84/api/Crud/Question/". $question_id));
        $data->answersIds = $answers_ids;
        $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
        
        $curl = curl_init('http://18.193.128.69:84/api/Crud/Question');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($curl);
        curl_close($curl);
    }
    
    $data = json_decode(file_get_contents("http://18.193.128.69:84/api/Crud/Test/". $testid));
    $data->questionsIds = $questions_ids;
    $data_string = json_encode ($data, JSON_UNESCAPED_UNICODE);
    $curl = curl_init('http://18.193.128.69:84/api/Crud/Test');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);
    curl_close($curl);
    header("Refresh:0");
}

if(isset($_POST['add_attempts'])){
    for($i=0; $i<$_POST['count_of_attempts']; $i++){
        $result = file_get_contents('http://18.193.128.69:84/api/Authorization/AddNewAttemptToUser?userId='.$_POST['selected_user'].'&testId='.$_POST['selected_test']);
    }
    header("Refresh:0");
}

function AllThems(){
    $all_thems = json_decode(file_get_contents('http://18.193.128.69:84/api/Crud/TestTheme'));
    $sting_to_returm = '';
    for($i = 0; $i<count($all_thems);$i++){
        $sting_to_returm .= '
        <option';
        if($i==0){
            $sting_to_returm .= ' selected ';
        }
        $sting_to_returm .= ' value="'.$all_thems[$i]->id.'">'. $all_thems[$i]->name .'</option>';
    }
    return $sting_to_returm;
}

function AllUsers(){
    $all_users = json_decode(file_get_contents('http://18.193.128.69:84/api/Authorization/User'));
    $sting_to_returm = '';
    for($i = 0; $i<count($all_users);$i++){
        $sting_to_returm .= '
        <option';
        if($i==0){
            $sting_to_returm .= ' selected ';
        }
        $sting_to_returm .= ' value="'.$all_users[$i]->id.'">'. $all_users[$i]->firstName .' ' . $all_users[$i]->lastName .  '</option>';
    }
    return $sting_to_returm;
}

function AllTests(){
    $all_tests = json_decode(file_get_contents('http://18.193.128.69:84/api/Crud/Test'));
    $sting_to_returm = '';
    for($i = 0; $i<count($all_tests);$i++){
        $sting_to_returm .= '
        <option';
        if($i==0){
            $sting_to_returm .= ' selected ';
        }
        $sting_to_returm .= ' value="'.$all_tests[$i]->id.'">'. json_decode(file_get_contents('http://18.193.128.69:84/api/Crud/TestTheme/' . $all_tests[$i]->testThemeId))->name . ' - ' . $all_tests[$i]->name . ' : ' . $all_tests[$i]->description . '</option>';
    }
    return $sting_to_returm;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
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
                <?=json_decode(file_get_contents('http://18.193.128.69:84/api/Authorization/User/'.$_SESSION['user_ID']))->firstName . ' ' . json_decode(file_get_contents('http://18.193.128.69:84/api/Authorization/User/'.$_SESSION['user_ID']))->lastName;?>
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
        <form class="border-bottom mb-5" method="post">
            <div class="form-group row">
                <h2 style="color:#000000">Test</h2>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="test_name">
                </div>
                <div class="col-md-4">
                    <h4 class="mt-5" style="color:#000000; display:inline;">Name</h4>
                </div>
            </div>
            <div class="form-group row mt-5 ">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="test_description">
                </div>
                <div class="col-md-4">
                    <h4 class="mt-5" style="color:#000000; display:inline;">Description</h4>
                </div>
            </div>
            <div class="form-group row">
                <h2 class="mt-5" style="color:#000000">Theme</h2>
                <div class="col-md-8">
                    <select class="custom-select form-control mb-4" name="selected_theme"><?=AllThems();?>
                    </select>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-secondary form-control " id="createTheme">Create new theme</a>
                </div>
            </div>
            <div class="form-group row" id="form-group-class-to-change">
                <div class="col-md-8" id="createForm">
    
                </div>
                <div class="col-md-4" id="createButton">
    
                </div>
            </div>
            <div id="question-creating">
                <div class="question">
                   
                </div>
            </div>
            <div class="form-group row">
                <h2 class="mt-5" style="color:#000000">Create new question</h2>
                <div class="col-md-8" id="createForm">
                <input type="number" class="form-control" min="1" value="4" id="count_of_answers"></div>
                <div class="col-md-4">
                    <a class="btn btn-secondary form-control " id="newQuestion">Create new question</a>
                </div>
            </div>
            <button class="form-control mt-5 btn btn-rounded btn-lg btn-success" id="create_test_btn" name="create_test_btn">
                Create test
            </button>
            </div>
            </div>
            </div>

            </div>
        </form>
    </div>
</section>

<section class="sec container">
    <div class="inner row mt-5 mb-5">
    
    <form method="post">
            <select class="custom-select form-control mb-4" name="selected_user">
                <?=AllUsers();?>
            </select>
        
            <select class="custom-select form-control mb-4" name="selected_test">
                <?=AllTests();?>
            </select>
        
            <input type="number" placeholder="Amount" class="form-control mb-4" name="count_of_attempts" min="1" required>
            
            <button class="btn btn-lg btn-danger" name="add_attempts">
                Submit
            </button>
    </form>
    </div>
</section>
    
    <script>
    let questions = 0;
        document.getElementById("createTheme").onclick = () => {

            let toBlock = document.getElementById("createTheme");
            

            let form = document.getElementById("createForm");

            let element = document.createElement("input");
            element.name = "new_theme_name";
            element.type = "text";
            element.classList.add("form-control");

            form.appendChild(element);

            let buttonDiv = document.getElementById("createButton");

            let button = document.createElement("button");
            button.innerHTML = "Create";

            button.classList.add("form-control");
            button.classList.add("btn");
            button.classList.add("btn-primary");
            
            button.name = "create_new_theme";

            buttonDiv.appendChild(button);

            toBlock.classList.add("disabled");

        }


        
        document.getElementById("newQuestion").onclick = () => {
            let newquestion = document.createElement("div"); 
            newquestion.classList.add("question");

            let rowQuest = document.createElement("div");
            rowQuest.classList.add("row");

            let colQuest = document.createElement("div");
            colQuest.classList.add("col");

            let questionInput = document.createElement("input");
            questionInput.type = "text";
            questionInput.placeholder = "Question text";
            questionInput.classList.add("form-control");
            questionInput.name = "question_" + questions;
            

            newquestion.appendChild(rowQuest);
            rowQuest.appendChild(colQuest);
            colQuest.appendChild(questionInput);


            let inputofanswers = document.getElementById("count_of_answers");
            let hidenbtn = document.createElement("input");
            hidenbtn.type = "hidden";
            hidenbtn.name = "all_answers_" + questions;
            hidenbtn.value = parseInt(inputofanswers.value);
            document.getElementById("question-creating").appendChild(hidenbtn);
            
            for(let i = 0; i<parseInt(inputofanswers.value);i++){
                let answer = document.createElement("div");
                answer.classList.add("answer");
                answer.classList.add("mt-3");

                let answerRow = document.createElement("div");
                answerRow.classList.add("row");
                answer.appendChild(answerRow);


                let answer8 = document.createElement("div");
                answer8.classList.add("col-md-8");
                answerRow.appendChild(answer8);

                let input8  =document.createElement("input");
                input8.type="text";
                input8.placeholder="Answer text";
                input8.classList.add("form-control");
                input8.name = "answer_text_" + questions + "_" + i;

                answer8.appendChild(input8);


                let answer4 = document.createElement("div");
                answer4.classList.add("col-md-4");
                answerRow.appendChild(answer8);

                let input4  =document.createElement("input");
                input4.type="checkbox";
                input4.classList.add("form-check-input");
                input4.name = "answer_checkbox_" + questions + "_" + i;

                answer4.appendChild(input4);

                answerRow.appendChild(answer4);

                newquestion.appendChild(answer);

            }
            questions++;
            document.getElementById("create_test_btn").value = questions;
            document.getElementById("question-creating").appendChild(newquestion);
            inputofanswers.value = 4;
        }

    </script>


<style>
    body {

        background: url("https://look.com.ua/pic/201602/1920x1080/look.com.ua-150005.jpg");
        background-size: cover;
        background-position: top;
        background-repeat: no-repeat;
        background-attachment: fixed;
        }

    body:after {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,.5);
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
    .creating{
        color:black;
    }

    .question{
        margin-bottom: 100px;
    }

</style>

<!-- MDB -->
<script
  type="text/javascript"
  src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.3.0/mdb.min.js"
></script>
</body>
</html>