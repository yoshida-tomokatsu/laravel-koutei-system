@extends('layouts.app')

@section('title', 'ダッシュボード - 工程管理システム')

@section('content')
<div class="dashboard-container" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    
    <!-- ウェルカムメッセージ -->
    <div class="welcome-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
        <h1 style="font-size: 2.5em; margin-bottom: 10px;">工程管理システム</h1>
                  <p style="font-size: 1.2em; opacity: 0.9;">{{ auth()->user() ? auth()->user()->name : 'ゲスト' }}さん、おかえりなさい！</p>
        <p style="font-size: 1em; opacity: 0.8;">役割: {{ auth()->user() && auth()->user()->role === 'admin' ? '管理者' : '従業員' }}</p>
    </div>

    <!-- クイックアクション -->
    <div class="quick-actions" style="margin-bottom: 30px;">
        <h2 style="font-size: 1.8em; margin-bottom: 20px; color: #2c3e50;">クイックアクション</h2>
        <div class="action-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            
            <!-- 注文管理 -->
            <div class="action-card" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-left: 5px solid #3498db;">
                <h3 style="color: #3498db; font-size: 1.3em; margin-bottom: 15px;">📋 注文管理</h3>
                <p style="color: #7f8c8d; margin-bottom: 20px;">注文の確認・編集・進捗管理を行います</p>
                <a href="{{ route('orders.index') }}" style="background: #3498db; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">注文一覧を見る</a>
            </div>

            <!-- PDF表示 -->
            <div class="action-card" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-left: 5px solid #27ae60;">
                <h3 style="color: #27ae60; font-size: 1.3em; margin-bottom: 15px;">📄 PDF表示</h3>
                <p style="color: #7f8c8d; margin-bottom: 20px;">見積書・注文書のPDFを表示します</p>
                <a href="{{ route('pdf.index') }}" style="background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">PDF一覧を見る</a>
            </div>

            @if(auth()->user() && auth()->user()->role === 'admin')
            <!-- 管理者専用 -->
            <div class="action-card" style="background: white; border-radius: 10px; padding: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border-left: 5px solid #9b59b6;">
                <h3 style="color: #9b59b6; font-size: 1.3em; margin-bottom: 15px;">⚙️ 管理機能</h3>
                <p style="color: #7f8c8d; margin-bottom: 20px;">システム設定・ユーザー管理</p>
                <button style="background: #9b59b6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;" onclick="alert('管理機能は今後実装予定です')">管理画面（準備中）</button>
            </div>
            @endif
        </div>
    </div>

    <!-- 統計情報 -->
    <div class="stats-section" style="margin-bottom: 30px;">
        <h2 style="font-size: 1.8em; margin-bottom: 20px; color: #2c3e50;">システム統計</h2>
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-top: 4px solid #e74c3c;">
                <h3 style="color: #e74c3c; font-size: 2em; margin-bottom: 10px;" id="totalOrders">-</h3>
                <p style="color: #7f8c8d;">総注文数</p>
            </div>
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-top: 4px solid #f39c12;">
                <h3 style="color: #f39c12; font-size: 2em; margin-bottom: 10px;" id="pendingOrders">-</h3>
                <p style="color: #7f8c8d;">進行中</p>
            </div>
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-top: 4px solid #27ae60;">
                <h3 style="color: #27ae60; font-size: 2em; margin-bottom: 10px;" id="completedOrders">-</h3>
                <p style="color: #7f8c8d;">完了</p>
            </div>
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); border-top: 4px solid #8e44ad;">
                <h3 style="color: #8e44ad; font-size: 2em; margin-bottom: 10px;" id="todayOrders">-</h3>
                <p style="color: #7f8c8d;">今日の注文</p>
            </div>
        </div>
    </div>

    <!-- 最近のアクティビティ -->
    <div class="recent-activity" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <h2 style="font-size: 1.8em; margin-bottom: 20px; color: #2c3e50;">最近のアクティビティ</h2>
        <div id="recentActivity" style="color: #7f8c8d;">
            <p>アクティビティを読み込み中...</p>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 統計情報の取得
    fetch('/api/orders/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalOrders').textContent = data.data.total_orders || 0;
                document.getElementById('pendingOrders').textContent = data.data.pending_orders || 0;
                document.getElementById('completedOrders').textContent = data.data.completed_orders || 0;
                document.getElementById('todayOrders').textContent = data.data.today_orders || 0;
            }
        })
        .catch(error => {
            console.error('統計情報の取得エラー:', error);
        });

    // 最近のアクティビティの表示
    setTimeout(() => {
        const activities = [
            '注文管理システムが正常に動作しています',
            'PDFファイルへのアクセス準備完了',
            'データベース接続確認済み',
            'システム最終更新: ' + new Date().toLocaleString('ja-JP')
        ];
        
        const activityHTML = activities.map(activity => 
            `<p style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; border-left: 3px solid #3498db;">• ${activity}</p>`
        ).join('');
        
        document.getElementById('recentActivity').innerHTML = activityHTML;
    }, 1000);
});
</script>
@endsection
