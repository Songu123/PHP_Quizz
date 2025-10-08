<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Debug</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        h2 {
            color: #764ba2;
            margin-top: 30px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }
        .error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
        .info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            color: #1565c0;
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
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f5f5f5;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-danger {
            background: #f44336;
        }
        .btn-danger:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database & OAuth Status</h1>
        
        <?php
        require_once '../app/config/config.php';
        require_once '../app/config/google_oauth.php';
        
        // Test database connection
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<div class='status success'>✅ Database connection: OK</div>";
        } catch(PDOException $e) {
            echo "<div class='status error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
            exit();
        }
        
        // Check table structure
        echo "<h2>1. Database Structure</h2>";
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach($columns as $col) {
            $highlight = in_array($col['Field'], ['username', 'google_id', 'role', 'password']) ? "style='background:#fff3e0;'" : "";
            echo "<tr $highlight>";
            echo "<td><strong>" . $col['Field'] . "</strong></td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . $col['Key'] . "</td>";
            echo "<td>" . ($col['Default'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Check required columns
        $requiredColumns = ['username', 'email', 'google_id', 'password', 'role'];
        $columnNames = array_column($columns, 'Field');
        
        echo "<div class='status info'>";
        echo "<strong>Required columns check:</strong><br>";
        foreach($requiredColumns as $req) {
            $exists = in_array($req, $columnNames) ? "✅" : "❌";
            echo "$exists <code>$req</code><br>";
        }
        echo "</div>";
        
        // Check existing users
        echo "<h2>2. Current Users</h2>";
        $stmt = $pdo->query("SELECT id, username, email, google_id, role, created_at FROM users ORDER BY created_at DESC LIMIT 10");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Google ID</th><th>Role</th><th>Created</th></tr>";
            foreach($users as $user) {
                $isGoogle = $user['google_id'] ? "style='background:#e8f5e9;'" : "";
                echo "<tr $isGoogle>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td><strong>" . $user['username'] . "</strong></td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . ($user['google_id'] ? '✅ ' . substr($user['google_id'], 0, 20) . '...' : '❌') . "</td>";
                echo "<td><span style='padding:4px 8px;background:#667eea;color:white;border-radius:3px;'>" . $user['role'] . "</span></td>";
                echo "<td>" . $user['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='status info'>📝 No users in database yet</div>";
        }
        
        // OAuth Configuration
        echo "<h2>3. Google OAuth Configuration</h2>";
        echo "<div class='status info'>";
        echo "<strong>Client ID:</strong> " . substr(GOOGLE_CLIENT_ID, 0, 30) . "...<br>";
        echo "<strong>Redirect URI:</strong> <code>" . GOOGLE_REDIRECT_URI . "</code><br>";
        echo "<strong>Scopes:</strong> " . count(GOOGLE_OAUTH_SCOPE) . " configured<br>";
        echo "</div>";
        
        // Test username generation
        echo "<h2>4. Username Generation Test</h2>";
        $testEmails = [
            'john.doe@gmail.com',
            'test@example.com',
            'long.email.address@domain.com'
        ];
        
        echo "<table>";
        echo "<tr><th>Email</th><th>Generated Username</th></tr>";
        foreach($testEmails as $email) {
            $username = explode('@', $email)[0];
            $username = str_replace('.', '', $username); // Remove dots if needed
            echo "<tr>";
            echo "<td>" . $email . "</td>";
            echo "<td><code>" . $username . "</code></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Actions
        echo "<h2>5. Actions</h2>";
        echo "<a href='" . URLROOT . "/auth/login' class='btn'>🔐 Go to Login Page</a>";
        echo "<a href='" . URLROOT . "/auth/googlelogin' class='btn'>🔑 Test Google Login</a>";
        echo "<a href='test_redirect_uri.php' class='btn'>🔍 OAuth Debug Tool</a>";
        
        // Delete test user
        if (isset($_GET['delete_google_users'])) {
            $pdo->exec("DELETE FROM users WHERE google_id IS NOT NULL");
            echo "<div class='status success'>✅ Deleted all Google users. <a href='database_debug.php'>Refresh page</a></div>";
        } else {
            $googleCount = $pdo->query("SELECT COUNT(*) FROM users WHERE google_id IS NOT NULL")->fetchColumn();
            if ($googleCount > 0) {
                echo "<a href='database_debug.php?delete_google_users=1' class='btn btn-danger' onclick='return confirm(\"Delete $googleCount Google users?\")'>🗑️ Delete Google Users ($googleCount)</a>";
            }
        }
        
        ?>
        
        <h2>6. Troubleshooting</h2>
        <div class="status info">
            <strong>Common Issues:</strong>
            <ul>
                <li><code>Duplicate entry '' for key 'username'</code> → Fixed! Username now auto-generated from email</li>
                <li><code>redirect_uri_mismatch</code> → Check Google Console redirect URI matches: <code><?php echo GOOGLE_REDIRECT_URI; ?></code></li>
                <li><code>invalid_client</code> → Verify Client ID and Secret in <code>app/config/google_oauth.php</code></li>
                <li><code>Yêu cầu không hợp lệ</code> → Add your email to Test users in OAuth consent screen</li>
            </ul>
        </div>
    </div>
</body>
</html>
