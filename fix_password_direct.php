<?php
// パスワード直接修正スクリプト
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔐 パスワード直接修正</h1>";

if (isset($_POST['fix_password'])) {
    try {
        // データベース接続
        $pdo = new PDO("mysql:host=localhost;dbname=factory0328_wp2", "factory0328_wp2", "ctwjr3mmf5");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p>✅ データベース接続成功</p>";
        
        // 現在のユーザー確認
        $stmt = $pdo->query("SELECT user_id, name, password FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>現在のユーザー:</h3>";
        foreach ($users as $user) {
            echo "<p>- {$user['user_id']} ({$user['name']})</p>";
        }
        
        // 管理者パスワードを確実に更新
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = 'admin'");
        $stmt->execute([$admin_password]);
        echo "<p>✅ adminパスワード更新: <strong>password</strong></p>";
        
        // 従業員パスワードを確実に更新
        $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = 'employee'");
        $stmt->execute([$employee_password]);
        echo "<p>✅ employeeパスワード更新: <strong>employee123</strong></p>";
        
        // 管理者が存在しない場合は作成
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (user_id, password, name, email, role, created_at, updated_at) VALUES ('admin', ?, '管理者', 'admin@example.com', 'admin', NOW(), NOW())");
            $stmt->execute([$admin_password]);
            echo "<p>✅ adminユーザー作成</p>";
        }
        
        // 従業員が存在しない場合は作成
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = 'employee'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (user_id, password, name, email, role, created_at, updated_at) VALUES ('employee', ?, '従業員', 'employee@example.com', 'employee', NOW(), NOW())");
            $stmt->execute([$employee_password]);
            echo "<p>✅ employeeユーザー作成</p>";
        }
        
        echo "<h2>🎉 パスワード修正完了！</h2>";
        echo "<h3>ログイン情報:</h3>";
        echo "<ul>";
        echo "<li><strong>管理者:</strong> admin / password</li>";
        echo "<li><strong>従業員:</strong> employee / employee123</li>";
        echo "</ul>";
        echo "<p><a href='https://koutei.kiryu-factory.com/login' target='_blank'>ログインページでテスト</a></p>";
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ エラー: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h2>問題の説明</h2>";
    echo "<p>ログインページは表示されるが、パスワードが違うエラーが出る</p>";
    echo "<p>データベースのパスワードハッシュを直接修正します</p>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_password' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;'>🚨 パスワード修正実行</button>";
    echo "</form>";
}
?> 