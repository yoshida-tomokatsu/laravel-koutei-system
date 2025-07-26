<?php
// 404エラー最終修正スクリプト
// .htaccessとpublic/index.phpを正しく設定

echo "<h1>🔧 404エラー最終修正</h1>";

if (isset($_POST['fix'])) {
    echo "<h2>修正実行中...</h2>";
    
    // 1. 正しい.htaccessを作成
    $htaccess = "<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Laravel用設定
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/index.php [L]
</IfModule>";
    
    file_put_contents('.htaccess', $htaccess);
    echo "<p>✅ .htaccess修正完了</p>";
    
    // 2. publicディレクトリ確認
    if (!is_dir('public')) {
        mkdir('public', 0755);
        echo "<p>✅ publicディレクトリ作成</p>";
    }
    
    // 3. 正しいpublic/index.phpを作成
    $public_index = "<?php
define('LARAVEL_START', microtime(true));

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
} else {
    die('Composer autoload not found.');
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
    die('Laravel bootstrap not found.');
}
?>";
    
    file_put_contents('public/index.php', $public_index);
    echo "<p>✅ public/index.php作成完了</p>";
    
    echo "<h2>🎉 修正完了！</h2>";
    echo "<p><a href='https://koutei.kiryu-factory.com/' target='_blank'>サイトをテスト</a></p>";
    
    // 修正後、このファイルを削除
    echo "<script>setTimeout(function(){window.location.href='https://koutei.kiryu-factory.com/';}, 3000);</script>";
    
} else {
    echo "<h2>404エラーの原因</h2>";
    echo "<p>.htaccessがpublic/index.phpを正しく指していません</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='fix' style='background:#dc3545;color:white;padding:15px 30px;border:none;border-radius:5px;font-size:16px;'>🚨 今すぐ修正</button>";
    echo "</form>";
}
?> 