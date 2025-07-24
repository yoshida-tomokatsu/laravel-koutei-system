/**
 * 工程管理システム - 注文編集機能
 * Order Management System - Edit Functions
 */

// CSRF設定
document.addEventListener('DOMContentLoaded', function() {
    // CSRFトークンの設定
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        // jQuery AJAX設定
        if (window.$) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
        }
    }
});

// 編集モード管理用のストレージ
let editModeData = {};

/**
 * 編集モードの切り替え
 * @param {string} orderId - 注文ID
 * @param {boolean} isEdit - 編集モードかどうか
 */
function toggleEditMode(orderId, isEdit) {
    const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
    if (!orderRow) {
        console.error('Order row not found for ID:', orderId);
        return;
    }
    
    if (isEdit) {
        startEditMode(orderRow, orderId);
    } else {
        cancelEditMode(orderRow, orderId);
    }
}

/**
 * 編集モード開始
 * @param {HTMLElement} orderRow - 注文行要素
 * @param {string} orderId - 注文ID
 */
function startEditMode(orderRow, orderId) {
    const editableFields = [
        'category', 'customer-name', 'company-name', 'delivery-date', 'publication-permission',
        'order-handler', 'image-sent', 'payment-method', 'payment-completed',
        'print-request', 'print-factory', 'print-deadline',
        'sewing-request', 'sewing-factory', 'sewing-deadline',
        'quality-check', 'shipping-date', 'notes'
    ];
    
    editableFields.forEach(field => {
        const displayElement = orderRow.querySelector(`.${field}-display`);
        const inputElement = orderRow.querySelector(`.${field}-input, .${field}-select, .${field}-textarea, .${field}-dropdown`);
        
        if (displayElement && inputElement) {
            if (!editModeData[orderId]) {
                editModeData[orderId] = {};
            }
            
            let currentValue = displayElement.textContent.trim();
            if (currentValue === '-') {
                currentValue = '';
            }
            
            editModeData[orderId][field] = currentValue;
            
            // 入力要素の値を表示値に同期
            if (inputElement.tagName === 'SELECT') {
                for (let option of inputElement.options) {
                    if (option.text === currentValue || option.value === currentValue) {
                        inputElement.value = option.value;
                        break;
                    }
                }
            } else {
                inputElement.value = currentValue;
            }
            
            displayElement.style.display = 'none';
            inputElement.style.display = 'inline-block';
        }
    });
    
    // ボタンの表示切り替え
    const editButton = orderRow.querySelector('.edit-button');
    const saveButton = orderRow.querySelector('.save-button');
    const cancelButton = orderRow.querySelector('.cancel-button');
    
    if (editButton) editButton.style.display = 'none';
    if (saveButton) saveButton.style.display = 'inline-block';
    if (cancelButton) cancelButton.style.display = 'inline-block';
}

/**
 * 編集モードのキャンセル
 * @param {HTMLElement} orderRow - 注文行要素
 * @param {string} orderId - 注文ID
 */
function cancelEditMode(orderRow, orderId) {
    const editableFields = [
        'category', 'customer-name', 'company-name', 'delivery-date', 'publication-permission',
        'order-handler', 'image-sent', 'payment-method', 'payment-completed',
        'print-request', 'print-factory', 'print-deadline',
        'sewing-request', 'sewing-factory', 'sewing-deadline',
        'quality-check', 'shipping-date', 'notes'
    ];
    
    editableFields.forEach(field => {
        const displayElement = orderRow.querySelector(`.${field}-display`);
        const inputElement = orderRow.querySelector(`.${field}-input, .${field}-select, .${field}-textarea, .${field}-dropdown`);
        
        if (displayElement && inputElement) {
            // 元の値に戻す
            if (editModeData[orderId] && editModeData[orderId][field] !== undefined) {
                inputElement.value = editModeData[orderId][field];
            }
            
            displayElement.style.display = 'inline-block';
            inputElement.style.display = 'none';
        }
    });
    
    // ボタンの表示切り替え
    const editButton = orderRow.querySelector('.edit-button');
    const saveButton = orderRow.querySelector('.save-button');
    const cancelButton = orderRow.querySelector('.cancel-button');
    
    if (editButton) editButton.style.display = 'inline-block';
    if (saveButton) saveButton.style.display = 'none';
    if (cancelButton) cancelButton.style.display = 'none';
    
    // 編集データをクリア
    if (editModeData[orderId]) {
        delete editModeData[orderId];
    }
}

/**
 * 注文情報の保存
 * @param {string} orderId - 注文ID
 */
