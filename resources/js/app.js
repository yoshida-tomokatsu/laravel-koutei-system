import './bootstrap';

import Alpine from 'alpinejs';

// 工程管理システムJavaScript機能をインポート
import './order-management';
import './pdf-management';
import './ui-utilities';

window.Alpine = Alpine;

Alpine.start();

console.log('工程管理システム JavaScript 読み込み完了');
