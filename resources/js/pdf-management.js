/**
 * 工程管理システム - PDF管理機能
 * Order Management System - PDF Functions
 */

// PDF.js動的読み込み
function loadPdfJsLibrary() {
    return new Promise((resolve, reject) => {
        if (window.pdfjsLib && window.pdfjsLib.getDocument) {
            resolve();
            return;
        }
        
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js';
        script.onload = function() {
            if (window.pdfjsLib) {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';
                resolve();
            } else {
                reject(new Error('PDF.js library failed to load'));
            }
        };
        script.onerror = function() {
            const fallbackScript1 = document.createElement('script');
            fallbackScript1.src = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.6.347/build/pdf.min.js';
            fallbackScript1.onload = function() {
                if (window.pdfjsLib) {
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.6.347/build/pdf.worker.min.js';
                    resolve();
                } else {
                    reject(new Error('PDF.js fallback library failed to load'));
                }
            };
            fallbackScript1.onerror = function() {
                const fallbackScript2 = document.createElement('script');
                fallbackScript2.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.min.js';
                fallbackScript2.onload = function() {
                    if (window.pdfjsLib) {
                        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.5.207/pdf.worker.min.js';
                        resolve();
                    } else {
                        reject(new Error('All PDF.js fallback libraries failed to load'));
                    }
                };
                fallbackScript2.onerror = function() {
                    reject(new Error('All PDF.js libraries failed to load'));
                };
                document.head.appendChild(fallbackScript2);
            };
            document.head.appendChild(fallbackScript1);
        };
        document.head.appendChild(script);
    });
}

/**
 * PDFモーダルを表示
 * @param {string} pdfUrl - PDF URL
 * @param {string} orderId - 注文ID
 */
function showPdfModal(pdfUrl, orderId) {
    const modal = document.getElementById('pdfModal');
    if (!modal) return;
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    loadPdfInModal(pdfUrl, orderId);
    loadPdfPagesList(orderId);
    
    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', handleEscKey);
}

/**
 * PDFモーダル内にPDFを読み込み
 * @param {string} pdfUrl - PDF URL
 * @param {string} orderId - 注文ID
 */
function loadPdfInModal(pdfUrl, orderId) {
    const pdfContainer = document.getElementById('pdfContainer');
    if (!pdfContainer) return;
    
    pdfContainer.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100%; color: #666;">
            <div>PDFを読み込み中...</div>
        </div>
    `;
    
    const iframe = document.createElement('iframe');
    iframe.src = pdfUrl;
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    
    iframe.onload = function() {
        pdfContainer.innerHTML = '';
        pdfContainer.appendChild(iframe);
    };
    
    iframe.onerror = function() {
        showPdfError('PDF読み込みエラー', 'PDFファイルの読み込みに失敗しました。ファイルが存在しないか、破損している可能性があります。');
    };
}

/**
 * PDFファイルを追加
 * @param {string} orderId - 注文ID
 */
function addPdfToModal(orderId) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf';
    input.multiple = true;
    
    input.onchange = function(event) {
        const files = event.target.files;
        if (files.length === 0) return;
        
        for (let i = 0; i < files.length; i++) {
            uploadPdfFileToModal(orderId, files[i]);
        }
    };
    
    input.click();
}

/**
 * PDFファイルをアップロード
 * @param {string} orderId - 注文ID
 * @param {File} file - ファイル
 */
function uploadPdfFileToModal(orderId, file) {
    if (!file.type.includes('pdf')) {
        showToast('PDFファイルのみアップロード可能です', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('pdf_file', file);
    
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    
    fetch(`/pdf/${paddedId}/upload`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            loadPdfPagesList(orderId);
            updatePdfInfo(orderId, data.files);
        } else {
            showToast(`アップロードに失敗しました: ${data.message || '不明なエラー'}`, 'error');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showToast('アップロードに失敗しました', 'error');
    });
}

/**
 * PDFページリストを読み込み
 * @param {string} orderId - 注文ID
 */
function loadPdfPagesList(orderId) {
    const pdfPagesList = document.getElementById('pdfPagesList');
    if (!pdfPagesList) return;
    
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    
    fetch(`/pdf/${paddedId}/list`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.files && data.files.length > 0) {
                const filesHtml = data.files.map(file => `
                    <div class="pdf-file-item" data-filename="${file.filename}">
                        <div class="pdf-file-info">
                            <span class="pdf-filename">${file.filename}</span>
                            <span class="pdf-filesize">(${(file.size / 1024).toFixed(1)}KB)</span>
                        </div>
                        <div class="pdf-file-actions">
                            <button onclick="viewPdfInModal('${orderId}', '${file.filename}')" class="btn-view">表示</button>
                            <button onclick="deletePdfFileFromModal('${orderId}', '${file.filename}')" class="btn-delete">削除</button>
                        </div>
                    </div>
                `).join('');
                
                pdfPagesList.innerHTML = filesHtml;
                
                // ドラッグ&ドロップソート機能
                if (window.Sortable && data.files.length > 1) {
                    if (window.currentSortable && typeof window.currentSortable.destroy === 'function') {
                        window.currentSortable.destroy();
                    }
                    
                    window.currentSortable = Sortable.create(pdfPagesList, {
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        onEnd: function(evt) {
                            const pages = [];
                            const items = pdfPagesList.querySelectorAll('.pdf-file-item');
                            items.forEach((item, index) => {
                                pages.push({
                                    filename: item.getAttribute('data-filename'),
                                    order: index + 1
                                });
                            });
                            
                            fetch(`/pdf/${paddedId}/reorder`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ pages: pages })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showToast('ページ順序を保存しました', 'success');
                                } else {
                                    showToast('ページ順序の保存に失敗しました', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Reorder error:', error);
                                showToast('ページ順序の保存に失敗しました', 'error');
                            });
                        }
                    });
                }
            } else {
                pdfPagesList.innerHTML = '<div class="no-pdf-message">PDFファイルがありません</div>';
            }
        })
        .catch(error => {
            console.error('Load PDF list error:', error);
            pdfPagesList.innerHTML = '<div class="error-message">PDFリストの読み込みに失敗しました</div>';
        });
}

/**
 * モーダル内でPDFを表示
 * @param {string} orderId - 注文ID
 * @param {string} filename - ファイル名
 */
function viewPdfInModal(orderId, filename) {
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    const pdfUrl = `/pdf/${paddedId}/${filename}`;
    
    loadPdfInModal(pdfUrl, orderId);
}

/**
 * PDFファイルを削除
 * @param {string} orderId - 注文ID
 * @param {string} filename - ファイル名
 */
function deletePdfFileFromModal(orderId, filename) {
    if (!confirm(`PDFファイル「${filename}」を削除しますか？`)) {
        return;
    }
    
    const numericId = orderId.replace('#', '');
    const paddedId = numericId.padStart(5, '0');
    
    fetch(`/pdf/${paddedId}/delete`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            filename: filename
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            loadPdfPagesList(orderId);
            if (data.count === 0) {
                showNoPdfMessage();
            } else {
                refreshPdfInModal(orderId);
            }
            updatePdfInfo(orderId, data.files);
        } else {
            showToast(`削除に失敗しました: ${data.message || '不明なエラー'}`, 'error');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showToast('削除に失敗しました', 'error');
    });
}

/**
 * PDFモーダルを閉じる
 */
function closePdfModal() {
    const modal = document.getElementById('pdfModal');
    if (!modal) return;
    
    if (window.currentSortable && typeof window.currentSortable.destroy === 'function') {
        window.currentSortable.destroy();
        window.currentSortable = null;
    }
    
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    const pdfContainer = document.getElementById('pdfContainer');
    if (pdfContainer) {
        pdfContainer.innerHTML = '';
    }
    
    document.removeEventListener('keydown', handleEscKey);
}

/**
 * PDF情報を更新
 * @param {string} orderId - 注文ID
 * @param {Array} files - ファイル一覧
 */
function updatePdfInfo(orderId, files) {
    // メインテーブルのPDF情報を更新
    const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
    if (orderRow) {
        const pdfCountElement = orderRow.querySelector('.pdf-count');
        if (pdfCountElement) {
            pdfCountElement.textContent = files ? files.length : 0;
        }
    }
}

/**
 * PDFなしメッセージを表示
 */
function showNoPdfMessage() {
    const pdfContainer = document.getElementById('pdfContainer');
    if (!pdfContainer) return;
    
    pdfContainer.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100%; color: #666; flex-direction: column;">
            <div style="font-size: 48px; margin-bottom: 20px;">📄</div>
            <div style="font-size: 18px; margin-bottom: 10px;">PDFファイルがありません</div>
            <div style="font-size: 14px; color: #999;">右側からPDFファイルをアップロードしてください</div>
        </div>
    `;
}

