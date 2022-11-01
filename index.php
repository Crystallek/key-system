<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content=
        "width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <center>
        <h2>
            <?php 



            function generateRandomString() {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < 25; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
            }

            $keydeletion = 30;
            $checkpoints = 2;


            $found = False;
            $filename = "iplist.txt";
            $keyfilename = "keylist.txt";
            $ip = $_SERVER['REMOTE_ADDR'];
            $checkpointnumber = 1;

            $fileread = fopen($filename, "r");
            
            if(!$fileread) {
                echo ("Error in opening file");
                exit();
            }

            $filesize = filesize($filename);
            $filetext = fread($fileread, $filesize);
            fclose($fileread);

            $mainarray = explode("\n", $filetext);
            foreach($mainarray as $value) {
                $subarray = explode(" ", $value);
                if (str_contains($value, $ip)) {
                    $checkpointnumber = $subarray[1];
                    $found = True;
                }
            } 

            if ($checkpointnumber >= $checkpoints) {
                $key = "None";
                $keyread = fopen($keyfilename, "r");
                $filesize = filesize($keyfilename);
                $filetext = fread($keyread, $filesize);
                
                $keyarray = explode("\n", $filetext);
                foreach($keyarray as $keyvalue) {
                    $keysubarray = explode(" ", $keyvalue);
                    if (str_contains($keyvalue, $ip)) {
                        $key = $keysubarray[1];
                        $deletiontime = $keysubarray[2];
                    }
                } 

                if ($key == "None") {
                    $key = generateRandomString();
                    $deletiontime = time() + $keydeletion;

                    $keywrite = fopen($keyfilename, "a");

                    fwrite($keywrite, "\n$ip $key $deletiontime");
                    fclose($keywrite);
                }

                fclose($keyread);
                $unixtime = (int)time();

                if ($unixtime >= $deletiontime) {
                    foreach($keyarray as $keyvalue) {
                        $keysubarray = explode(" ", $keyvalue);
                        if (!str_contains($keyvalue, $ip)) {
                            $newfiletext2 = $newfiletext .= $keyvalue;
                            $newfiletext = $newfiletext2 .= "\n";
                        }
                        
                    } 

                    echo("Your key has expired. Please reload the page.");

                    $filewrite = fopen($keyfilename, "w");

                    fwrite($filewrite, $newfiletext);
                    fclose($filewrite);
                    
                    $fileread = fopen($filename, "r");
                    $filesize = filesize($filename);
                    $filetext = fread($fileread, $filesize);
                    fclose($fileread);
                    
                    $delarray = explode("\n", $filetext);
                    foreach($keyarray as $keyvalue) {
                        $keysubarray = explode(" ", $keyvalue);
                        if (!str_contains($keyvalue, $ip)) {
                            $newfiletext2 = $newfiletext .= $keyvalue;
                            $newfiletext = $newfiletext2 .= "\n";
                        }
                    } 

                    $filewrite = fopen($filename, "w");
                    if(!$filewrite) {
                        echo("Error in opening file");
                        exit();
                    }
                    fwrite($filewrite, $newfiletext);
                    fclose($filewrite);

                    exit();
                }
                
            echo("<h1>Key:</h1>\n<h2>$key</h2>");
            exit();
            }

            if ($checkpointnumber == 1 and $found == False) {
                $filewrite = fopen($filename, "a");

                fwrite($filewrite, "\n$ip $checkpointnumber");
                fclose($filewrite);
            }

            echo("<h1>Checkpoint $checkpointnumber </h1>");
            ?>
        </h2>
        <div class="container">
        <form action="action.php" method="post">
            <div class="g-recaptcha" 
                data-sitekey="6Lcy5s0iAAAAADIqvqcW8RCqw0F40Z-l8i_Z9RpA">
            </div><br>
            <button type="submit" name="submit_btn">Submit</button>
        </form>
    </div>
    </center>
</body></html>
