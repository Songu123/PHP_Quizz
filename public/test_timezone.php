<?php
/**
 * Test timezone và thời gian database
 */

// Kết nối database
$host = 'localhost';
$dbname = 'quizz_loq';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🕐 Test Timezone & Time</h2>";
    
    echo "<h3>PHP Settings:</h3>";
    echo "PHP Timezone: " . date_default_timezone_get() . "<br>";
    echo "PHP Current Time: " . date('Y-m-d H:i:s') . "<br>";
    echo "PHP time(): " . time() . "<br>";
    echo "PHP +15 minutes: " . date('Y-m-d H:i:s', strtotime('+15 minutes')) . "<br>";
    
    echo "<hr>";
    
    echo "<h3>MySQL Settings:</h3>";
    $stmt = $pdo->query("SELECT NOW() as mysql_now, @@system_time_zone as system_tz, @@session.time_zone as session_tz");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "MySQL NOW(): " . $result['mysql_now'] . "<br>";
    echo "System Timezone: " . $result['system_tz'] . "<br>";
    echo "Session Timezone: " . $result['session_tz'] . "<br>";
    
    echo "<hr>";
    
    echo "<h3>Test DATE_ADD:</h3>";
    $stmt = $pdo->query("SELECT NOW() as now, DATE_ADD(NOW(), INTERVAL 15 MINUTE) as plus_15_min");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "NOW(): " . $result['now'] . "<br>";
    echo "DATE_ADD(NOW(), INTERVAL 15 MINUTE): " . $result['plus_15_min'] . "<br>";
    
    echo "<hr>";
    
    echo "<h3>Password Resets Table:</h3>";
    $stmt = $pdo->query("SELECT email, token, created_at, expires_at, 
                         TIMESTAMPDIFF(SECOND, NOW(), expires_at) as seconds_left,
                         TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_left
                         FROM password_resets ORDER BY created_at DESC LIMIT 5");
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Email</th><th>Token</th><th>Created At</th><th>Expires At</th><th>Seconds Left</th><th>Minutes Left</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['token']) . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td>" . $row['expires_at'] . "</td>";
        echo "<td style='color:" . ($row['seconds_left'] > 0 ? 'green' : 'red') . "'>" . $row['seconds_left'] . "s</td>";
        echo "<td style='color:" . ($row['minutes_left'] > 0 ? 'green' : 'red') . "'>" . $row['minutes_left'] . "m</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<hr>";
    echo "<p><strong>✅ Fix Suggestion:</strong> Sử dụng <code>DATE_ADD(NOW(), INTERVAL 15 MINUTE)</code> trong SQL thay vì PHP date()</p>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f5f5f5;
    }
    table {
        background: white;
        border-collapse: collapse;
        margin: 10px 0;
    }
    th {
        background: #667eea;
        color: white;
        padding: 10px;
    }
    td {
        padding: 8px;
    }
    h2 {
        color: #667eea;
    }
    code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>
