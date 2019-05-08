<!doctype html>
<html lang="en">
  <head>
    <!-- Jack McManus jmm4ye Kelsie Reinaltt klr5fk -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join Group</title>
    <link rel="stylesheet" href="goal_form.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
  </head>
<?php
  session_start();
  if (count($_SESSION) === 0){
    header("Location: login.php");
  }
  function logout() {
    if (count($_SESSION) > 0){  
      foreach ($_SESSION as $key => $value){    
        unset($_SESSION[$key]);
      }      
      session_destroy();
    }
    if (count($_COOKIE) > 0){
      foreach ($_COOKIE as $key => $value){
          setcookie($key, '', time() - 3600);  
      }
    }
    header("Location: login.php");
  }
  if (isset($_GET['logout'])) {
    logout();
  }

function joinGroup($user2){
    $db = new PDO($_SESSION['dsn'], 'cs4640', 'cs4640');
    $user1 = $_SESSION['user'];
    //if user2 field not empty compare user2 with all usernames in db
    if(!empty($user2)){
        $query = "SELECT * FROM `users` WHERE `username` = '$user2'";
        $statement = $db->prepare($query);
        $statement->execute();
        $results = $statement->fetchAll();
        //if the there is no match between user2 and db usernames, print user DNE
        if(empty($results)){
            echo '<p align="center">This user was not found, try again!!!!</p>';
        }
        else{
        //if user2 matches a username in db, put user1 and user2 in groupo table
        $query1 = "INSERT INTO groupo (user1,user2) VALUES ('$user1','$user2')";
        $statement1 = $db->prepare($query1);
        $statement1->execute();
        }
        $statement->closeCursor();
    }
    else{
        echo '<p align="center">Please enter a username.</p>';
    }
}
    
?>

<script>
	/* set initial focus and validate functions */
  function setFocus(){
    document.getElementById('goal').focus()
  }
  function validate() {
    var group = document.getElementById('submit').value

    if (group === ''){
      document.getElementById('submit').focus()
      document.getElementById('submit-note').innerHTML = "Please enter a valid group"
    return false
    
    else{return true}    
  }
</script>

<body onload="setFocus()">

  <!-- Navbar -->

  <nav class="navbar navbar-expand-md" style="background-color: white">
    <a class="navbar-brand" style="color: black">Weight, What?</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
      <span class="fa fa-bars"></span>
    </button>
    
    <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">   
      <ul class="navbar-nav">
        <li class="nav-item emphasize">
          <a class="nav-link" style="color: black" href="about.php">About</a>
        </li>               
        <li class="nav-item dropdown emphasize">
          <a class="nav-link dropdown-toggle" href="#" id="dropdown" data-toggle="dropdown" aria-haspopup="true" style="color: black" aria-expanded="false"><i class="fa fa-wrench" aria-hidden="true"></i> Settings</a>
          <div class="dropdown-menu" aria-labelledby="dropdown0">
            <a class="dropdown-item" href="dashboard.php">Dashboard</a>
            <a class="dropdown-item" href="#">Join Group</a>
            <a class="dropdown-item" href="competition.php">View Group</a>
            <a class="dropdown-item" href="goal_form.php">Add Goal</a>
         </div>
        </li>                          
        <li class="nav-item emphasize"> 
          <a class="nav-link" style="color: black" href='dashboard.php?logout=true'><i class="fa fa-sign-out-alt" aria-hidden="true"></i>Logout</a>
        </li>
      </ul>
    </div>  
  </nav>

	<!-- Form -->

  <div class="jumbotron" style="background-color: transparent">
    <div>
      <h2>Join Group</h2>

  		<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
  			<div class="form-group center-div">
  			    <input class="form-control form-control-lg" type="text" placeholder="type group member's name" id="submit" name="user2">
            <span style="color: maroon; font-style: oblique;" id="submit-note"></span>
  			</div>
  			<br>
  			<div class="form-group center-div">
    				<input type="submit" class="btn btn-block btn-primary custom-btn" id="submit" value="Submit">
    		</div>
  		</form>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

</body>
</html>

<!-- put at bottom to have error message at bottom -->
<?php
if (isset($_POST['user2'])) {
  joinGroup($_POST['user2']);
}
?>


