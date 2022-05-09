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
            <h1 div style="text-align: center; margin: 0px auto; display; block;">Visualization of data</h1>
            <div id='pirDiv'><!-- Plotly chart will be drawn inside this DIV --></div>
            <div id='weightDiv'><!-- Plotly chart will be drawn inside this DIV --></div>

            <?php
              if ($file = fopen("data/pir_data.txt", "r")) {

                echo '
                  <script>
                    var n_hours = 48;
                    var now = new Date("' . date("Y-m-d H:i:s") . '");
                    var now_rounded = new Date(now);
                    now_rounded.setMinutes(0, 0, 0);
                    var period_count = 0;
                    var hour_count = 0;
        
                    var x1 = [];
                    for (var i = (-1)*(n_hours - 1); i <= 0; i++) {
                        var t = new Date(now.getTime() + (1000*60*60*i));
                        x1.push((t.getYear() + 1900).toString() + "-" + (t.getMonth()+1).toString().padStart(2, "0") + "-" + t.getDate().toString().padStart(2, "0") + " " + t.getHours().toString().padStart(2, "0") + ":00");
                    }
                    var y1 = Array(n_hours).fill(0);
                ';
                  $count_dets = 0;
                  while(!feof($file)) {
                    $line = fgets($file);
                    if (strlen($line) == 0) {
                      continue;
                    }
                      $count_dets += 1;
                      echo 'var t = new Date("' . substr($line, 0, 19) . '");
                        var d = Math.floor((t - now_rounded)/(1000*60*60));
                        var i = (n_hours-1) + d;
                        if (i >= 0) {
                          y1[i] += 1;
                          period_count += 1;
                        }
                        if (now - t < 1000*60*60) {
                          hour_count += 1;
                        }
                      ';
                  }

                  echo '
                      var data = [{
                        type: "bar",
                        opacity: 1,
                        y: y1,
                        x: x1
                      }];
        
                      var layout = {
                        bargap: 0.05, 
                        bargroupgap: 0.2, 
                        barmode: "overlay", 
                        title: "Detections per hour", 
                        xaxis: {title: "Time",
                                type: "date"}, 
                        yaxis: {title: "Count"}
                      };
        
                      var config = {doubleClickDelay: 1000,
                                    scrollZoom: true};
        
                      Plotly.newPlot("pirDiv", data, layout, config);


                    </script>
                  ';
                  
                  fclose($file);
              }
              else {
                echo "ERROR: cannot find data file";
              }
            ?>      
            <br>
            <br>
            
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
                        
                        Plotly.newPlot("weightDiv", data, layout, config);

                        
                    </script>
                  ';
                  fclose($file);
              }
              else {
                echo "ERROR: cannot find data file";
              }
            ?>  

            See the raw data <a href="raw_data.php">here</a>
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
