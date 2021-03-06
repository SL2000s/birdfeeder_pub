<html>
    <head>
		<title> Birdfeeder Collective | The SMALL Device</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    </head>
    <body class="is-preload">
		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Header -->
					<header id="header">
						<div class="inner">

							<!-- Logo -->
								<a href="index.html" class="logo">
									<img src="images/logo.PNG" width="50"/> <span class="title">The Birdfeeder Collective</span>
								</a>

							<!-- Nav -->
								<nav>
									<ul>
										<li><a href="#menu">Menu</a></li>
									</ul>
								</nav>

						</div>
					</header>

				<!-- Menu -->
					<nav id="menu">
						<h2>Menu</h2>
						<ul>
							<li><a href="index.html" class="active">Home</a></li>

							<li>
								<a href="analyze.php">Analyze</a>
								<ul>
									<li><a href="analyze.php">All Graphs</a></li>
									<li><a href="birddetection_stats.php">Bird Detections</a></li>
									<li><a href="weight_stats.php">Bird Food</a></li>
									<li><a href="raw_data.php">Raw Data</a></li>
								</ul>
							</li>
							

							<li><a href="blog.html">Blog</a></li>

							<li>
								<a href="about.html" class="dropdown-toggle">About</a>

								<ul>
									<li><a href="about.html">About Us</a></li>
									<li><a href="team.html">Team</a></li>
								</ul>
							</li>
							<li><a href="contact.html">Contact Us</a></li>
						</ul>
					</nav>

				<!-- Main -->
                <div id="main">
                    <div class="inner">
					<a href="analyze.php">Back to analyze</a>
            			<br>
            			<br>
                        <h1>Data flow from birdfeeder</h1> 
                        <table>
                            <tr>
                                <th>Sensor</th>
								<th>Value</th>
                                <th>Time</th>
                                <th>Date</th>
                            </tr>
                            <?php
                                if ($file = fopen("data/data_flow.txt", "r")) {
                                    while(!feof($file)) {
                                        $line = fgets($file);
                                        if (strlen($line) == 0) {
                                            continue;
                                        }
										if (substr($line, 20, 1) == "p") {
											$sensor = "PIR";
										}
										else if (substr($line, 20, 1) == "w") {
											$sensor = "Weight";
										}
										$value = substr($line, 22, strlen($line) - 22);
										$time = substr($line, 11, 8);
										$date = substr($line, 0, 10);
                                        echo "<tr><td>" . $sensor . "</td><td>" . $value . "</td><td>" . $time . "</td><td>" . $date . "</td></tr>";
                                    }
                                    fclose($file);
                                }   
                            ?>
                        </table>
                        
						<a class="button" onclick="pwd_reset()">Reset Data</a>
						<script>
							function pwd_reset() {
								let pwd = prompt("Please enter the reset password!", "");
								if (pwd.length > 0) {
									location.href = 'reset.php?pwd=' + pwd;
								}
							}							
						</script>
						

						<br>
						<br>
						<br>
							<b>Visualization of the data may be found here:</b>
							<dl>
									<dd>- <a href="analyze.php">All graphs</a></dd>
									<dd>- <a href="birddetection_stats.php">Bird Detection Stats</a></dd>
									<dd>- <a href="weight_stats.php">Bird Food Stats</a></dd>

							</dl>	
                    </div>
                </div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/main.js"></script>
    </body>
</html>