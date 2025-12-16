 <!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register</title>
<style>
  body {
    margin: 0;
    background-color: #121a26;
    background-image: radial-gradient(circle, #1e2538 1px, transparent 1px);
    background-size: 40px 40px;
    color: #e1e5eb;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    padding: 0 1rem;
  }

  .front-title {
    font-weight: 500;
    font-size: 4rem;
    color: #448aff;
    margin-bottom: 1rem;
  }

  .form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 320px;
  }

  .label {
    font-weight: 500;
    font-size: 1rem;
    color: #a0aec0;
  }

  .input {
    padding: 0.6em 0.8em;
    margin-bottom: 2rem;
    font-size: 1rem;
    border-radius: 4px;
    border: 1.5px solid #2a3a5a;
    background-color: #1e2538;
    color: #e1e5eb;
    outline: none;
  }

  .input:focus {
    border-color: #82b1ff;
  }

  .front-link {
    background-color: #2a3a5a;
    border: none;
    color: #e1e5eb;
    font-weight: 500;
    font-size: 1rem;
    padding: 0.75em 2.25em;
    cursor: pointer;
    border-radius: 4px;
    user-select: none;
  }

  .front-link:hover {
    background-color: #82b1ff;
    color: #121a26;
    outline: none;
  }

  .login-link {
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #a0aec0;
    text-align: center;
  }

  .login-link a {
    color: #82b1ff;
    text-decoration: none;
    margin-left: 0.25rem;
  }
  .success-message {
    background-color: #00b020;
    color: #fff;
    padding: 0.6em 1em;
    text-align: center;
  }
  .error-message {
    background-color: #b00020;
    color: #fff;
    padding: 0.6em 1em;
    text-align: center;
  }
</style>
</head>
<body>
  <?php 
  require "user.php";
  $user = new User();
  if($_SERVER["REQUEST_METHOD"] == "POST"){
      $username = $_POST["username"];
      $password = $_POST["password"];
      
      $message = $user->register($username, $password);
      if($message == "Registration Successfully"){
          echo '<div class="success-message">'.htmlspecialchars($message).'</div>';
      }
      else{
          echo '<div class="error-message">'.htmlspecialchars($message).'</div>';
      }
  }
  ?>

  <h1 class="front-title">Register</h1>

  <form class="form" method="POST" action="register.php">
    <label class="label" for="username">Username</label>
    <input class="input" type="text" placeholder="Username" name="username" id="username" required autocomplete="username" />

    <label class="label" for="password">Password</label>
    <input class="input" type="password" placeholder="Password" name="password" id="password" required autocomplete="new-password" />

    <button type="submit" class="front-link">Register</button>
  </form>

  <div class="login-link">
    Already have an account?
    <a href="login.php">Login</a>
  </div>
</body>
</html>
