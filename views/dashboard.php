<?php
   session_start();
   require_once '../vendor/jasig/phpcas/CAS.php';
   phpCAS::setDebug();
   phpCAS::setVerbose(true);
   phpCAS::client(CAS_VERSION_3_0, 'cas-auth.rpi.edu', 443, '/cas');
   phpCAS::setNoCasServerValidation();
   phpCAS::forceAuthentication();
   $casUser = strtolower($_SESSION['casLogin']) ? strtolower($_SESSION['casLogin']) : strtolower(phpCAS::getUser());

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

   //determines if there is existing user, inserts into DB if not
   $userName = "SELECT * FROM amalgamation.users WHERE users.rcs = '$casUser'";

   $userNameResults = mysqli_query($conn, $userName);
   if(mysqli_num_rows($userNameResults)==0){
	   $newUser = "INSERT INTO amalgamation.users (rcs) VALUES('$casUser')";
	   $addUser = mysqli_query($conn, $newUser);
   }

   //checks if a new post has been submitted, if it has, adds to DB
   if(isset($_POST['title']) and $_POST['title'] != ""){
      $name = $conn->escape_string($_POST["title"]);//real_escape_string helps defeat SQL injection
      $desc = $conn->escape_string($_POST["desc"]);

      $newProj = "INSERT INTO amalgamation.projects (name, Description) VALUES ('$name','$desc')";
      $success = mysqli_query($conn, $newProj);
      $newID = $conn->insert_id;

      $newPerm = "INSERT INTO amalgamation.permissions (ProjectID, rcs, perm) VALUES ('$newID','$casUser','owner')";
      $addPerm = mysqli_query($conn, $newPerm);
   }

   //if a user presses Delete Project, remove it from their view
   if( isset($_POST['deleteProject']) ) {
     $removeUser = $conn->real_escape_string($_POST['userName']);
     $projID = $conn->real_escape_string($_POST['shareNumber']);
     $removeQuery = "DELETE FROM amalgamation.permissions WHERE ProjectID = '$projID' AND rcs = '$casUser'";

     mysqli_query($conn, $removeQuery);
     if (mysqli_affected_rows($conn) == 0) {
            echo "<h3>Error: Project could not be deleted</h3>";
     } else {
            echo"<h3>Project successfully deleted</h3>";
            header("Refresh:0");
     }
}

   //display shared/owned projects on the dashboard
   $projects = "SELECT * FROM amalgamation.projects INNER JOIN amalgamation.permissions
	   ON projects.projectID = permissions.projectID
	   WHERE permissions.rcs = '$casUser';";

   $projectResults = mysqli_query($conn, $projects);

   $permissions = "SELECT * FROM amalgamation.permissions WHERE projectID = 4 AND (perm = 'owner' OR perm = 'edit')";
   $permitResults = mysqli_query($conn, $permissions);

   //creates the list of names and their permissions for the share modal
   function modelContent($num) {
	   global $conn;
	   $permissions = "SELECT * FROM amalgamation.permissions WHERE
	   projectID = $num AND (perm = 'owner' OR perm = 'edit')";
	   $permitResults = mysqli_query($conn, $permissions);
		$final = "";
		while($row = $permitResults->fetch_assoc()) {
	      $final .= "<p>". $row["rcs"] ."  ". $row["perm"] ."</p>";
	   }
	   return $final;
	}