async function saveOrderInfo(orderId) {
    const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
    if (!orderRow) {
        console.error('Order row not found for ID:', orderId);
        return;
    }
    
    const saveButton = orderRow.querySelector('.save-button');
    if (saveButton) {
        saveButton.disabled = true;
        saveButton.textContent = '保存中...';
    }
    
    const editableFields = [
        'category', 'customer_name', 'company_name', 'delivery_date', 'publication_permission',
        'order_handler', 'image_sent', 'payment_method', 'payment_completed',
        'print_request', 'print_factory', 'print_deadline',
        'sewing_request', 'sewing_factory', 'sewing_deadline',
        'quality_check', 'shipping_date', 'notes'
    ];
    
    const data = {};
    let hasChanges = false;
    
    editableFields.forEach(field => {
        const fieldClass = field.replace('_', '-');
        const inputElement = orderRow.querySelector(`.${fieldClass}-input, .${fieldClass}-select, .${fieldClass}-textarea, .${fieldClass}-dropdown`);
        
        if (inputElement) {
            const originalValue = editModeData[orderId] && editModeData[orderId][fieldClass] ? editModeData[orderId][fieldClass] : '';
            const currentValue = inputElement.value.trim();
            
            if (currentValue !== originalValue) {
                data[field] = currentValue;
                hasChanges = true;
            }
        }
    });
    
    if (!hasChanges) {
        showToast('変更はありませんでした', 'info');
        toggleEditMode(orderId, false);
        return;
    }
    
    try {
        const response = await fetch(`/orders/${orderId}/update-info`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // 表示を更新
            Object.keys(data).forEach(field => {
                updateFieldDisplay(orderRow, field, data[field], result.data || {});
            });
            
            showToast('保存が完了しました', 'success');
            toggleEditMode(orderId, false);
        } else {
            showToast(`保存に失敗しました: ${result.message || '不明なエラー'}`, 'error');
        }
    } catch (error) {
        console.error('Save error:', error);
        showToast('保存に失敗しました', 'error');
    } finally {
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.textContent = '保存';
        }
    }
}

/**
 * フィールド表示の更新
 * @param {HTMLElement} orderRow - 注文行要素
 * @param {string} field - フィールド名
 * @param {string} value - 値
 * @param {Object} responseData - レスポンスデータ
 */
function updateFieldDisplay(orderRow, field, value, responseData) {
    if (field === 'category') {
        const displayElement = orderRow.querySelector('.category-display');
        if (displayElement) {
            displayElement.textContent = value || '-';
            displayElement.className = `category-display category-${value.toLowerCase().replace(' ', '')}`;
        }
    } else if (field === 'customer_name') {
        const displayElement = orderRow.querySelector('.customer-name-display');
        if (displayElement) {
            displayElement.textContent = value || '-';
        }
    } else if (field === 'company_name') {
        const displayElement = orderRow.querySelector('.company-name-display');
        if (displayElement) {
            displayElement.textContent = value || '-';
        }
    } else if (field === 'delivery_date') {
        const displayElement = orderRow.querySelector('.delivery-date-display');
        if (displayElement) {
            displayElement.textContent = value || '-';
        }
    } else if (field === 'publication_permission') {
        const displayElement = orderRow.querySelector('.publication-permission-display');
        if (displayElement) {
            displayElement.textContent = value || '-';
        }
    } else if (field.includes('_date')) {
        const displayElement = orderRow.querySelector(`.${field.replace('_', '-')}-display`);
        if (displayElement) {
            if (value) {
                const date = new Date(value);
                displayElement.textContent = `${date.getMonth() + 1}/${date.getDate()}`;
            } else {
                displayElement.textContent = '-';
            }
        }
    } else {
        const displayElement = orderRow.querySelector(`.${field.replace('_', '-')}-display`);
        if (displayElement) {
            displayElement.textContent = value || '-';
        }
    }
}

/**
 * 個別フィールドの保存
 * @param {string} orderId - 注文ID
 * @param {string} field - フィールド名
 * @param {string} value - 値
 * @returns {Promise}
 */
function saveOrderField(orderId, field, value) {
    return new Promise((resolve, reject) => {
        const data = {};
        data[field] = value;
        
        fetch(`/orders/${orderId}/update-info`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resolve({ 
                    success: true, 
                    field: field, 
                    value: value,
                    responseData: data.data || {} 
                });
            } else {
                reject(new Error(data.message || `${field} の更新に失敗しました`));
            }
        })
        .catch(error => {
            reject(error);
        });
    });
}

/**
 * 自動保存機能
 * @param {HTMLElement} element - 入力要素
 */
function autoSaveField(element) {
    const orderRow = element.closest('[data-order-id]');
    if (!orderRow) return;
    
    const orderId = orderRow.getAttribute('data-order-id');
    const fieldName = element.getAttribute('data-field');
    const value = element.value;
    
    if (!fieldName) return;
    
    // デバウンス処理（500ms後に実行）
    clearTimeout(element.autoSaveTimeout);
    element.autoSaveTimeout = setTimeout(() => {
        saveOrderField(orderId, fieldName, value)
            .then(result => {
                updateFieldDisplay(orderRow, fieldName, value, result.responseData);
                showToast('自動保存しました', 'success');
            })
            .catch(error => {
                console.error('Auto save error:', error);
                showToast('自動保存に失敗しました', 'error');
            });
    }, 500);
}

// グローバル関数として公開
window.toggleEditMode = toggleEditMode;
window.startEditMode = startEditMode;
window.cancelEditMode = cancelEditMode;
window.saveOrderInfo = saveOrderInfo;
window.updateFieldDisplay = updateFieldDisplay;
window.saveOrderField = saveOrderField;
window.autoSaveField = autoSaveField; 