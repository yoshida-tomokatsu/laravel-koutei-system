/**
 * 工程管理システム - UI・UXユーティリティ機能
 * Order Management System - UI Utilities
 */

/**
 * タブ切り替え
 * @param {string} tab - タブ名
 */
function changeTab(tab) {
    // タブボタンのアクティブ状態を更新
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active');
        if (button.getAttribute('data-tab') === tab) {
            button.classList.add('active');
        }
    });
    
    // URLパラメータを更新
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    url.searchParams.set('page', '1'); // ページを1にリセット
    window.history.pushState({}, '', url);
    
    // ページをリロード（サーバーサイドフィルタリングのため）
    window.location.reload();
}

/**
 * ページ数変更
 * @param {number} perPage - ページあたりの表示件数
 */
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', '1'); // ページを1にリセット
    window.history.pushState({}, '', url);
    window.location.reload();
}

/**
 * トースト通知を表示
 * @param {string} message - メッセージ
 * @param {string} type - タイプ（success, error, info, warning）
 */
function showToast(message, type = 'info') {
    // 既存のトーストを削除
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // トースト要素を作成
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-icon">${getToastIcon(type)}</span>
            <span class="toast-message">${message}</span>
        </div>
    `;
    
    // スタイルを設定
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        max-width: 500px;
        padding: 16px 20px;
        border-radius: 8px;
        color: white;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease-out;
        transition: all 0.3s ease;
        background: ${getToastColor(type)};
    `;
    
    // 本文に追加
    document.body.appendChild(toast);
    
    // 自動削除（3秒後）
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
    
    // クリックで削除
    toast.addEventListener('click', () => {
        toast.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    });
}

/**
 * トーストアイコンを取得
 * @param {string} type - タイプ
 * @returns {string} アイコン
 */
function getToastIcon(type) {
    switch (type) {
        case 'success': return '✅';
        case 'error': return '❌';
        case 'warning': return '⚠️';
        case 'info': 
        default: return 'ℹ️';
    }
}

/**
 * トースト背景色を取得
 * @param {string} type - タイプ
 * @returns {string} 背景色
 */
function getToastColor(type) {
    switch (type) {
        case 'success': return '#2ecc71';
        case 'error': return '#e74c3c';
        case 'warning': return '#f39c12';
        case 'info': 
        default: return '#3498db';
    }
}

/**
 * 新規注文モーダルを表示
 */
function showNewOrderModal() {
    const modal = document.getElementById('newOrderModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

/**
 * 新規注文モーダルを閉じる
 */
function closeNewOrderModal() {
    const modal = document.getElementById('newOrderModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

/**
 * 見積もりPDFを直接表示
 * @param {string} orderId - 注文ID
 */
function viewQuotePdfDirect(orderId) {
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    const pdfUrl = `/pdf/${paddedId}/quote.pdf`;
    
    // 新しいタブでPDFを開く
    const newWindow = window.open(pdfUrl, '_blank');
    if (!newWindow) {
        showToast('ポップアップがブロックされました。設定を確認してください。', 'warning');
    }
}

/**
 * 見積もりPDFを管理
 * @param {string} orderId - 注文ID
 */
function manageQuotePdf(orderId) {
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    
    // 見積もりPDF管理ページに遷移
    window.location.href = `/pdf/${paddedId}/manage`;
}

/**
 * 注文画像を表示
 * @param {string} orderId - 注文ID
 */
function viewOrderImages(orderId) {
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    
    // 画像表示ページに遷移
    window.location.href = `/images/${paddedId}`;
}

/**
 * 画像ファイル選択
 * @param {string} orderId - 注文ID
 */
function selectImageFiles(orderId) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.multiple = true;
    
    input.onchange = function(event) {
        const files = event.target.files;
        if (files.length === 0) return;
        
        showToast(`${files.length}個のファイルが選択されました`, 'info');
        // 実際のアップロード処理はここに実装
    };
    
    input.click();
}

/**
 * 製品カテゴリを更新
 * @param {string} orderId - 注文ID
 * @param {string} category - カテゴリ
 */
function updateProductCategory(orderId, category) {
    saveOrderField(orderId, 'category', category)
        .then(result => {
            const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
            if (orderRow) {
                updateFieldDisplay(orderRow, 'category', category, result.responseData);
            }
            showToast('カテゴリを更新しました', 'success');
        })
        .catch(error => {
            console.error('Category update error:', error);
            showToast('カテゴリの更新に失敗しました', 'error');
        });
}

/**
 * モーダル外クリックで閉じる処理
 */
function setupModalClickHandlers() {
    // PDFモーダル
    const pdfModal = document.getElementById('pdfModal');
    if (pdfModal) {
        pdfModal.addEventListener('click', function(e) {
            if (e.target === pdfModal) {
                closePdfModal();
            }
        });
    }
    
    // 新規注文モーダル
    const newOrderModal = document.getElementById('newOrderModal');
    if (newOrderModal) {
        newOrderModal.addEventListener('click', function(e) {
            if (e.target === newOrderModal) {
                closeNewOrderModal();
            }
        });
    }
}

/**
 * キーボードショートカットを設定
 */
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl+N で新規注文モーダル
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            showNewOrderModal();
        }
        
        // Ctrl+R でページリロード（デフォルト動作を維持）
        // ESCキーは各モーダルで個別に処理
    });
}

/**
 * ローディング表示を開始
 * @param {string} message - ローディングメッセージ
 */
function showLoading(message = '読み込み中...') {
    const loading = document.createElement('div');
    loading.id = 'globalLoading';
    loading.innerHTML = `
        <div style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
            font-size: 16px;
        ">
            <div style="
                background: white;
                color: #333;
                padding: 30px 40px;
                border-radius: 8px;
                text-align: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            ">
                <div style="
                    width: 40px;
                    height: 40px;
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #3498db;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 20px;
                "></div>
                <div>${message}</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(loading);
}

/**
 * ローディング表示を終了
 */
function hideLoading() {
    const loading = document.getElementById('globalLoading');
    if (loading) {
        loading.remove();
    }
}

/**
 * ページ初期化時の設定
 */
function initializePage() {
    // モーダルクリックハンドラーを設定
    setupModalClickHandlers();
    
    // キーボードショートカットを設定
    setupKeyboardShortcuts();
    
    // スタイルシートを追加（アニメーション用）
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .toast-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .toast:hover {
            transform: translateX(-5px);
            cursor: pointer;
        }
    `;
    document.head.appendChild(style);
}

// DOMコンテンツ読み込み完了時に初期化
document.addEventListener('DOMContentLoaded', initializePage);

// グローバル関数として公開
window.changeTab = changeTab;
window.changePerPage = changePerPage;
window.showToast = showToast;
window.showNewOrderModal = showNewOrderModal;
window.closeNewOrderModal = closeNewOrderModal;
window.viewQuotePdfDirect = viewQuotePdfDirect;
window.manageQuotePdf = manageQuotePdf;
window.viewOrderImages = viewOrderImages;
window.selectImageFiles = selectImageFiles;
window.updateProductCategory = updateProductCategory;
window.showLoading = showLoading;
window.hideLoading = hideLoading; 