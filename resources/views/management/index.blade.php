@extends('layouts.app')

@section('title', 'マスタ管理 - 工程管理システム')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>マスタ管理</h3>
                </div>
                <div class="card-body">
                    
                    <!-- タブナビゲーション -->
                    <ul class="nav nav-tabs" id="managementTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="order-handlers-tab" data-bs-toggle="tab" data-bs-target="#order-handlers" type="button" role="tab">注文担当者</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payment-methods-tab" data-bs-toggle="tab" data-bs-target="#payment-methods" type="button" role="tab">支払方法</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="print-factories-tab" data-bs-toggle="tab" data-bs-target="#print-factories" type="button" role="tab">プリント工場</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sewing-factories-tab" data-bs-toggle="tab" data-bs-target="#sewing-factories" type="button" role="tab">縫製工場</button>
                        </li>
                    </ul>
                    
                    <!-- タブコンテンツ -->
                    <div class="tab-content" id="managementTabsContent">
                        
                        <!-- 注文担当者タブ -->
                        <div class="tab-pane fade show active" id="order-handlers" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h5>注文担当者一覧</h5>
                                    <button class="btn btn-primary mb-2" onclick="showAddOrderHandlerModal()">+ 新規追加</button>
                                    <div id="orderHandlersList" class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>名前</th>
                                                    <th>メールアドレス</th>
                                                    <th>電話番号</th>
                                                    <th>状態</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody id="orderHandlersTableBody">
                                                <!-- データは JavaScript で読み込み -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 支払方法タブ -->
                        <div class="tab-pane fade" id="payment-methods" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <h5>支払方法一覧</h5>
                                    <button class="btn btn-primary mb-2" onclick="showAddPaymentMethodModal()">+ 新規追加</button>
                                    <div id="paymentMethodsList" class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>名前</th>
                                                    <th>説明</th>
                                                    <th>状態</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody id="paymentMethodsTableBody">
                                                <!-- データは JavaScript で読み込み -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- プリント工場タブ -->
                        <div class="tab-pane fade" id="print-factories" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <h5>プリント工場一覧</h5>
                                    <button class="btn btn-primary mb-2" onclick="showAddPrintFactoryModal()">+ 新規追加</button>
                                    <div id="printFactoriesList" class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>名前</th>
                                                    <th>住所</th>
                                                    <th>電話番号</th>
                                                    <th>メールアドレス</th>
                                                    <th>状態</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody id="printFactoriesTableBody">
                                                <!-- データは JavaScript で読み込み -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 縫製工場タブ -->
                        <div class="tab-pane fade" id="sewing-factories" role="tabpanel">
                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <h5>縫製工場一覧</h5>
                                    <button class="btn btn-primary mb-2" onclick="showAddSewingFactoryModal()">+ 新規追加</button>
                                    <div id="sewingFactoriesList" class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>名前</th>
                                                    <th>住所</th>
                                                    <th>電話番号</th>
                                                    <th>メールアドレス</th>
                                                    <th>状態</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sewingFactoriesTableBody">
                                                <!-- データは JavaScript で読み込み -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 注文担当者追加・編集モーダル -->
<div class="modal fade" id="orderHandlerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderHandlerModalTitle">注文担当者追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="orderHandlerForm">
                    <div class="mb-3">
                        <label for="orderHandlerName" class="form-label">名前 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="orderHandlerName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="orderHandlerEmail" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="orderHandlerEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="orderHandlerPhone" class="form-label">電話番号</label>
                        <input type="tel" class="form-control" id="orderHandlerPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="orderHandlerActive" name="is_active" checked>
                            <label class="form-check-label" for="orderHandlerActive">
                                有効
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" onclick="saveOrderHandler()">保存</button>
            </div>
        </div>
    </div>
</div>

<!-- 支払方法追加・編集モーダル -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentMethodModalTitle">支払方法追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentMethodForm">
                    <div class="mb-3">
                        <label for="paymentMethodName" class="form-label">名前 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="paymentMethodName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethodDescription" class="form-label">説明</label>
                        <textarea class="form-control" id="paymentMethodDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="paymentMethodActive" name="is_active" checked>
                            <label class="form-check-label" for="paymentMethodActive">
                                有効
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" onclick="savePaymentMethod()">保存</button>
            </div>
        </div>
    </div>
</div>

