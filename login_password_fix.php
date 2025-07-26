<?php
// ログインパスワード問題 & 404エラー修正スクリプト
// APP_KEY修正後のログイン関連問題を解決

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔐 ログインパスワード問題 & 404エラー修正</h1>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>実行場所: " . __DIR__ . "</p>";

// 修正を実行するかの確認
if (isset($_POST['fix_login'])) {
    echo "<h2>🚀 ログイン問題修正実行中...</h2>";
    
    try {
        // 1. Laravel環境の確認
        echo "<h3>📋 Laravel環境確認</h3>";
        
        if (!file_exists('vendor/autoload.php')) {
            echo "<p>❌ vendor/autoload.php が見つかりません</p>";
            echo "<p>composer install が必要です</p>";
        } else {
            echo "<p>✅ vendor/autoload.php 存在</p>";
        }
        
        if (!file_exists('bootstrap/app.php')) {
            echo "<p>❌ bootstrap/app.php が見つかりません</p>";
        } else {
            echo "<p>✅ bootstrap/app.php 存在</p>";
        }
        
        if (!file_exists('.env')) {
            echo "<p>❌ .env ファイルが見つかりません</p>";
        } else {
            echo "<p>✅ .env ファイル存在</p>";
        }
        
        // 2. データベース接続テスト
        echo "<h3>🔧 データベース接続テスト</h3>";
        
        $env_content = file_get_contents('.env');
        preg_match('/DB_HOST=(.*)/', $env_content, $host_match);
        preg_match('/DB_DATABASE=(.*)/', $env_content, $db_match);
        preg_match('/DB_USERNAME=(.*)/', $env_content, $user_match);
        preg_match('/DB_PASSWORD=(.*)/', $env_content, $pass_match);
        
        $host = trim($host_match[1] ?? 'localhost');
        $database = trim($db_match[1] ?? '');
        $username = trim($user_match[1] ?? '');
        $password = trim($pass_match[1] ?? '');
        
        try {
            $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p>✅ データベース接続成功</p>";
            
            // 3. usersテーブルの確認
            echo "<h3>👥 usersテーブル確認</h3>";
            
            // テーブル存在確認
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() == 0) {
                echo "<p>❌ usersテーブルが存在しません</p>";
                
                // usersテーブルを作成
                $create_users = "
                CREATE TABLE users (
                    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    user_id varchar(255) NOT NULL,
                    email varchar(255) NOT NULL,
                    email_verified_at timestamp NULL DEFAULT NULL,
                    password varchar(255) NOT NULL,
                    role varchar(255) NOT NULL DEFAULT 'user',
                    remember_token varchar(100) DEFAULT NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY users_user_id_unique (user_id),
                    UNIQUE KEY users_email_unique (email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ";
                
                $pdo->exec($create_users);
                echo "<p>✅ usersテーブルを作成しました</p>";
            } else {
                echo "<p>✅ usersテーブル存在</p>";
            }
            
            // カラム存在確認
            $stmt = $pdo->query("DESCRIBE users");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $required_columns = ['user_id', 'password', 'role'];
            foreach ($required_columns as $column) {
                if (!in_array($column, $columns)) {
                    echo "<p>⚠️ {$column} カラムが不足しています</p>";
                    
                    // カラムを追加
                    switch ($column) {
                        case 'user_id':
                            $pdo->exec("ALTER TABLE users ADD COLUMN user_id VARCHAR(255) UNIQUE AFTER name");
                            echo "<p>✅ user_id カラムを追加しました</p>";
                            break;
                        case 'role':
                            $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'user' AFTER password");
                            echo "<p>✅ role カラムを追加しました</p>";
                            break;
                    }
                } else {
                    echo "<p>✅ {$column} カラム存在</p>";
                }
            }
            
            // 4. 管理者ユーザーの確認・作成
            echo "<h3>👤 管理者ユーザー確認・修正</h3>";
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = 'admin'");
            $stmt->execute();
            $admin_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 正しいパスワードハッシュを生成
            $correct_password = password_hash('password', PASSWORD_DEFAULT);
            
            if (!$admin_user) {
                // 管理者ユーザーを作成
                $stmt = $pdo->prepare("
                    INSERT INTO users (user_id, password, name, email, role, created_at, updated_at) 
                    VALUES ('admin', ?, '管理者', 'admin@example.com', 'admin', NOW(), NOW())
                ");
                $stmt->execute([$correct_password]);
                echo "<p>✅ 管理者ユーザー(admin)を作成しました</p>";
                echo "<p>パスワード: <strong>password</strong></p>";
            } else {
                // 既存の管理者ユーザーのパスワードを更新
                $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE user_id = 'admin'");
                $stmt->execute([$correct_password]);
                echo "<p>✅ 管理者ユーザー(admin)のパスワードを更新しました</p>";
                echo "<p>パスワード: <strong>password</strong></p>";
            }
            
            // 5. 従業員ユーザーの確認・作成
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = 'employee'");
            $stmt->execute();
            $employee_user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $employee_password = password_hash('employee123', PASSWORD_DEFAULT);
            
            if (!$employee_user) {
                $stmt = $pdo->prepare("
                    INSERT INTO users (user_id, password, name, email, role, created_at, updated_at) 
                    VALUES ('employee', ?, '従業員', 'employee@example.com', 'employee', NOW(), NOW())
                ");
                $stmt->execute([$employee_password]);
                echo "<p>✅ 従業員ユーザー(employee)を作成しました</p>";
                echo "<p>パスワード: <strong>employee123</strong></p>";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE user_id = 'employee'");
                $stmt->execute([$employee_password]);
                echo "<p>✅ 従業員ユーザー(employee)のパスワードを更新しました</p>";
                echo "<p>パスワード: <strong>employee123</strong></p>";
            }
            
            // 6. 全ユーザー一覧表示
            echo "<h3>📋 現在のユーザー一覧</h3>";
            $stmt = $pdo->query("SELECT user_id, name, email, role, created_at FROM users ORDER BY id");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($users) {
                echo "<table border='1' style='border-collapse:collapse; width:100%; margin:10px 0;'>";
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
            
        } catch (PDOException $e) {
            echo "<p>❌ データベースエラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        // 7. .htaccess と public/index.php の確認
        echo "<h3>🔧 404エラー対策 - .htaccess確認</h3>";
        
        if (file_exists('.htaccess')) {
            $htaccess_content = file_get_contents('.htaccess');
            echo "<p>✅ .htaccess存在</p>";
            echo "<details><summary>現在の.htaccess内容</summary><pre>" . htmlspecialchars($htaccess_content) . "</pre></details>";
            
            // 問題のある.htaccessを修正
            if (strpos($htaccess_content, 'RewriteRule ^(.*)$ public/ [L]') !== false) {
                echo "<p>⚠️ .htaccessに問題があります。修正します...</p>";
                
                $new_htaccess = "<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Laravel public directory
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/index.php [L]
</IfModule>";
                
                file_put_contents('.htaccess', $new_htaccess);
                echo "<p>✅ .htaccessを修正しました</p>";
            }
        }
        
        // 8. public/index.php の確認
        if (!is_dir('public')) {
            mkdir('public', 0755, true);
            echo "<p>✅ publicディレクトリを作成しました</p>";
        }
        
        if (!file_exists('public/index.php')) {
            $public_index = "<?php
define('LARAVEL_START', microtime(true));

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    echo 'vendor/autoload.php が見つかりません。composer install を実行してください。';
    exit(1);
}

if (file_exists(__DIR__.'/../bootstrap/app.php')) {
    \$app = require_once __DIR__.'/../bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\\Contracts\\Http\\Kernel::class);
    \$response = \$kernel->handle(
        \$request = Illuminate\\Http\\Request::capture()
    );
    \$response->send();
    \$kernel->terminate(\$request, \$response);
} else {
    echo 'bootstrap/app.php が見つかりません。Laravelプロジェクトの構造を確認してください。';
}
?>";
            
            file_put_contents('public/index.php', $public_index);
            echo "<p>✅ public/index.php を作成しました</p>";
        }
        
        echo "<h2>🎉 修正完了！</h2>";
        echo "<h3>📋 ログイン情報:</h3>";
        echo "<ul>";
        echo "<li><strong>管理者</strong> - ユーザーID: <code>admin</code> / パスワード: <code>password</code></li>";
        echo "<li><strong>従業員</strong> - ユーザーID: <code>employee</code> / パスワード: <code>employee123</code></li>";
        echo "</ul>";
        
        echo "<h3>🔗 テスト用リンク:</h3>";
        echo "<ul>";
        echo "<li><a href='https://koutei.kiryu-factory.com/login' target='_blank'>ログインページ</a></li>";
        echo "<li><a href='https://koutei.kiryu-factory.com/' target='_blank'>メインページ</a></li>";
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    
} else {
    // 現在の状況を診断
    echo "<h2>🔍 現在の問題診断</h2>";
    echo "<p>報告された問題：</p>";
    echo "<ul>";
    echo "<li>❌ <strong>ログインパスワードが違う</strong>: 認証に失敗している</li>";
    echo "<li>❌ <strong>404エラー</strong>: ページが見つからない</li>";
    echo "</ul>";
    
    echo "<h3>🔍 考えられる原因:</h3>";
    echo "<ul>";
    echo "<li><strong>ユーザーテーブル問題</strong>: パスワードハッシュが正しくない</li>";
    echo "<li><strong>データベース構造問題</strong>: user_idカラムやroleカラムの不足</li>";
    echo "<li><strong>.htaccess問題</strong>: URL書き換えルールの設定ミス</li>";
    echo "<li><strong>public/index.php問題</strong>: Laravel起動ファイルの不備</li>";
    echo "</ul>";
    
    echo "<h2>🔧 修正内容</h2>";
    echo "<p>このスクリプトは以下の修正を行います：</p>";
    echo "<ul>";
    echo "<li>✅ データベース接続テスト</li>";
    echo "<li>✅ usersテーブル構造の確認・修正</li>";
    echo "<li>✅ 管理者・従業員ユーザーの作成・パスワード更新</li>";
    echo "<li>✅ .htaccessの確認・修正</li>";
    echo "<li>✅ public/index.phpの確認・作成</li>";
    echo "</ul>";
    
    echo "<div style='margin:20px 0; padding:15px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:5px;'>";
    echo "<h3>⚠️ 注意事項</h3>";
    echo "<p>この修正により、既存のユーザーパスワードが変更される可能性があります。</p>";
    echo "<p>修正後は以下の認証情報を使用してください：</p>";
    echo "<ul>";
    echo "<li>管理者: admin / password</li>";
    echo "<li>従業員: employee / employee123</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<form method='post' style='margin:20px 0;'>";
    echo "<button type='submit' name='fix_login' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🚨 ログイン問題を修正する</button>";
    echo "</form>";
}
?> 