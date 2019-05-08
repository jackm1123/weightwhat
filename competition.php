<!DOCTYPE html>
<html lang="en">
<!-- Sources Used:
Chart.JS https://www.chartjs.org/ 
-->

<head>
  <!-- Jack McManus jmm4ye Kelsie Reinaltt klr5fk -->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>View Group</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Import Bootstrap CDN, ChartJS -->

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>

  <link rel="stylesheet" href="competition.css">
</head>
<?php
  session_start();
  if (count($_SESSION) === 0){
    header("Location: login.php");
  }

  //logout function waits for logout button to send logout GET request, then deletes all sessions, cookies,
  //and redirects to login

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
?>


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
          <a class="nav-link" style="color: black" href='dashboard.php?logout=true'></i>Logout</a>
        </li>
      </ul>
    </div>  
  </nav>

  <!-- Graph Container -->
  <div class="container" style="text-align:center";>
    <br />
    <h2 class="sectiontext"><?php echo $_SESSION['user'] ?>'s Group</h2> <!-- Replace with user's group -->
    <br/>
    <canvas id="myChart" width="850" height="400"></canvas>
  </div>


  <?php

    //get all data from database and make two arrays with group members and their data separate

    $db = new PDO($_SESSION['dsn'], 'cs4640', 'cs4640');
    $un = $_SESSION['user'];
    $query = "SELECT groupo.user2 FROM groupo WHERE groupo.user1='$un'";
    //returns an array for every row in the table, aka every user in group
    $statement = $db->prepare($query); 
    $statement->execute();
    $results = $statement->fetchAll();
    $statement->closeCursor();
    $group_members = array();
    foreach ($results as $value){
      $group_members[] = $value['user2'];
    }
    $group_members[] = $un;             //array with all user's names
    $group_members_data = array();      //array with all point data
    foreach ($group_members as $value){
      $query = "SELECT weight, day FROM history WHERE username='$value'";
      $statement = $db->prepare($query); 
      $statement->execute();
      $results = $statement->fetchAll();
      $weights = array();
      $dates = array();
      foreach ($results as $point){
        $weights[] = $point['weight'];
        $dates[] = $point['day'];
      }
      $single_member_data = array();
      $single_member_data[] = $weights;
      $single_member_data[] = $dates;
      $group_members_data[] = $single_member_data;
      $statement->closeCursor();
    }
  ?>


  <script>
    /* Data to Graph */

    //use the PHP arrays with data
    var group_members = <?php echo json_encode($group_members); ?>;
    var group_members_data = <?php echo json_encode($group_members_data); ?>;


    //format for graph.js
    dataset = []
    for (i = 0; i < group_members.length; i++){

      //make data array then put it in obj
      point_data = []
      for (j = 0; j < (group_members_data[i][0]).length; j++){
        temp = {
          x: group_members_data[i][1][j],
          y: Number(group_members_data[i][0][j])
        }
        point_data.push(temp)
      }

      obj = {
        label: group_members[i],
        borderColor: "#"+((1<<24)*Math.random()|0).toString(16),
        fill: false,
        data: point_data
      }
      dataset.push(obj)
    }


    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
      type: 'line',
      data: { datasets: dataset },
      options: {
        scales: {
          xAxes: [{
            type: 'time'
          }]
        }
      }
    });
  </script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

</body>