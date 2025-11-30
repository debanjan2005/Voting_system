 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
  <style>
    .fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    input:focus {
      outline: none;
      box-shadow: 0 0 8px rgba(37, 99, 235, 0.5);
      border-color: #2563eb;
    }
    label.error {
      color: red;
      font-size: 0.85rem;
      margin-top: 4px;
      display: block;
    }
  </style>
</head>
<body class="bg-blue-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-6 rounded-lg shadow-lg w-[90%] max-w-md fade-in">
    <h1 class="text-2xl font-bold text-center text-blue-700 mb-4">Admin Login</h1>

    <form id="loginForm" class="space-y-4" action="admin_login_check.php" method="post">
      <!-- Email -->
      <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" class="w-full border rounded p-2" placeholder="Enter your email">
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" class="w-full border rounded p-2" placeholder="Enter your password">
      </div>

      <!-- Submit -->
      <div class="text-center">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition transform hover:scale-105" name="login_check">
          Login
        </button>
      </div>

      <!-- Link -->
      <!-- <p class="text-center text-sm mt-4">
        Don't have an account? 
        <a href="registration.php" class="text-blue-600 hover:underline">Register here</a>
      </p> -->
    </form>
  </div>

  <script>
    $(document).ready(function() {
      $("#loginForm").validate({
        rules: {
          email: {
            required: true,
            email: true
          },
          password: {
            required: true,
            minlength: 6
          }
        },
        messages: {
          email: {
            required: "Please enter your email.",
            email: "Please enter a valid email."
          },
          password: {
            required: "Please enter your password.",
            minlength: "Password must be at least 6 characters."
          }
        },
        // submitHandler: function(form) {
        //   alert("Login successful!");
        //   form.reset();
        // }
      });
    });
  </script>

</body>
</html>