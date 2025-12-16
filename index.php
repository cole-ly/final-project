<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Reservation Tracker</title>
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
  }

  #front-title {
    font-weight: 500 ;
    font-size: 4rem;
    color: #448aff;
    margin-bottom: 1rem;
  }

  #front-subtitle {
    font-weight: 400;
    font-size: 1.25rem;
    color: #a0aec0;
    margin-bottom: 3rem;
  }

  .buttons {
    display: flex;
    gap: 2rem;
  }

  .front-link {
    background-color: #2a3a5a;
    border: none;
    color: #e1e5eb;
    font-weight: 500;
    font-size: 1rem;
    padding: 0.75em 2.25em;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border-radius: 4px;
  }

  .front-link:hover {
    background-color: #82b1ff;
    color: #121a26;
  }
</style>
</head>
<body>
  <h1 id="front-title">Reservation Tracker</h1>
  <p id="front-subtitle">Manage and Track Reservations</p>

  <div class="buttons">
    <a href="login.php" class="front-link">Login</a>
    <a href="register.php" class="front-link">Register</a>
  </div>
</body>
</html>
