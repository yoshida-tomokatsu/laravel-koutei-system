<?php
// 重複したpasswordカラムを整理
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧹 passwordカラム整理</h1>";

if (isset($_POST['cleanup'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=factory0328_wp2", "factory0328_wp2", "ctwjr3mmf5");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p>✅ データベース接続成功</p>";
        
        // 現在のテーブル構造確認
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>現在のカラム:</h3>";
        foreach ($columns as $column) {
            echo "<p>- {$column['Field']} ({$column['Type']})</p>";
        }
        
        // password_hashカラムを削除
        $pdo->exec("ALTER TABLE users DROP COLUMN password_hash");
        echo "<p>✅ password_hashカラムを削除しました</p>";
        
        // adminユーザーのパスワードを確実に更新
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = 'admin'");
        $stmt->execute([$admin_password]);
        echo "<p>✅ adminのパスワードを更新: <strong>password</strong></p>";
        
        // employeeユーザーのパスワードを確実に更新
        $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = 'employee'");
        $stmt->execute([$employee_password]);
        echo "<p>✅ employeeのパスワードを更新: <strong>employee123</strong></p>";
        
        // 修正後のテーブル構造確認
        echo "<h3>修正後のカラム:</h3>";
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "<p>- {$column['Field']} ({$column['Type']})</p>";
        }
        
        echo "<h2>🎉 整理完了！</h2>";
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
    echo "<p>usersテーブルに<code>password_hash</code>と<code>password</code>の両方のカラムがあります</p>";
    echo "<p><code>password_hash</code>カラムを削除し、<code>password</code>カラムだけにします</p>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='cleanup' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;'>🧹 カラム整理実行</button>";
    echo "</form>";
}
?> 