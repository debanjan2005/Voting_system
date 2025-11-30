<?php
session_start();
include("db_conn.php");
if (isset($_POST['add_data'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $village = $_POST['village'];
    $po = $_POST['po'];
    $ps = $_POST['ps'];
    $district = $_POST['district'];
    $state = $_POST['state'];
    $pin = $_POST['pincode'];
    $pass = $_POST['password'];

    $_SESSION['form_data'] = $_POST;      //for store the data in session
    $check = "SELECT * from users where email = '$email'";
    $check_user = mysqli_query($conn,$check);
    $row_count = mysqli_num_rows($check_user);
    if($row_count == 1){
        echo "<script>alert('Email already exists'); window.location.href = 'registration.php'</script>";
        exit;

        // header('location:registration.php');
    }
    else{
        $sql = "INSERT INTO users(name,email,dob,gender,phone,village,post_office,police_station,districe,state,pin_code,password) VALUES ('$name','$email','$dob','$gender','$phone','$village','$po','$ps','$district','$state','$pin','$pass')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
          unset($_SESSION['form_data']); // Clear stored form data
            echo "<script>alert('Registration Successful'); window.location.href = 'registration.php';</script>";
            exit;
            
        }
    }
    

    

}

?>
