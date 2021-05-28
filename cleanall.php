<?php

function CleanAll($json_link, $link_to_del){
    $all_elems = json_decode(file_get_contents($json_link));
    
    for($i=0;$i<count($all_elems); $i++){
        file_get_contents($link_to_del . $all_elems[$i]->id);
    }
}

if(isset($_POST['clean_all'])){
    CleanAll('http://18.197.83.92:84/api/Authorization/User', 'http://18.197.83.92:84/api/Authorization/User/');
    //CleanAll('', '');
    header("Refresh:0");
}

?>

<form method="post">
    <input type="submit" value="o4istit" name="clean_all">
</form>