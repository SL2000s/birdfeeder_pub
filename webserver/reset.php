<?php
    $pw_hash = file_get_contents('hidden/pw_hash.txt');
    if (isset($_GET["pwd"]) && hash('sha256', $_GET["pwd"]) == $pw_hash) {  
        // Delete data
        $pirfile = fopen("data/pir_data.txt", "w") or die("Unable to open file!");
        fwrite($pirfile, "");
        fclose($pirfile);
        $weightfile = fopen("data/weight_data.txt", "w") or die("Unable to open file!");
        fwrite($weightfile, "");
        fclose($weightfile);
        $datafile = fopen("data/data_flow.txt", "w") or die("Unable to open file!");
        fwrite($datafile, "");
        fclose($datafile);
        
        // Redirect back to upload
        header("Location: analyze.php");
        exit();
    }
    else {
        echo "<p>Wrong password</p>";
    }
?>