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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
  <style>
    /* Animation for form */
    .fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    /* Input focus animation */
    input:focus {
      outline: none;
      box-shadow: 0 0 8px rgba(37, 99, 235, 0.5);
      border-color: #2563eb;
    }
    /* Validation error styling */
    label.error {
      color: red;
      font-size: 0.85rem;
      margin-top: 4px;
      display: block;
    }
  </style>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center overflow-x-hidden">

  <div class="bg-white p-6 rounded-lg shadow-lg w-[80%] max-w-5xl fade-in">
    <h1 class="text-2xl font-bold text-center text-blue-700 mb-4">Registration Form</h1>

    <form id="registrationForm" class="grid grid-cols-2 gap-4" action="#" method="post">
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium">Name</label>
        <input type="text" name="name" class="w-full border rounded p-2" placeholder="Enter your name" value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" class="w-full border rounded p-2" placeholder="Enter your email"value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" >
      </div>

      <!-- DOB -->
      <div>
        <label class="block text-sm font-medium">Date of Birth</label>
        <input type="date" name="dob" class="w-full border rounded p-2" value="<?php echo isset($_SESSION['form_data']['dob']) ? htmlspecialchars($_SESSION['form_data']['dob']) : ''; ?>">
      </div>

      <!-- Gender -->
      <div>
        <label class="block text-sm font-medium">Gender</label>
        <div class="flex items-center gap-4 p-2 border rounded">
          <label><input type="radio" name="gender" value="male" class="mr-1" <?php if(isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'male') echo 'checked'; ?>>Male</label>
          <label><input type="radio" name="gender" value="female" class="mr-1" <?php if(isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'female') echo 'checked'; ?>>Female</label>
        </div>
      </div>

      <!-- Phone -->
      <div>
        <label class="block text-sm font-medium">Phone Number</label>
        <input type="tel" name="phone" class="w-full border rounded p-2" placeholder="Enter phone number" value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>">
      </div>

      <!-- Address -->
      <div class="col-span-2 grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium">Village</label>
          <input type="text" name="village" class="w-full border rounded p-2" placeholder="Village" value="<?php echo isset($_SESSION['form_data']['village']) ? htmlspecialchars($_SESSION['form_data']['village']) : ''; ?>">
        </div>
        <div>
          <label class="block text-sm font-medium">Post Office</label>
          <input type="text" name="po" class="w-full border rounded p-2" placeholder="PO" value="<?php echo isset($_SESSION['form_data']['po']) ? htmlspecialchars($_SESSION['form_data']['po']) : ''; ?>">
        </div>
        <div>
          <label class="block text-sm font-medium">Police Station</label>
          <input type="text" name="ps" class="w-full border rounded p-2" placeholder="PS" value="<?php echo isset($_SESSION['form_data']['ps']) ? htmlspecialchars($_SESSION['form_data']['ps']) : ''; ?>">
        </div>
        <div>
          <label class="block text-sm font-medium">District</label>
          <input type="text" name="district" class="w-full border rounded p-2" placeholder="District" value="<?php echo isset($_SESSION['form_data']['district']) ? htmlspecialchars($_SESSION['form_data']['district']) : ''; ?>">
        </div>
        <div>
          <label class="block text-sm font-medium">State</label>
          <input type="text" name="state" class="w-full border rounded p-2" placeholder="State" value="<?php echo isset($_SESSION['form_data']['state']) ? htmlspecialchars($_SESSION['form_data']['state']) : ''; ?>">
        </div>
        <div>
          <label class="block text-sm font-medium">Pincode</label>
          <input type="number" name="pincode" class="w-full border rounded p-2" placeholder="Pincode" value="<?php echo isset($_SESSION['form_data']['pincode']) ? htmlspecialchars($_SESSION['form_data']['pincode']) : ''; ?>">
        </div>
      </div>

      <!-- Password -->
      <div class="col-span-2">
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" class="w-full border rounded p-2" placeholder="Enter password" value="<?php echo isset($_SESSION['form_data']['password']) ? htmlspecialchars($_SESSION['form_data']['password']) : ''; ?>">
      </div>

      <!-- Submit -->
      <div class="col-span-2 text-center">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition transform hover:scale-105" name="add_data">
          Register
        </button>
      </div>
    </form>
  </div>

  <script>
    $(document).ready(function() {
      $("#registrationForm").validate({
        rules: {
          name: { required: true, minlength: 3 },
          email: { required: true, email: true },
          dob: { required: true },
          gender: { required: true },
          phone: { required: true, digits: true, minlength: 10, maxlength: 10 },
          village: { required: true },
          po: { required: true },
          ps: { required: true },
          district: { required: true },
          state: { required: true },
          pincode: { required: true, digits: true, minlength: 6, maxlength: 6 },
          password: { required: true, minlength: 6 }
        },
        messages: {
          name: "Please enter your name (min 3 letters)",
          email: "Enter a valid email",
          dob: "Select your date of birth",
          gender: "Select your gender",
          phone: "Enter a valid 10-digit phone number",
          village: "Enter your village",
          po: "Enter your post office",
          ps: "Enter your police station",
          district: "Enter your district",
          state: "Enter your state",
          pincode: "Enter a valid 6-digit pincode",
          password: "Password must be at least 6 characters"
        },
        errorPlacement: function(error, element) {
          if (element.attr("name") == "gender") {
            error.insertAfter(element.closest('div'));
          } else {
            error.insertAfter(element);
          }
        },
      //   submitHandler: function(form) {
      //   alert("Registration successful!");
      //   form.submit();
      // }

      });
    });
  </script>

</body>
</html>