/**
 * PDFモーダル内容を更新
 * @param {string} orderId - 注文ID
 */
function refreshPdfInModal(orderId) {
    loadPdfPagesList(orderId);
}

/**
 * PDFエラーを表示
 * @param {string} title - エラータイトル
 * @param {string} message - エラーメッセージ
 */
function showPdfError(title, message) {
    const pdfContainer = document.getElementById('pdfContainer');
    if (!pdfContainer) return;
    
    pdfContainer.innerHTML = `
        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            flex-direction: column;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
            width: 90%;
            margin: 0 auto;
        ">
            <div style="color: #e74c3c; font-size: 48px; margin-bottom: 20px;">⚠️</div>
            <h2 style="color: #2c3e50; margin-bottom: 15px; font-size: 24px;">${title}</h2>
            <p style="color: #7f8c8d; margin-bottom: 30px; font-size: 16px; line-height: 1.5;">${message}</p>
            <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px; border-left: 4px solid #e74c3c;">
                <p style="color: #2c3e50; margin: 0; font-size: 14px;">
                    <strong>対処方法:</strong><br>
                    • ページを再読み込みしてください<br>
                    • インターネット接続を確認してください<br>
                    • しばらく時間をおいてから再試行してください
                </p>
            </div>
            <button onclick="closePdfModal()" style="
                background: #3498db; color: white; border: none; padding: 12px 24px;
                border-radius: 4px; cursor: pointer; font-size: 16px; margin-right: 10px;
            ">閉じる</button>
            <button onclick="location.reload()" style="
                background: #2ecc71; color: white; border: none; padding: 12px 24px;
                border-radius: 4px; cursor: pointer; font-size: 16px;
            ">ページを再読み込み</button>
        </div>
    `;
}

// ESCキーハンドラー
function handleEscKey(e) {
    if (e.key === 'Escape') {
        closePdfModal();
    }
}

// グローバル関数として公開
window.loadPdfJsLibrary = loadPdfJsLibrary;
window.showPdfModal = showPdfModal;
window.loadPdfInModal = loadPdfInModal;
window.addPdfToModal = addPdfToModal;
window.uploadPdfFileToModal = uploadPdfFileToModal;
window.loadPdfPagesList = loadPdfPagesList;
window.viewPdfInModal = viewPdfInModal;
window.deletePdfFileFromModal = deletePdfFileFromModal;
window.closePdfModal = closePdfModal;
window.updatePdfInfo = updatePdfInfo;
window.showNoPdfMessage = showNoPdfMessage;
window.refreshPdfInModal = refreshPdfInModal;
window.showPdfError = showPdfError;
window.handleEscKey = handleEscKey; 