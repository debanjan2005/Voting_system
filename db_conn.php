 <?php
    $server = "localhost"; 
    $user = "root";
    $password = "";
    $db = "voting_system";
    $conn = mysqli_connect($server,$user,$password,$db);
    if(!$conn){
        echo "Connection error";
    }

 ?>