?>
<!DOCTYPE html>
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
   </head>
   <body>
      <header>
         <a href="index.php"><img class="logo" src=../resources/images/logo1.png alt="amalgamation logo" width="200" height="50"></a>
         <img class="editing-icon" src="../resources/images/icons8-user.png" width="50" height="50"/>
      </header>
      <section class="colorbar"></section>
      <br/>

      <div id="main-body">
         <?php
	    //display user's RCS
            echo "<h1> Hello, ". $casUser ."</h1>";
	    //"add user" functionality
            if( isset($_POST['addUser']) ) {
               $shareUser = $conn->real_escape_string(strtolower($_POST['userName']));
               $projID = $conn->real_escape_string($_POST['shareNumber']);

               $vQuery = "SELECT * FROM amalgamation.permissions WHERE ProjectID = $projID";
               $flag = false;

               $results = mysqli_query($conn, $vQuery);
               while($row = $results->fetch_assoc()) {
                  if ($row['rcs'] == $shareUser) {
                     $flag = true;
                  }
               }
		//basic error checking for displaying info after an add
               if ($shareUser == $casUser) {
                  echo "<h2>Can't add yourself to your own project!</h2>";
               }
               else if (!$flag) {
                  $shareQuery = "INSERT INTO amalgamation.permissions (ProjectID, rcs, perm) VALUES ('$projID','$shareUser','edit')";
                  mysqli_query($conn, $shareQuery);
                  echo"<h2>Project Shared with $shareUser</h2>";
               }
               else {
                  echo "<h2>Project already shared with $shareUser</h2>";
               }
            }
	    
	    //"remove user" functionality
            if( isset($_POST['removeUser']) ) {
               $removeUser = $conn->real_escape_string(strtolower($_POST['userName']));
               $projID = $conn->real_escape_string($_POST['shareNumber']);

               if ($removeUser == $casUser) {
                  echo "<h2>Can't remove yourself from the project!</h2>";
               } 
               else {
                  $removeQuery = "DELETE FROM amalgamation.permissions WHERE rcs = '$removeUser' AND ProjectID = '$projID'";
                  mysqli_query($conn, $removeQuery);
                  if (mysqli_affected_rows($conn) == 0) {
                     echo "<h2>No user with RCS ID: $removeUser to remove</h2>";
                  } else {
                     echo"<h2>Edit permissions removed from $removeUser</h2>";
                  }	
               }	  
            }

            while($row = $projectResults->fetch_assoc()) {
               $x = $row["ProjectID"];
               if ($row['perm'] == "owner") {
                  echo "
                     <div ondblclick=\"location.href='doodling.php?id=". $row["ProjectID"] ."'\" class=\"display-window\">
                        <h3  class=\"centered\">". $row["name"] ."</h3>
                        <h4  class=\"centered\">Permissions: ". $row["perm"] ."</h4>
                        <ul>
                           <li>". $row["Description"] ."</li>
                        </ul>
                        <div onclick= \"document.getElementById('myModal".$x."').style.display='block'\" class = \"bottom-right\">Share!</div>
                     </div>
                     <div id=\"myModal".$x."\" class=\"modal\">
                        <!-- Modal content -->
                        <div class=\"modal-content\">
                           <a class='close' onclick=\"document.getElementById('myModal".$x."').style.display='none'\">x</a>
                           ". modelContent($x) ."
                           <form action=\"../views/dashboard.php\" method=\"POST\">
                              <input type=\"text\" id=\"userName\" name=\"userName\" placeholder='RCS here' ><br>
                              <input type=\"text\" id='shareNumber' name ='shareNumber' style=\"display:none\" value=$x>
                              <button type='submit' name='addUser'>Add User</button>
                              <button type='submit' name='removeUser'>Remove User</button>
                              <button type='submit' name='deleteProject'>Delete Project</button>
                           </form>
                        </div>
                     </div>"
                  ;
               } else {
                  echo "
                     <div ondblclick=\"location.href='doodling.php?id=". $row["ProjectID"] ."'\" class=\"display-window\">
                        <h3  class=\"centered\">". $row["name"] ."</h3>
                        <h4  class=\"centered\">Permissions: ". $row["perm"] ."</h4>
                        <ul>
                           <li>". $row["Description"] ."</li>
                        </ul>
                     </div>
                     <div id=\"myModal".$x."\" class=\"modal\">
                        <!-- Modal content -->
                        <div class=\"modal-content\">
                           <a class='close' onclick=\"document.getElementById('myModal".$x."').style.display='none'\">x</a>
                           ". modelContent($x) ."
                           <form action=\"../views/dashboard.php\" method=\"POST\">
                              <input type=\"text\" id=\"userName\" name=\"userName\" placeholder='RCS here' ><br>
                              <input type=\"text\" id='shareNumber' name ='shareNumber' style=\"display:none\" value=$x>
                              <button type='submit' name='addUser'>Add User</button>
                              <button type='submit' name='removeUser'>Remove User</button>
                              <button type='submit' name='deleteProject'>Delete Project</button>
                           </form>
                        </div>
                     </div>"
                  ;
               }
            }
         ?>
         <div id="add-window">
            <h3 class="centered"> Add Project </h3>
            <div class="centered">
               <form action="../views/dashboard.php" method="POST">
                  <label for="title">Title:</label>
                  <input type="text" id="title" name="title" required>
                  <label for="desc">Description:</label>
                  <input type="text" id="desc" name="desc">
                  <input type="submit" value="Submit">
               </form>
            </div>
         </div>
	   </div>
      <footer>
         <h2 id="teamTux">&copy; Team Tux</h2>
         <a href="https://github.com/TheStopsign/Tux" target="_blank"><img alt="Github Octocat" src="../resources/images/Octocat.png"/></a>
      </footer>
   </body>
</html>
