<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderHandler;
use App\Models\PaymentMethod;
use App\Models\PrintFactory;
use App\Models\SewingFactory;

class ManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 注文担当者の初期データ
        $handlers = [
            ['name' => '田中太郎', 'email' => 'tanaka@example.com', 'phone' => '03-1234-5678', 'is_active' => true, 'sort_order' => 1],
            ['name' => '佐藤花子', 'email' => 'sato@example.com', 'phone' => '03-2345-6789', 'is_active' => true, 'sort_order' => 2],
            ['name' => '鈴木次郎', 'email' => 'suzuki@example.com', 'phone' => '03-3456-7890', 'is_active' => true, 'sort_order' => 3],
        ];

        foreach ($handlers as $handler) {
            OrderHandler::create($handler);
        }

        // 支払方法の初期データ
        $paymentMethods = [
            ['name' => 'クレジットカード', 'description' => 'VISA、MasterCard、JCB対応', 'is_active' => true, 'sort_order' => 1],
            ['name' => '銀行振込', 'description' => '三菱UFJ銀行、みずほ銀行対応', 'is_active' => true, 'sort_order' => 2],
            ['name' => '代金引換', 'description' => 'ヤマト運輸、佐川急便対応', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'コンビニ決済', 'description' => 'セブンイレブン、ローソン、ファミリーマート対応', 'is_active' => true, 'sort_order' => 4],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }

        // プリント工場の初期データ
        $printFactories = [
            [
                'name' => '東京プリント工場',
                'address' => '東京都台東区浅草1-1-1',
                'phone' => '03-1111-2222',
                'email' => 'tokyo@print.com',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => '大阪印刷センター',
                'address' => '大阪府大阪市中央区本町2-2-2',
                'phone' => '06-3333-4444',
                'email' => 'osaka@print.com',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => '名古屋プリント株式会社',
                'address' => '愛知県名古屋市中区栄3-3-3',
                'phone' => '052-5555-6666',
                'email' => 'nagoya@print.com',
                'is_active' => true,
                'sort_order' => 3
            ],
        ];

        foreach ($printFactories as $factory) {
            PrintFactory::create($factory);
        }

        // 縫製工場の初期データ
        $sewingFactories = [
            [
                'name' => '関東縫製工場',
                'address' => '神奈川県横浜市港北区新横浜1-1-1',
                'phone' => '045-7777-8888',
                'email' => 'kanto@sewing.com',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => '関西縫製センター',
                'address' => '大阪府大阪市住之江区南港北2-2-2',
                'phone' => '06-9999-0000',
                'email' => 'kansai@sewing.com',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => '中部縫製株式会社',
                'address' => '愛知県豊田市トヨタ町1-1',
                'phone' => '0565-1111-2222',
                'email' => 'chubu@sewing.com',
                'is_active' => true,
                'sort_order' => 3
            ],
        ];

        foreach ($sewingFactories as $factory) {
            SewingFactory::create($factory);
        }

        $this->command->info('Management data seeded successfully.');
    }
} 