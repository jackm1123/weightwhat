<!DOCTYPE html>
<html lang="en">
<!-- 
Sources Used:
https://jqueryui.com/datepicker/
w3Schools
 -->

<head>
  <!-- Jack McManus jmm4ye Kelsie Reinaltt klr5fk -->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Import Bootstrap CDN, jQuery DatePicker, Font Awesome, CSS, and Icons -->

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <link rel="stylesheet" href="dashboardstyle.css">

  <link href="jquery-ui.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>

</head>

<?php
  session_start();

  //so people can't access the dashboard or any page without being logged in first
  if (count($_SESSION) === 0){
    header("Location: login.php");
  }

  //logout function

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

  //logging weight from middle form function
  function log_weight($weight, $date){
    $db = new PDO($_SESSION['dsn'], 'cs4640', 'cs4640');
    if (!empty($weight) && !empty($date)){
      $un = $_SESSION['user'];
      $newDate = date("Y-m-d", strtotime($date));
      $query = "INSERT INTO history (username, weight, day) VALUES('$un', $weight, '$newDate')";
      $statement = $db->prepare($query); 
      $statement->execute();
      $statement->closeCursor();
      $query = "UPDATE users SET currentweight = $weight WHERE username = '$un'";
      $statement = $db->prepare($query); 
      $statement->execute();
      $statement->closeCursor();
    }
  }

  if (isset($_POST['current_weight']) && isset($_POST['date'])) {
    log_weight($_POST['current_weight'], $_POST['date']);
  }
?>

<script>
var pcent = <?php
  //calculating what percent to goal
  //including cases for desire to gain weight, to lose weight, and edge cases of negative progress or ovr 100%

  $db = new PDO($_SESSION['dsn'], 'cs4640', 'cs4640');
  $un = $_SESSION['user'];
  $query = "SELECT history.weight, history.day FROM history WHERE history.username='$un'";
  $statement = $db->prepare($query); 
  $statement->execute();
  $results = $statement->fetchAll();
  $statement->closeCursor();
  if (!empty($results)){
    $original_weight = $results[0]['weight'];
  }
  else{
    $original_weight = 0;
  }
  $query = "SELECT currentweight, targetweight FROM `users` WHERE `username` = '$un'";
  $statement = $db->prepare($query); 
  $statement->execute();
  $results = $statement->fetchAll();
  if (!empty($results[0]['currentweight'])){
    $currentweight = $results[0]['currentweight'];
  }
  else{
    $currentweight = 0;
  }
  if (!empty($results[0]['targetweight'])){
    $targetweight = $results[0]['targetweight'];
  }
  else{
    $targetweight = 0;
  }
  $statement->closeCursor();
  $diff = abs($targetweight - $original_weight);
  if ($original_weight > $targetweight){
    $progress = $original_weight - $currentweight;
    if ($progress <= 0){
      $percent = 0;
    }
    else{
      $percent = $progress / $diff;
    }
    if ($original_weight === 0){
      $percent = 0;
    }
    echo json_encode($percent);
  }
  else{
    $progress = $currentweight - $original_weight;
    if ($progress <= 0){
      $percent = 0;
    }
    else{
      $percent = $progress / $diff;
    }
    if ($original_weight === 0){
      $percent = 0;
    }
    echo json_encode($percent);
  }
?>
</script>