<!-- プリント工場追加・編集モーダル -->
<div class="modal fade" id="printFactoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printFactoryModalTitle">プリント工場追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="printFactoryForm">
                    <div class="mb-3">
                        <label for="printFactoryName" class="form-label">名前 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="printFactoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="printFactoryAddress" class="form-label">住所</label>
                        <textarea class="form-control" id="printFactoryAddress" name="address" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="printFactoryPhone" class="form-label">電話番号</label>
                        <input type="tel" class="form-control" id="printFactoryPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="printFactoryEmail" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="printFactoryEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="printFactoryActive" name="is_active" checked>
                            <label class="form-check-label" for="printFactoryActive">
                                有効
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" onclick="savePrintFactory()">保存</button>
            </div>
        </div>
    </div>
</div>

<!-- 縫製工場追加・編集モーダル -->
<div class="modal fade" id="sewingFactoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sewingFactoryModalTitle">縫製工場追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sewingFactoryForm">
                    <div class="mb-3">
                        <label for="sewingFactoryName" class="form-label">名前 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sewingFactoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="sewingFactoryAddress" class="form-label">住所</label>
                        <textarea class="form-control" id="sewingFactoryAddress" name="address" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="sewingFactoryPhone" class="form-label">電話番号</label>
                        <input type="tel" class="form-control" id="sewingFactoryPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="sewingFactoryEmail" class="form-label">メールアドレス</label>
                        <input type="email" class="form-control" id="sewingFactoryEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sewingFactoryActive" name="is_active" checked>
                            <label class="form-check-label" for="sewingFactoryActive">
                                有効
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" onclick="saveSewingFactory()">保存</button>
            </div>
        </div>
    </div>
</div>

<script>
// CSRFトークンの設定
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ページ読み込み時にデータを取得
document.addEventListener('DOMContentLoaded', function() {
    loadOrderHandlers();
    loadPaymentMethods();
    loadPrintFactories();
    loadSewingFactories();
});

// 注文担当者関連の関数
function loadOrderHandlers() {
    fetch('/management/api/order-handlers')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('orderHandlersTableBody');
            tbody.innerHTML = '';
            data.forEach(handler => {
                const row = createOrderHandlerRow(handler);
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading order handlers:', error));
}

function createOrderHandlerRow(handler) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${handler.name}</td>
        <td>${handler.email || '-'}</td>
        <td>${handler.phone || '-'}</td>
        <td><span class="badge ${handler.is_active ? 'bg-success' : 'bg-secondary'}">${handler.is_active ? '有効' : '無効'}</span></td>
        <td>
            <button class="btn btn-sm btn-outline-primary" onclick="editOrderHandler(${handler.id})">編集</button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteOrderHandler(${handler.id})">削除</button>
        </td>
    `;
    return row;
}

function showAddOrderHandlerModal() {
    document.getElementById('orderHandlerModalTitle').textContent = '注文担当者追加';
    document.getElementById('orderHandlerForm').reset();
    document.getElementById('orderHandlerForm').removeAttribute('data-id');
    new bootstrap.Modal(document.getElementById('orderHandlerModal')).show();
}

function editOrderHandler(id) {
    fetch(`/management/api/order-handlers/${id}`)
        .then(response => response.json())
        .then(handler => {
            document.getElementById('orderHandlerModalTitle').textContent = '注文担当者編集';
            document.getElementById('orderHandlerName').value = handler.name;
            document.getElementById('orderHandlerEmail').value = handler.email || '';
            document.getElementById('orderHandlerPhone').value = handler.phone || '';
            document.getElementById('orderHandlerActive').checked = handler.is_active;
            document.getElementById('orderHandlerForm').setAttribute('data-id', handler.id);
            new bootstrap.Modal(document.getElementById('orderHandlerModal')).show();
        })
        .catch(error => console.error('Error loading order handler:', error));
}

function saveOrderHandler() {
    const form = document.getElementById('orderHandlerForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.is_active = document.getElementById('orderHandlerActive').checked;
    
    const id = form.getAttribute('data-id');
    const url = id ? `/management/api/order-handlers/${id}` : '/management/api/order-handlers';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('orderHandlerModal')).hide();
            loadOrderHandlers();
            showToast('注文担当者を保存しました', 'success');
        } else {
            showToast('保存に失敗しました: ' + result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving order handler:', error);
        showToast('保存に失敗しました', 'error');
    });
}

function deleteOrderHandler(id) {
    if (confirm('この注文担当者を削除してもよろしいですか？')) {
        fetch(`/management/api/order-handlers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadOrderHandlers();
                showToast('注文担当者を削除しました', 'success');
            } else {
                showToast('削除に失敗しました: ' + result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting order handler:', error);
            showToast('削除に失敗しました', 'error');
        });
    }
}

