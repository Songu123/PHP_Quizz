<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Username Generation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #11998e;
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
        }
        .test-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #11998e;
        }
        .test-section h2 {
            color: #11998e;
            font-size: 20px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #11998e;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        code {
            background: #e9ecef;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #c62828;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #11998e;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            transition: all 0.3s;
            font-weight: bold;
        }
        .btn:hover {
            background: #38ef7d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .existing {
            background: #fff3e0 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Username Generation Test</h1>
        
        <?php
        require_once '../app/config/config.php';
        
        // Connect to database
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("<div class='info' style='border-color:#f44336;background:#ffebee;'>❌ Database connection failed: " . $e->getMessage() . "</div>");
        }
        
        // Function to generate username
        function generateUsername($email, $pdo) {
            $username = explode('@', $email)[0];
            $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
            
            if (empty($username)) {
                $username = 'user' . rand(1000, 9999);
            }
            
            $originalUsername = $username;
            $counter = 1;
            
            // Check if exists
            $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->execute([$username]);
            
            while ($stmt->fetch()) {
                $username = $originalUsername . $counter;
                $counter++;
                $stmt->execute([$username]);
            }
            
            return $username;
        }
        
        // Test emails
        echo "<div class='test-section'>";
        echo "<h2>1. Test Email → Username Conversion</h2>";
        
        $testEmails = [
            'john.doe@gmail.com',
            'test.user@example.com',
            'admin123@domain.com',
            'long.email.address.here@company.com',
            'user_with_underscore@test.com',
            'number123@gmail.com',
            '___special___@test.com',
            'vietnamesename@gmail.com'
        ];
        
        echo "<table>";
        echo "<tr><th>Email</th><th>Generated Username</th><th>Status</th></tr>";
        
        foreach ($testEmails as $email) {
            $username = generateUsername($email, $pdo);
            
            // Check if exists in DB
            $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $exists = $stmt->fetch() ? "⚠️ Already exists" : "✅ Available";
            $rowClass = $stmt->fetch() ? "class='existing'" : "";
            
            echo "<tr $rowClass>";
            echo "<td>" . htmlspecialchars($email) . "</td>";
            echo "<td><code>" . htmlspecialchars($username) . "</code></td>";
            echo "<td>" . $exists . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
        
        // Current users
        echo "<div class='test-section'>";
        echo "<h2>2. Existing Users in Database</h2>";
        
        $stmt = $pdo->query("SELECT id, username, email, google_id, role FROM users ORDER BY created_at DESC LIMIT 10");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Source</th><th>Role</th></tr>";
            
            foreach ($users as $user) {
                $source = $user['google_id'] ? "🔑 Google" : "📧 Email";
                $rowClass = $user['google_id'] ? "class='existing'" : "";
                
                echo "<tr $rowClass>";
                echo "<td>#" . $user['id'] . "</td>";
                echo "<td><code>" . htmlspecialchars($user['username']) . "</code></td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . $source . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No users found in database.</p>";
        }
        
        echo "</div>";
        
        // Rules
        echo "<div class='info'>";
        echo "<h3 style='color:#1565c0;margin-bottom:15px;'>📋 Username Generation Rules</h3>";
        echo "<ol style='margin-left:20px;'>";
        echo "<li>Extract part before @ from email</li>";
        echo "<li>Remove special characters (keep only letters and numbers)</li>";
        echo "<li>If empty after filtering, use 'user' + random number</li>";
        echo "<li>If username exists, append counter (1, 2, 3...)</li>";
        echo "<li>Default role: <strong>student</strong></li>";
        echo "</ol>";
        echo "</div>";
        
        // Examples
        echo "<div class='test-section'>";
        echo "<h2>3. Examples</h2>";
        echo "<table>";
        echo "<tr><th>Input Email</th><th>Step 1: Extract</th><th>Step 2: Clean</th><th>Final Username</th></tr>";
        
        $examples = [
            ['email' => 'john.doe@gmail.com', 'extract' => 'john.doe', 'clean' => 'johndoe', 'final' => 'johndoe'],
            ['email' => 'test_123@test.com', 'extract' => 'test_123', 'clean' => 'test123', 'final' => 'test123'],
            ['email' => '___@test.com', 'extract' => '___', 'clean' => '(empty)', 'final' => 'user1234'],
        ];
        
        foreach ($examples as $ex) {
            echo "<tr>";
            echo "<td><code>" . $ex['email'] . "</code></td>";
            echo "<td>" . $ex['extract'] . "</td>";
            echo "<td>" . $ex['clean'] . "</td>";
            echo "<td><strong>" . $ex['final'] . "</strong></td>";
            echo "</tr>";
        }
        
        echo "</table>";
        echo "</div>";
        
        // Actions
        echo "<div style='text-align:center;margin-top:30px;'>";
        echo "<a href='" . URLROOT . "/auth/register' class='btn'>📝 Test Register</a>";
        echo "<a href='" . URLROOT . "/auth/login' class='btn'>🔐 Test Login</a>";
        echo "<a href='database_debug.php' class='btn'>🔍 Database Debug</a>";
        echo "</div>";
        ?>
    </div>
</body>
</html>
