<html>
    <head>
      <title> Birdfeeder Collective | The SMALL Device</title>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
      <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
      <link rel="stylesheet" href="assets/css/main.css" />
      <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
      <!-- Load plotly.js into the DOM -->
      <script src='https://cdn.plot.ly/plotly-2.11.1.min.js'></script>
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
            <h1 div style="text-align: center; margin: 0px auto; display; block;">Data from weight sensor</h1>
            <div id='myDiv'><!-- Plotly chart will be drawn inside this DIV --></div>

            <?php
              if ($file = fopen("data/weight_data.txt", "r")) {
                
                echo '
                    <script>
                    var x = [];
                    var y = [];
                    var last_weight = -1;
                    var last_date = -1;
                ';

                  $count_dets = 0;
                  while(!feof($file)) {
                    $line = fgets($file);
                    if (strlen($line) == 0) {
                      continue;
                    }
                      $count_dets += 1;
                      echo '
                        last_date = "' . substr($line, 0, 19) .'";
                        x.push(new Date(last_date));
                        last_weight = ' . substr($line, 20, strlen($line)-20) . ';
                        y.push(last_weight);
                      ';
                  }

                  echo '
                        var trace1 = {
                            x: x,
                            y: y,
                            type: "scatter"
                        };
                        
                        var data = [trace1];
                        
                        var layout = {
                            title: "Weight since start", 
                            xaxis: {title: "Time",
                                    type: "date"}, 
                            yaxis: {title: "Weight (g)"},
                            showlegend: false
                        };

                        var config = {doubleClickDelay: 1000,
                                    scrollZoom: true};
                        
                        Plotly.newPlot("myDiv", data, layout, config);

                        document.write("<i>Last meassured weight:</i>&nbsp;&nbsp;&nbsp;&nbsp;" + last_weight + "g &nbsp;&nbsp; (" + last_date + ")");
                    </script>
                  ';
                  fclose($file);
              }
              else {
                echo "ERROR: cannot find data file";
              }
            ?>        
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
