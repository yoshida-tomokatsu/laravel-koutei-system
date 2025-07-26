<?php
// password_hashカラムをpasswordに変更
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 passwordカラム名修正</h1>";

if (isset($_POST['rename_column'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=factory0328_wp2", "factory0328_wp2", "ctwjr3mmf5");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p>✅ データベース接続成功</p>";
        
        // password_hashカラムをpasswordに変更
        $pdo->exec("ALTER TABLE users CHANGE password_hash password VARCHAR(255) NOT NULL");
        echo "<p>✅ password_hashカラムをpasswordに変更しました</p>";
        
        // adminユーザーのパスワードを更新
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = 'admin'");
        $stmt->execute([$admin_password]);
        echo "<p>✅ adminのパスワードを更新: <strong>password</strong></p>";
        
        // employeeユーザーのパスワードを更新
        $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = 'employee'");
        $stmt->execute([$employee_password]);
        echo "<p>✅ employeeのパスワードを更新: <strong>employee123</strong></p>";
        
        echo "<h2>🎉 修正完了！</h2>";
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
    echo "<p>usersテーブルに<code>password_hash</code>カラムがありますが、</p>";
    echo "<p>Laravelは<code>password</code>カラムを期待しています。</p>";
    echo "<p>カラム名を変更して、パスワードを正しく設定します。</p>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='rename_column' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;'>🚨 カラム名修正実行</button>";
    echo "</form>";
}
?> 