// 支払方法関連の関数
function loadPaymentMethods() {
    fetch('/management/api/payment-methods')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('paymentMethodsTableBody');
            tbody.innerHTML = '';
            data.forEach(method => {
                const row = createPaymentMethodRow(method);
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading payment methods:', error));
}

function createPaymentMethodRow(method) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${method.name}</td>
        <td>${method.description || '-'}</td>
        <td><span class="badge ${method.is_active ? 'bg-success' : 'bg-secondary'}">${method.is_active ? '有効' : '無効'}</span></td>
        <td>
            <button class="btn btn-sm btn-outline-primary" onclick="editPaymentMethod(${method.id})">編集</button>
            <button class="btn btn-sm btn-outline-danger" onclick="deletePaymentMethod(${method.id})">削除</button>
        </td>
    `;
    return row;
}

function showAddPaymentMethodModal() {
    document.getElementById('paymentMethodModalTitle').textContent = '支払方法追加';
    document.getElementById('paymentMethodForm').reset();
    document.getElementById('paymentMethodForm').removeAttribute('data-id');
    new bootstrap.Modal(document.getElementById('paymentMethodModal')).show();
}

function editPaymentMethod(id) {
    fetch(`/management/api/payment-methods/${id}`)
        .then(response => response.json())
        .then(method => {
            document.getElementById('paymentMethodModalTitle').textContent = '支払方法編集';
            document.getElementById('paymentMethodName').value = method.name;
            document.getElementById('paymentMethodDescription').value = method.description || '';
            document.getElementById('paymentMethodActive').checked = method.is_active;
            document.getElementById('paymentMethodForm').setAttribute('data-id', method.id);
            new bootstrap.Modal(document.getElementById('paymentMethodModal')).show();
        })
        .catch(error => console.error('Error loading payment method:', error));
}

function savePaymentMethod() {
    const form = document.getElementById('paymentMethodForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.is_active = document.getElementById('paymentMethodActive').checked;
    
    const id = form.getAttribute('data-id');
    const url = id ? `/management/api/payment-methods/${id}` : '/management/api/payment-methods';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentMethodModal')).hide();
            loadPaymentMethods();
            showToast('支払方法を保存しました', 'success');
        } else {
            showToast('保存に失敗しました: ' + result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving payment method:', error);
        showToast('保存に失敗しました', 'error');
    });
}

function deletePaymentMethod(id) {
    if (confirm('この支払方法を削除してもよろしいですか？')) {
        fetch(`/management/api/payment-methods/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadPaymentMethods();
                showToast('支払方法を削除しました', 'success');
            } else {
                showToast('削除に失敗しました: ' + result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting payment method:', error);
            showToast('削除に失敗しました', 'error');
        });
    }
}

// プリント工場関連の関数
function loadPrintFactories() {
    fetch('/management/api/print-factories')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('printFactoriesTableBody');
            tbody.innerHTML = '';
            data.forEach(factory => {
                const row = createPrintFactoryRow(factory);
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading print factories:', error));
}

function createPrintFactoryRow(factory) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${factory.name}</td>
        <td>${factory.address || '-'}</td>
        <td>${factory.phone || '-'}</td>
        <td>${factory.email || '-'}</td>
        <td><span class="badge ${factory.is_active ? 'bg-success' : 'bg-secondary'}">${factory.is_active ? '有効' : '無効'}</span></td>
        <td>
            <button class="btn btn-sm btn-outline-primary" onclick="editPrintFactory(${factory.id})">編集</button>
            <button class="btn btn-sm btn-outline-danger" onclick="deletePrintFactory(${factory.id})">削除</button>
        </td>
    `;
    return row;
}

function showAddPrintFactoryModal() {
    document.getElementById('printFactoryModalTitle').textContent = 'プリント工場追加';
    document.getElementById('printFactoryForm').reset();
    document.getElementById('printFactoryForm').removeAttribute('data-id');
    new bootstrap.Modal(document.getElementById('printFactoryModal')).show();
}

function editPrintFactory(id) {
    fetch(`/management/api/print-factories/${id}`)
        .then(response => response.json())
        .then(factory => {
            document.getElementById('printFactoryModalTitle').textContent = 'プリント工場編集';
            document.getElementById('printFactoryName').value = factory.name;
            document.getElementById('printFactoryAddress').value = factory.address || '';
            document.getElementById('printFactoryPhone').value = factory.phone || '';
            document.getElementById('printFactoryEmail').value = factory.email || '';
            document.getElementById('printFactoryActive').checked = factory.is_active;
            document.getElementById('printFactoryForm').setAttribute('data-id', factory.id);
            new bootstrap.Modal(document.getElementById('printFactoryModal')).show();
        })
        .catch(error => console.error('Error loading print factory:', error));
}

function savePrintFactory() {
    const form = document.getElementById('printFactoryForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.is_active = document.getElementById('printFactoryActive').checked;
    
    const id = form.getAttribute('data-id');
    const url = id ? `/management/api/print-factories/${id}` : '/management/api/print-factories';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('printFactoryModal')).hide();
            loadPrintFactories();
            showToast('プリント工場を保存しました', 'success');
        } else {
            showToast('保存に失敗しました: ' + result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving print factory:', error);
        showToast('保存に失敗しました', 'error');
    });
}

function deletePrintFactory(id) {
    if (confirm('このプリント工場を削除してもよろしいですか？')) {
        fetch(`/management/api/print-factories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadPrintFactories();
                showToast('プリント工場を削除しました', 'success');
            } else {
                showToast('削除に失敗しました: ' + result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting print factory:', error);
            showToast('削除に失敗しました', 'error');
        });
    }
}

