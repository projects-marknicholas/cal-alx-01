<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - CAP ALX 01</title>
  <link rel="stylesheet" href="css/log-styles.css">
</head>
<body>
<div class="wrapper">
    <div class="title-text">
      <div class="title login">Login Form</div>
      <div class="title signup">Signup Form</div>
    </div>

    <!-- Start Form Container -->
    <div class="form-container">
      <div class="slide-controls">
        <input type="radio" name="slider" id="login" checked>
        <input type="radio" name="slider" id="signup">
        <label for="login" class="slide login">Login</label>
        <label for="signup" class="slide signup"> Signup</label>
        <div class="slide-tab"></div>
      </div>

      <!-- Error Message Display -->
      <div id="error-message" class="error-message">hello</div>

      <div class="form-inner">

        <!-- Start Login Form -->
        <form id="login-form" class="login">
          <div class="field">
            <input id="login-email" type="text" name="email" placeholder="Email Address" required>
          </div>
          <div class="field">
            <input id="login-password" type="password" name="password" placeholder="Password" required>
          </div>
          <div class="pass-link">
            <!-- <a href="#">Forgot password?</a> -->
          </div>
          <div class="field">
            <input type="submit" value="Login">
          </div>
          <div class="signup-link">
            Not a member? <a href="#">Signup now</a>
          </div>
        </form>

        <!-- Start Signup Form -->
        <form id="signup-form" class="signup">
          <div class="field">
            <input id="signup-first-name" type="text" name="first_name" placeholder="First Name" required>
          </div>
          <div class="field">
            <input id="signup-last-name" type="text" name="last_name" placeholder="Last Name" required>
          </div>
          <div class="field">
            <input id="signup-email" type="text" name="email" placeholder="Email Address" required>
          </div>
          <div class="field">
            <input id="signup-password" type="password" name="password" placeholder="Password" required>
          </div>
          <div class="field">
            <input id="signup-confirm-password" type="password" name="confirm_password" placeholder="Confirm password" required>
          </div>

          <div class="field">
            <input type="submit" value="Signup">
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="js/log-effects.js"></script>
  <script src="js/log-ajax.js"></script>
</body>
</html>