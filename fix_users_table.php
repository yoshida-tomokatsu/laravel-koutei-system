<?php
// usersテーブル修正スクリプト
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>👥 usersテーブル修正</h1>";

if (isset($_POST['fix_table'])) {
    try {
        // データベース接続
        $pdo = new PDO("mysql:host=localhost;dbname=factory0328_wp2", "factory0328_wp2", "ctwjr3mmf5");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p>✅ データベース接続成功</p>";
        
        // 現在のテーブル構造確認
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>現在のusersテーブル構造:</h3>";
        foreach ($columns as $column) {
            echo "<p>- {$column['Field']} ({$column['Type']})</p>";
        }
        
        // passwordカラムが存在するかチェック
        $column_names = array_column($columns, 'Field');
        
        if (!in_array('password', $column_names)) {
            echo "<p>⚠️ passwordカラムが存在しません。追加します...</p>";
            $pdo->exec("ALTER TABLE users ADD COLUMN password VARCHAR(255) NOT NULL AFTER email");
            echo "<p>✅ passwordカラムを追加しました</p>";
        } else {
            echo "<p>✅ passwordカラムは既に存在します</p>";
        }
        
        // user_idカラムが存在するかチェック
        if (!in_array('user_id', $column_names)) {
            echo "<p>⚠️ user_idカラムが存在しません。追加します...</p>";
            $pdo->exec("ALTER TABLE users ADD COLUMN user_id VARCHAR(255) UNIQUE AFTER name");
            echo "<p>✅ user_idカラムを追加しました</p>";
        } else {
            echo "<p>✅ user_idカラムは既に存在します</p>";
        }
        
        // roleカラムが存在するかチェック
        if (!in_array('role', $column_names)) {
            echo "<p>⚠️ roleカラムが存在しません。追加します...</p>";
            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'user' AFTER password");
            echo "<p>✅ roleカラムを追加しました</p>";
        } else {
            echo "<p>✅ roleカラムは既に存在します</p>";
        }
        
        // 管理者ユーザーの作成・更新
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        
        // 既存のadminユーザーをチェック
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = 'admin'");
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            // adminユーザーが存在しない場合は作成
            $stmt = $pdo->prepare("INSERT INTO users (user_id, password, name, email, role, created_at, updated_at) VALUES ('admin', ?, '管理者', 'admin@example.com', 'admin', NOW(), NOW())");
            $stmt->execute([$admin_password]);
            echo "<p>✅ adminユーザーを作成しました</p>";
        } else {
            // 既存のadminユーザーのパスワードを更新
            $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', updated_at = NOW() WHERE user_id = 'admin'");
            $stmt->execute([$admin_password]);
            echo "<p>✅ adminユーザーのパスワードを更新しました</p>";
        }
        
        // 従業員ユーザーの作成・更新
        $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = 'employee'");
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (user_id, password, name, email, role, created_at, updated_at) VALUES ('employee', ?, '従業員', 'employee@example.com', 'employee', NOW(), NOW())");
            $stmt->execute([$employee_password]);
            echo "<p>✅ employeeユーザーを作成しました</p>";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'employee', updated_at = NOW() WHERE user_id = 'employee'");
            $stmt->execute([$employee_password]);
            echo "<p>✅ employeeユーザーのパスワードを更新しました</p>";
        }
        
        // 最終確認
        echo "<h3>修正後のユーザー一覧:</h3>";
        $stmt = $pdo->query("SELECT user_id, name, email, role, created_at FROM users ORDER BY id");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($users) {
            echo "<table border='1' style='border-collapse:collapse; margin:10px 0;'>";
            echo "<tr style='background:#f0f0f0;'><th>ユーザーID</th><th>名前</th><th>メール</th><th>権限</th><th>作成日</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<h2>🎉 テーブル修正完了！</h2>";
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
    echo "<p>usersテーブルに必要なカラム（password、user_id、role）が不足しています</p>";
    echo "<p>テーブル構造を修正し、正しいユーザーを設定します</p>";
    
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_table' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;'>🚨 テーブル修正実行</button>";
    echo "</form>";
}
?> 