// 縫製工場関連の関数
function loadSewingFactories() {
    fetch('/management/api/sewing-factories')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('sewingFactoriesTableBody');
            tbody.innerHTML = '';
            data.forEach(factory => {
                const row = createSewingFactoryRow(factory);
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading sewing factories:', error));
}

function createSewingFactoryRow(factory) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${factory.name}</td>
        <td>${factory.address || '-'}</td>
        <td>${factory.phone || '-'}</td>
        <td>${factory.email || '-'}</td>
        <td><span class="badge ${factory.is_active ? 'bg-success' : 'bg-secondary'}">${factory.is_active ? '有効' : '無効'}</span></td>
        <td>
            <button class="btn btn-sm btn-outline-primary" onclick="editSewingFactory(${factory.id})">編集</button>
            <button class="btn btn-sm btn-outline-danger" onclick="deleteSewingFactory(${factory.id})">削除</button>
        </td>
    `;
    return row;
}

function showAddSewingFactoryModal() {
    document.getElementById('sewingFactoryModalTitle').textContent = '縫製工場追加';
    document.getElementById('sewingFactoryForm').reset();
    document.getElementById('sewingFactoryForm').removeAttribute('data-id');
    new bootstrap.Modal(document.getElementById('sewingFactoryModal')).show();
}

function editSewingFactory(id) {
    fetch(`/management/api/sewing-factories/${id}`)
        .then(response => response.json())
        .then(factory => {
            document.getElementById('sewingFactoryModalTitle').textContent = '縫製工場編集';
            document.getElementById('sewingFactoryName').value = factory.name;
            document.getElementById('sewingFactoryAddress').value = factory.address || '';
            document.getElementById('sewingFactoryPhone').value = factory.phone || '';
            document.getElementById('sewingFactoryEmail').value = factory.email || '';
            document.getElementById('sewingFactoryActive').checked = factory.is_active;
            document.getElementById('sewingFactoryForm').setAttribute('data-id', factory.id);
            new bootstrap.Modal(document.getElementById('sewingFactoryModal')).show();
        })
        .catch(error => console.error('Error loading sewing factory:', error));
}

function saveSewingFactory() {
    const form = document.getElementById('sewingFactoryForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.is_active = document.getElementById('sewingFactoryActive').checked;
    
    const id = form.getAttribute('data-id');
    const url = id ? `/management/api/sewing-factories/${id}` : '/management/api/sewing-factories';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('sewingFactoryModal')).hide();
            loadSewingFactories();
            showToast('縫製工場を保存しました', 'success');
        } else {
            showToast('保存に失敗しました: ' + result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving sewing factory:', error);
        showToast('保存に失敗しました', 'error');
    });
}

function deleteSewingFactory(id) {
    if (confirm('この縫製工場を削除してもよろしいですか？')) {
        fetch(`/management/api/sewing-factories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadSewingFactories();
                showToast('縫製工場を削除しました', 'success');
            } else {
                showToast('削除に失敗しました: ' + result.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting sewing factory:', error);
            showToast('削除に失敗しました', 'error');
        });
    }
}

// トースト表示関数
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}
</script>
@endsection 