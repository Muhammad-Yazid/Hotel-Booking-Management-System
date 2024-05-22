<?php
include('./header.php');
include('./db_connect.php');

session_start();
if(isset($_SESSION['login_id']))
  header("location:index.php?page=home");

$query = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach ($query as $key => $value) {
  if(!is_numeric($key))
    $_SESSION['setting_'.$key] = $value;
}
function check_login($conn, $username, $password) {
  $username = mysqli_real_escape_string($conn, $username);
  $password = mysqli_real_escape_string($conn, $password);

  $query = "SELECT * FROM users WHERE username='$username'";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if(password_verify($password, $user['password'])) {
      return $user['id']; 
    }
  }
  return false; 
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  
  if(!empty($username) && !empty($password)) {
    $user_id = check_login($conn, $username, $password);
    if ($user_id) {
      $_SESSION['login_id'] = $user_id;
      header("location:index.php?page=home");
      exit;
    } else {
      $error = "Username or password is incorrect.";
    }
  } else {
    $error = "Please enter username and password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Hotel Management System</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Custom CSS -->
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f1f1f1;
    }
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-form {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
    }
    .login-form h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .form-control {
      border-radius: 20px;
    }
    .btn-login {
      border-radius: 20px;
      margin-top: 20px;
    }
    .alert-danger {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-form">
      <h2>Hotel Management System</h2>
      <?php if(isset($error)) { ?>
        <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
      <?php } ?>
      <form id="login-form" method="POST">
        <div class="form-group">
          <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="form-group">
          <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-login">Login</button>
      </form>
    </div>
  </div>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#login-form').submit(function(e) {
        e.preventDefault();
        $('.btn-login').attr('disabled', true).html('Logging in...');
        if ($(this).find('.alert-danger').length > 0)
          $(this).find('.alert-danger').remove();

        var username = $('#username').val().trim();
        var password = $('#password').val().trim();

        if (username.length == 0 || password.length == 0) {
          $('#login-form').prepend('<div class="alert alert-danger">Please enter username and password.</div>');
          $('.btn-login').removeAttr('disabled').html('Login');
          return;
        }

        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: {
            username: username,
            password: password
          },
          error: function(err) {
            console.log(err);
            $('.btn-login').removeAttr('disabled').html('Login');
          },
          success: function(resp) {
            if (resp == 1) {
              location.href = 'index.php?page=home';
            } else if (resp == 2) {
              location.href = 'voting.php';
            } else {
              $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
              $('.btn-login').removeAttr('disabled').html('Login');
            }
          }
        });
      });
    });
  </script>
</body>
</html>
