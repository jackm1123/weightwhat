<!doctype html>
<html lang="en">
  <head>
    <!-- Jack McManus jmm4ye Kelsie Reinaltt klr5fk -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
 	<link rel="stylesheet" href="login.css">
    <title>Weight, What?</title>
  </head>


  <script>
    /* Focus on username to start with */
    function setFocus(){
      document.getElementById('username').focus()
    }

    /* Forgot password alert */
    forgotPass = () => alert("Check your email!")

    /* validate form entered correctly */
    function validate() {
      var username = document.getElementById('username').value
      var pass = document.getElementById('password').value
      if (username === ''){
        document.getElementById('username').focus()
        document.getElementById('username-note').innerHTML = "Please enter username"
        if (pass === ''){
          document.getElementById('password-note').innerHTML = "Please enter password"
        }
        else{
          document.getElementById('password-note').innerHTML = ""
        }
        return false
      }
      if (pass === ''){
        document.getElementById('password').focus()
        document.getElementById('password-note').innerHTML = "Please enter password"
        if (username === ''){
          document.getElementById('username-note').innerHTML = "Please enter username"
        }
        else{
          document.getElementById('username-note').innerHTML = ""
        }
        return false
      }
      //window.location = "dashboard.php"
      else{return true }  
    }
  </script>

  <body class="container" onload="setFocus()">
    <div class="jumbotron">
      <div>
        <h1>WELCOME</h1>

        <!-- Form section with username and password -->

    		<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return validate()">
    			<div class="form-group center-div">
  			    <input class="form-control form-control-lg" type="text" placeholder="username" id="username" name="username">
            <span style="color: maroon; font-style: oblique;" id="username-note"></span>
    			</div>
    			<br>
    			<div class="form-group center-div">
  			    <input type="password" class="form-control form-control-lg" id="password" placeholder="password" name="password">
            <span style="color: maroon; font-style: oblique;" id="password-note"></span>
    			</div>
    			<br>
    			<div class="form-group center-div">
    				<input type="submit" class="btn btn-block btn-primary custom-btn" id="login" value="Login">
      		</div>
    		</form>
      </div>
    </div>

    <!-- Forgot Password Event Listener -->
    <p>
    	<a class="nav-link link" id="forgot" href='#' >Forgot password?</a>
      <script>
        document.getElementById("forgot").addEventListener('click', forgotPass)
      </script>
    </p>

    <p>
      <a class="nav-link link" href='http://localhost:4200' >Make Account</a>
    </p>


  </body>
</html>

<?php
session_start();


//db access credentials

$dbusername = 'cs4640';
$dbpassword = 'cs4640';
$hostname = 'localhost:3306';
$dbname = 'cs4640';
$dsn = "mysql:host=$hostname;dbname=$dbname";

//check if cookie remembers user and password. if so, bypass login screen and makes sure sessions are set
if (isset($_COOKIE['user']) && isset($_COOKIE['password'])){
  if (!isset($_SESSION['user'])){
    $_SESSION['user'] = $_COOKIE['user'];
  }
  if (!isset($_SESSION['password'])){
    $_SESSION['password'] = $_COOKIE['password'];
  }
  if (!isset($_SESSION['dsn'])){
    $_SESSION['dsn'] = $dsn;
  }
  header("Location: dashboard.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  if (isset($_POST['username']) && isset($_POST['password']))

  {
      $username = $_POST['username'];
      $password = $_POST['password'];
      $hashed_pass = md5($password);

      try 
      {
         $db = new PDO($dsn, $dbusername, $dbpassword);

          //by this point user and password together are not set as cookies
          //clear cookies if there happens to be one left
         
          if (count($_COOKIE) > 0){
            foreach ($_COOKIE as $key => $value){
                setcookie($key, '', time() - 120);    
            }
          }


          $query = "SELECT `password` FROM `users` WHERE `username` = '$username'";
          $statement = $db->prepare($query); 
          $statement->execute();
          $results = $statement->fetchAll();
          $statement->closeCursor();
          if (empty($results)){
            echo '<p align="center">No user found, try again!!!!</p>';
          }
          else{
            $fetched_password = $results[0]['password'];
            if ($hashed_pass === $fetched_password){

              //set the cookies to username and password for 20 min
              setcookie('user', $username, time()+1200);
              setcookie('password', $hashed_pass, time()+1200);

              //set session if it needs to be set
              if (!isset($_SESSION['user'])){
                $_SESSION['user'] = $username;
              }
              if (!isset($_SESSION['password'])){
                $_SESSION['password'] = $hashed_pass;
              }
              if (!isset($_SESSION['dsn'])){
                $_SESSION['dsn'] = $dsn;
              }
              header("Location: dashboard.php");
            }
            else{
              echo '<p align="center">Incorrect password, try again!!!!</p>';
            }
         }

      }
      catch (PDOException $e)
      {
         $error_message = $e->getMessage();        
         echo '<p align="center">Incorrect db username or password, try again!!!!</p>';
      }
      catch (Exception $e)
      {
         $error_message = $e->getMessage();
         echo '<p align="center">Incorrect db username or password, try again!!!!</p>';
      }

  }
}
?>


