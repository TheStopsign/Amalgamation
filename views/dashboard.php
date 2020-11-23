<?php



?>
<!doctype html>

<html lang="en">

<head>
  <title>Dashboard</title>
  <link href="../resources/style_dashboard.css" rel="stylesheet" />
  <link href="../resources/style.css" rel="stylesheet" />
  <meta charset="UTF-8"> 
  
  <?php 
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "amalgamation";

	// Create connection
	global $conn;
	$conn = mysqli_connect($servername, $username, $password);
	// Check connection
	if (mysqli_connect_errno()) {
	 die("Connection failed " . $conn->connect_error);
	}
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
	  $o1 = $_POST['op1'];
	  $o2 = $_POST['op2'];
	}
	
	$user = 1; //hard coded for now needs integration with login
	$userName = "SELECT * FROM amalgamation.users WHERE users.UserID = $user";
	$userNameResults = mysqli_query($conn, $userName);

	$projects = "SELECT * FROM amalgamation.projects WHERE projects.UserID = $user";
	$projectResults = mysqli_query($conn, $projects);
	
	function addProject($name, $desc) {
		$newProj = "INSERT INTO amalgamation.projects (name, UserID, Description) VALUES (". $name . ", ". $user . ", ". $desc . ")";
		$success = mysqli_query($conn, $newProj);
	}
	
	if(isset($_POST['submit'])){
		$op = addProject($_POST['name'], $_POST['desc']);
	}
  ?>
</head>

<body>
  <header>
    <h1 id="headerTitle">amalgamation.</h1>
    <img class="editing-icon" src="../resources/images/icons8-user.png" width="50" height="50"/>
  </header>
  <section class="colorbar"></section>
  <br/>

  <div class="main-body">
    <?php
		$myName = $userNameResults->fetch_assoc();
		echo "<h1> Hello, ". $myName["name"] ."</h1>";
	?>
	
    <div class="custom-select" style="width:200px;">
      <label for="sortby">Sort By</label>
      <select name="Sort By" id="sortby">
        <option value="name_up">Name ↑</option>
        <option value="name_down">Name ↓</option>
        <option value="edited_up">Last Edited ↑</option>
        <option value="edited_down">Last Edited ↓</option>
      </select>
    </div>
    <br>

    <?php

        while($row = $projectResults->fetch_assoc()) {
          echo "
			<div class=\"display-window\">
			  <h3 class=\"centered\">". $row["name"] ."</h3>
			  <ul>
				<li>
				  ". $row["Description"] ."
				</li>
			  </ul>

			  <button class=\"projectButton\" onclick= \"document.getElementById('myModal').style.display='block'\" type=\"button\">Share Settings</button>
			  <button class=\"projectButton\" type=\"button\">Download</button>
			  <button class=\"projectButton\"  onclick=\"location.href='doodling.html?id=". $row["ProjectID"] ."'\"  type=\"button\">Edit</button>
			</div>";
        }
        
        
      ?>
     
    <div class="display-window new""> 
      <form action="/views/dashboard.php">
		  <label for="title">Name: </label>
		  <input type="text" id="name" name="name"><br>
		  <label for="title">Description: </label>
		  <input type="text" id="desc" name="desc"><br>
		  <input type="submit" value="Submit">
		</form> 
	  <h3  class="centered">+</h3>
    </div>
  </div>

  <div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
      <button onclick = "document.getElementById('myModal').style.display='none'" type="button" class = "close">X</button>
      <p>Shared User 1</p>
      <p>Shared User 2</p>
      <p>Shared User 3</p>
      <button onclick = "" type="button" class = "addUser">Add User</button>
      <button onclick = "" type="button" class = "removeUser">Remove User</button>
    </div>

  </div>

  <footer>         
    <h2 id="teamTux">&copy; Team Tux</h2>
    <a href="https://github.com/TheStopsign/Tux" target="_blank"><img alt="Github Octocat" src="../resources/images/Octocat.png"/></a>
  </footer>

</body>

</html>