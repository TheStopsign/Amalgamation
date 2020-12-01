
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

	$user = 1; //hard coded for now needs integration with login
	$userName = "SELECT * FROM amalgamation.users WHERE users.UserID = $user";
	$userNameResults = mysqli_query($conn, $userName);

  $myName = $userNameResults->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../resources/style.css"/>
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
  
  <title>Doodling - amalgamation</title>

  <script src="../node_modules/socket.io-client/dist/socket.io.js"></script>
  <meta name="viewport" content="width=device-width, user-scalable=no" />
  <meta charset="UTF-8">

  <script defer>

    const userID = "<?php echo $user;?>"
    const userName = "<?php echo $myName["name"];?>"

    window.addEventListener("load",function() {

      const doodleID = (new URL(window.location.href)).searchParams.get("id")

      const usersConnected = document.getElementById("users-connected")

      const sock = io("http://localhost:3001", {transports: ["websocket", "polling", "flashsocket"]});

      sock.on("connect", () => {
        console.log("connected to editing session")
        sock.emit("joinsession",{room: doodleID, userID: userID, userName: userName});
      });

      sock.on("usersupdate", (users) => {
        usersConnected.innerHTML = ""
        for(let i=0;i<users.length;i++) {
          let container = document.createElement("div")
          container.setAttribute("class","editing-icon-container")
          let userimg = document.createElement("img")
          userimg.setAttribute("class","editing-icon")
          userimg.setAttribute("src","../resources/images/icons8-smiling.png")
          userimg.setAttribute("width","48")
          userimg.setAttribute("height","48")
          let caption = document.createElement("figcaption")
          caption.innerHTML = users[i].userName
          // userimg.setAttribute("src","../resources/images/icons8-smiling.png")
          caption.setAttribute("width","48")
          // userimg.setAttribute("height","48")
          container.append(userimg)
          container.append(caption)
          usersConnected.append(container)
        }
      });

      sock.on("draw", (data) => {
        console.log(data.userName + " doodled!")
      });

      document.getElementsByClassName("lc-drawing")[0].addEventListener("click",function() {
        sock.emit("draw",{userName: userName})
      })
    })
  </script>
</head>
<body>

  <header>
    <a  href="dashboard.php"><img class="logo" src=../resources/images/logo2.png alt="amalgamation logo" width="200" height="50"></a>
    <img class="editing-icon" src="../resources/images/icons8-user.png" width="50" height="50"/>
    <img class="editing-icon" src="../resources/images/icons8-folder.png" width="50" height="50"/>
  </header>

  <div id="doodling-container">
    <div id="content-area">

    </div>

    <div id="lc"></div>
    <script src="../literallycanvas/_js_libs/react-0.14.3.js"></script>
    <script src="../literallycanvas/_js_libs/literallycanvas.js"></script>

    <script type="text/javascript">
      var lc = LC.init(document.getElementById("lc"), {
        imageURLPrefix: '../literallycanvas/_assets/lc-images',
        toolbarPosition: 'bottom',
        defaultStrokeWidth: 2,
        strokeWidths: [2, 3, 5, 10, 15, 30]
      });
    </script>

    <div id="collab-info">
      <div id="users-connected">
        <!-- <img class="editing-icon" src="../resources/images/icons8-smiling.png" width="48" height="48"/>
        <img class="editing-icon" src="../resources/images/icons8-smiling.png" width="48" height="48"/>
        <img class="editing-icon" src="../resources/images/icons8-smiling.png" width="48" height="48"/>
        <img class="editing-icon" src="../resources/images/icons8-smiling.png" width="48" height="48"/>
        <img class="editing-icon" src="../resources/images/icons8-smiling.png" width="48" height="48"/> -->
      </div>
      <div id="chat">
        <img class="editing-icon" src="../resources/images/icons8-chat.png" width="50" height="50"/>
      </div>
      <div id="revision-history">
        
      </div>
    </div>
  </div>

  <footer>         
    <h2 id="teamTux">&copy; Team Tux</h2>
    <a href="https://github.com/TheStopsign/Amalgamation" target="_blank"><img alt="Github Octocat" src="../resources/images/Octocat.png"/></a>
  </footer>

</body>

</html>