
<?php

  session_start();

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

	$rcs = strtolower($_SESSION['casLogin']); //hard coded for now needs integration with login
  // echo strtolower($rcs);
	// $users = "SELECT * FROM amalgamation.users WHERE users.rcs = " . strtolower($rcs);
	// $userResults = mysqli_query($conn, $users);

  // $myUser = $userResults->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/custom.css">
  <link rel="stylesheet" href="../resources/style.css?v=<?php echo time(); ?>">
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

  <script src="../literallycanvas/_js_libs/react-0.14.3.js"></script>
  <script src="../literallycanvas/_js_libs/literallycanvas.js"></script>

  <script defer>

    const rcs = "<?php echo $rcs;?>"

    window.addEventListener("load",function() {

      var lc = LC.init(document.getElementById("lc"), {
        imageURLPrefix: '../literallycanvas/_assets/lc-images',
        toolbarPosition: 'bottom',
        defaultStrokeWidth: 2,
        strokeWidths: [2, 3, 5, 10, 15, 30],
      });

      const doodleID = (new URL(window.location.href)).searchParams.get("id")

      const usersConnected = document.getElementById("users-connected")

      const sock = io("http://localhost:3001", {transports: ["websocket", "polling", "flashsocket"]});

      sock.on("connect", () => {
        console.log("connected to editing session")
        sock.emit("joinsession",{room: doodleID, rcs: rcs});
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
          caption.innerHTML = users[i].rcs
          // userimg.setAttribute("src","../resources/images/icons8-smiling.png")
          caption.setAttribute("width","48")
          // userimg.setAttribute("height","48")
          container.append(userimg)
          container.append(caption)
          usersConnected.append(container)
        }
      });

      const history = document.getElementById("history-list")

      function addToHistory(event) {
        let toadd = document.createElement("li")
        toadd.innerHTML = event.rcs + " added "+ event.shape.className
        history.append(toadd)
      }

      sock.on("draw", (data) => {
        lc.saveShape(LC.JSONToShape(data.shape), false, data.previousShapeId)
        addToHistory(data)
      });

      sock.on("loadsessionchanges", (data) => {
        if(data) {
          data.forEach(event => {
            lc.saveShape(LC.JSONToShape(event.shape), false, event.previousShapeId)
            addToHistory(event)
          })
        }
      });

      lc.on('shapeSave',function(args) {
        sock.emit("draw",{rcs: rcs, shape: LC.shapeToJSON(args.shape), previousShapeId: args.previousShapeId});
        addToHistory({rcs: rcs, shape: LC.shapeToJSON(args.shape), previousShapeId: args.previousShapeId})
      })
    })
  </script>
</head>
<body>

  <header>
    <a href="index.php"><img class="logo" src=../resources/images/logo1.png alt="amalgamation logo" width="200" height="50"></a>
    <a href="dashboard.php"><img class="editing-icon" src="../resources/images/icons8-folder-white.png" width="50" height="50"/></a>
  </header>

  <div id="doodling-container">
    <div id="content-area">

    </div>

    <div id="lc"></div>

    <div id="collab-info">
      <div id="users-connected">
      </div>
      <div id="history">
        <img class="editing-icon" src="../resources/images/history.png" width="50" height="50"/>
        <figcaption>History</figcaption>
        <ol id="history-list">
        </ol>
      </div>
      <!-- <div id="revision-history">
        
      </div> -->
    </div>
  </div>

  <footer>         
    <h2 id="teamTux">&copy; Team Tux</h2>
    <a href="https://github.com/TheStopsign/Amalgamation" target="_blank"><img alt="Github Octocat" src="../resources/images/Octocat.png"/></a>
  </footer>

</body>

</html>