<!doctype html>
<html lang="en">
  <head>
    <!-- Jack McManus jmm4ye Kelsie Reinaltt klr5fk -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Goal</title>
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


  function addGoal($target, $goal){
    $db = new PDO($_SESSION['dsn'], 'cs4640', 'cs4640');
    if (!empty($goal)){
      $un = $_SESSION['user'];
      $query = "INSERT INTO goals (username, goal) VALUES ('$un', '$goal')";
      $statement = $db->prepare($query); 
      $statement->execute();
      $statement->closeCursor();
    }
    if (!empty($target)){
      $un = $_SESSION['user'];
      $query = "UPDATE users SET targetweight = $target WHERE username = '$un'";
      $statement = $db->prepare($query); 
      $statement->execute();
      $statement->closeCursor();
    }
    header("Location: dashboard.php");
  }

  if (isset($_POST['goal']) || isset($_POST['weight_target'])) {
    addGoal($_POST['weight_target'], $_POST['goal']);
  }
  
?>


  <script>
    /* Focus on username to start with */
    function setFocus(){
      document.getElementById('goal').focus()
    }

    /* validate form entered correctly */
    function validate() {
      var goal = document.getElementById('goal').value
      var weight = document.getElementById('weight_target').value
      if (goal === '' && weight === ''){
        document.getElementById('goal').focus()
        document.getElementById('goal-note').innerHTML = "Please enter a goal"
      return false
      }
      if (isNaN(weight)){
        document.getElementById('weight_target').focus()
        document.getElementById('goal-note').innerHTML = "Please enter a valid weight (integer)"
      return false
      }

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
            <a class="dropdown-item" href="join_group.php">Join Group</a>
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



  <!-- Add Goal Form -->

  <div class="jumbotron" style="background-color: transparent">
    <div>
      <h2>Add Goal</h2>

  		<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return validate()">
  			<div class="form-group center-div">
  			    <input class="form-control form-control-lg" type="text" placeholder="goal" id="goal" name="goal">
            <span style="color: maroon; font-style: oblique;" id="goal-note"></span>
            <br>
            <input class="form-control form-control-lg" type="text" placeholder="new weight target (lbs)" id="weight_target" name="weight_target">
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

