<html>
 <head>
  <title>PHP Test</title>
 </head>
 <body>
   <?php

      $pw_hash = file_get_contents('hidden/pw_hash.txt');
      if (isset($_GET["pwd"]) && hash('sha256', $_GET["pwd"]) == $pw_hash) {  
         
         if (isset($_GET["p"]) && $_GET["p"] == 1) {  
            $pir_file = fopen("data/pir_data.txt", "a") or die("Unable to open pir file!");
            $data_file = fopen("data/data_flow.txt", "a") or die("Unable to open data flow file!");

            fwrite($pir_file, date("Y-m-d H:i:s") . "\n");
            fwrite($data_file, date("Y-m-d H:i:s") . " p True\n");
            fclose($pir_file);
            fclose($data_file);

            echo "<p>Success - PIR uploaded</p>";
         }
         if (isset($_GET["w"])) {  
            $weight_file = fopen("data/weight_data.txt", "a") or die("Unable to open weight file!");
            $data_file = fopen("data/data_flow.txt", "a") or die("Unable to open data flow file!");

            fwrite($weight_file, date("Y-m-d H:i:s") . " " . $_GET["w"] . "\n");
            fwrite($data_file, date("Y-m-d H:i:s") . " w " . $_GET["w"] . "\n");
            fclose($weight_file);
            fclose($data_file);

            echo "<p>Success - Weight uploaded</p>";
         }
      }
      else {
         echo "<p>Wrong password</p>";
      }
   ?>
 <a href="analyze.php"  class="button">Go to analyze</a>
 </body>
</html>