<body>

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

  <!-- Dashboard Panels -->
 
  <div class="row content-row text-center" >
    <!-- Left Panel -->
    <div class="col-md-4">
      <div class="graph">
        <h3> Progress to Target Weight</h3>
        <div id="Progress" onmouseenter="displayPercent()" onmouseleave="hidePercent()">
          <div id="Bar"></div>
        </div>
        <h4 id="percent"></h4>
      </div>
    </div>
    <br>
    <!-- Middle Panel -->
    <div class="col-md-4">
      <div class="graph">
        <h3>Enter Log</h3>
        <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
          <div class="form-group">
            <i class="fa fa-weight"></i>
            <div style="padding-left: 30px">
              <input class="form-control input-wrapper" type="text" placeholder="current weight" id="weight" name="current_weight">
            </div>
            <br>
            <i class="fa fa-dumbbell"></i>
            <div style="padding-left: 30px">
              <input type="text" class="form-control" id="activities" placeholder="any activities done">
            </div>
            <br>
            <i class="fa fa-calendar-alt"></i>
            <div style="padding-left: 30px">
              <input type="text" class="form-control" id="datepicker" placeholder="date" name="date"/>
            </div>
            <script>
                $( "#datepicker" ).datepicker();
            </script>
            <br>
            <div class="text-center">
              <button type="submit" class="btn btn-default btn-lg btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <br>
    <!-- Right Panel -->
    <div class="col-md-4">
      <div class="graph">
        <h3>Goals</h3>
        <?php
          $db = new PDO($_SESSION['dsn'], 'cs4640', 'cs4640');
          $un = $_SESSION['user'];
          $query = "SELECT goals.goal FROM goals WHERE goals.username = '$un'";
          $statement = $db->prepare($query); 
          $statement->execute();
          $results = $statement->fetchAll();
          $statement->closeCursor();
          $count = 0;
          foreach ($results as $value){
            //cap the displayed goals at more than 6
            if ($count > 5){
              break;
            }
            $string = "<p style='font-style: oblique;'>" . $value['goal'] . "</p>";
            echo $string;
            $count++;
          }
        ?>
        <span></span>
      </div>
    </div>
    <br>
  </div>

  


<!-- Bottom Info Section -->
<!--  
  <div class="o">
    <div class="row">
      <div class="col-md-6" id="trending-left">
        <div class="sub-title">Top Gainers/Loserz</div>
        <div class="stock">
          <div class="stock-logo"><i class="fa-bolt fa fa-inverse"></i></div>
          <div class="stock-info">
            <div class="stock-name">Person A</div>
            <div class="stock-fullname">Affiliation</div>
          </div>
          <div class="stock-value" align="right">+/- XX.X%</div>
        </div>
        <div class="stock">
          <div class="stock-logo"><i class="fa-bolt fa fa-inverse"></i></div>
          <div class="stock-info">
            <div class="stock-name">Person B</div>
            <div class="stock-fullname">Facebook, Inc.</div>
          </div>
          <div class="stock-value" align="right">+/- XX.X%</div>
        </div>
        <div class="stock">
          <div class="stock-logo"><i class="fa-bolt fa fa-inverse"></i></div>
          <div class="stock-info">
            <div class="stock-name">Person C</div>
            <div class="stock-fullname">Amazon.com, Inc.</div>
          </div>
          <div class="stock-value" align="right">+/- XX.X%</div>
        </div>
      </div>
      
    
      
      
      <div class="col-md-6" id="trending-right">
          <div class="sub-title">Popular This Week</div>
          <div class="stock">
              <div class="stock-logo"><i class="fa-fire-alt fa fa-inverse"></i></div>
              <div class="stock-info">
                  <div class="stock-name">Put</div>
                  <div class="stock-fullname">Twitter Inc.</div>
              </div>
              <div class="stock-value fa-angle-double-up fa icon-ok-sign" align="center"></div>
          </div>
          <div class="stock">
              <div class="stock-logo"><i class="fa-fire-alt fa fa-inverse"></i></div>
              <div class="stock-info">
                  <div class="stock-name">Put</div>
                  <div class="stock-fullname">Twitter Inc.</div>
              </div>
              <div class="stock-value fa-angle-double-down fa" align="center"></div>
          </div>
          <div class="stock">
              <div class="stock-logo"><i class="fa-fire-alt fa fa-inverse"></i></div>
              <div class="stock-info">
                  <div class="stock-name">Put</div>
                  <div class="stock-fullname">Twitter Inc.</div>
              </div>
              <div class="stock-value fa-angle-double-up fa" align="center"></div>
          </div>
      </div>
    </div>
  </div>
  -->



  <!-- JavaScript -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

  <script>
    /* Self Invoking Function To Run on Page Load, Animates Bar*/

    (function (){
      var progress = document.getElementById("Bar")
      var width = 0 //keep track of how full to fill bar
      var timerId = setInterval(animation, 10) //repeat every 10ms
      function animation(){
        if (width >= Math.floor(JSON.parse(pcent) * 100) || width >= 100){
          clearInterval(timerId)
        }
        else{
          width++
          progress.style.width = width + '%' //update css width
        }
      }
    })();

    // Mouse over function
    function displayPercent(){
      document.getElementById('percent').innerHTML = Math.floor(JSON.parse(pcent) * 100) + "% there!"
    }
    function hidePercent(){
      document.getElementById('percent').innerHTML = ""
    }
  </script>

</body>

</html>
