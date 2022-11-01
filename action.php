<?php
    if (isset($_POST['submit_btn'])) {
        $recaptcha = $_POST['g-recaptcha-response'];
        $secret_key = '6Lcy5s0iAAAAAM1p-hGeriJJ5cOlOCLLt3hXc96l';
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='. $secret_key . '&response=' . $recaptcha;
    
        $response = file_get_contents($url);
        $response = json_decode($response);

        if ($response->success == true) {
            $filename = "iplist.txt";
            $ip = $_SERVER['REMOTE_ADDR'];

            $fileread = fopen($filename, "r");
                
            if(!$fileread) {
                echo ("Error in opening file");
                exit();
            }

            $filesize = filesize($filename);
            $filetext = fread($fileread, $filesize);
            
            $mainarray = explode("\n", $filetext);
            foreach($mainarray as $value) {
                $subarray = explode(" ", $value);
                if (str_contains($value, $ip)) {
                    $checkpointnumber = $subarray[1];    
                }
            } 

            $checkpointnumber = (int)$checkpointnumber;
            $checkpointnumber += 1;

            $newfiletext = "lol\n";
            $deletetionarray = explode("\n", $filetext);
            
            foreach($deletetionarray as $delvalue) {
                if (!str_contains($delvalue, $ip)) {
                    $newfiletext2 = $newfiletext .= $delvalue;
                    $newfiletext = $newfiletext2 .= "\n";
                }
            } 

            fclose($fileread);

            $filewrite = fopen($filename, "w");
            if(!$filewrite) {
                echo("Error in opening file");
                exit();
            }
            fwrite($filewrite, "$newfiletext\n$ip $checkpointnumber");
            fclose($filewrite);
            header("Location: http://appdownload.wz.cz"); #you can change this to linkvertise
            exit();
        } else {
            echo("<scripterror</script>");
        }
        
    }
?>