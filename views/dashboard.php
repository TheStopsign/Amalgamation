<?php

?>
<!doctype html>

<html lang="en">

<head>
  <title>Dashboard - amalgamation</title>
  <link href="../resources/style_dashboard.css" rel="stylesheet" />
  <link href="../resources/style.css" rel="stylesheet" />
  <meta charset="UTF-8">
  <!-- Icon -->
  <link rel="apple-touch-icon" sizes="180x180" href="../resources/images/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../resources/images/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../resources/images/favicons/favicon-16x16.png">
  <link rel="manifest" href="../resources/images/favicons/site.webmanifest">
  <link rel="mask-icon" href="../resources/images/favicons/safari-pinned-tab.svg" color="#4c3549">
  <link rel="shortcut icon" href="../resources/images/favicons/favicon.ico">
  <meta name="msapplication-TileColor" content="#9f00a7">
  <meta name="msapplication-config" content="../resources/images/favicons/browserconfig.xml">
  <meta name="theme-color" content="#4c3549">

  <?php
	$servername = "localhost";
	$username = "root";
	$password = "uzbGU/AT";
	$dbname = "amalgamation";

	// Create connection
	global $conn;
	$conn = mysqli_connect($servername, $username, $password);
	// Check connection
	if (mysqli_connect_errno()) {
	 die("Connection failed " . $conn->connect_error);
	}

	$user = 1; //hard coded for now needs integration with login
	$userName = "SELECT * FROM amalgamation.users WHERE users.UserID = $user";
	$userNameResults = mysqli_query($conn, $userName);



	if(isset($_POST['title'])){
    $name = $_POST["title"];
    $desc = $_POST["desc"];
    $newProj = "INSERT IGNORE INTO amalgamation.projects (name, UserID, Description) VALUES ('$name','$user','$desc')";
		$success = mysqli_query($conn, $newProj);
	}

  $projects = "SELECT * FROM amalgamation.projects WHERE projects.UserID = $user";
	$projectResults = mysqli_query($conn, $projects);

  ?>
</head>

<body>
  <header>
    <img class="logo" src=../resources/images/logo2.png alt="amalgamation logo" width="200" height="50">
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
			<div ondblclick=\"location.href='doodling.html?id=". $row["ProjectID"] ."'\" class=\"display-window\">
			  <h3  class=\"centered\">". $row["name"] ."</h3>
			  <ul>
				<li>
				  ". $row["Description"] ."
				</li>
			  </ul>

        <div onclick= \"document.getElementById('myModal').style.display='block'\" class = \"bottom-right\">Share!
        </div>
			</div>";
        }

      ?>

    <div class="display-window new">
      <form action="../views/dashboard.php" method = "POST">
		  Title: <br><input type="text" id="title" name="title"><br>
      Description: <input type="text" id="desc" name="desc"><br>
		  <input type="submit" value="submit">
		</form>
	  <h3 class="centered">+</h3>
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
