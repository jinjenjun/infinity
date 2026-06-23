<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::role('admin')->value('id');

        $electronics = ProductCategory::firstOrCreate(
            ['slug' => 'electronics'],
            ['name' => '電子產品', 'admin_id' => $adminId]
        );

        $phones = ProductCategory::firstOrCreate(
            ['slug' => 'smartphones'],
            ['name' => '智慧型手機', 'admin_id' => $adminId, 'parent_id' => $electronics->id]
        );

        $laptops = ProductCategory::firstOrCreate(
            ['slug' => 'laptops'],
            ['name' => '筆記型電腦', 'admin_id' => $adminId, 'parent_id' => $electronics->id]
        );

        $wearables = ProductCategory::firstOrCreate(
            ['slug' => 'wearables'],
            ['name' => '智慧穿戴', 'admin_id' => $adminId, 'parent_id' => $electronics->id]
        );

        $phoneProducts = [
            ['name' => 'Apple iPhone 16 Pro Max 256GB', 'price' => 49900],
            ['name' => 'Apple iPhone 16 Pro 128GB', 'price' => 42900],
            ['name' => 'Apple iPhone 16 128GB', 'price' => 32900],
            ['name' => 'Apple iPhone 15 128GB', 'price' => 27900],
            ['name' => 'Samsung Galaxy S25 Ultra 512GB', 'price' => 49900],
            ['name' => 'Samsung Galaxy S25+ 256GB', 'price' => 39900],
            ['name' => 'Samsung Galaxy S25 256GB', 'price' => 32900],
            ['name' => 'Samsung Galaxy A55 5G 256GB', 'price' => 15900],
            ['name' => 'Google Pixel 9 Pro 256GB', 'price' => 43900],
            ['name' => 'Google Pixel 9 128GB', 'price' => 29900],
            ['name' => 'Sony Xperia 1 VI 256GB', 'price' => 47900],
            ['name' => 'Sony Xperia 10 VI 128GB', 'price' => 19900],
            ['name' => 'ASUS ROG Phone 8 Pro 512GB', 'price' => 49000],
            ['name' => 'ASUS Zenfone 11 Ultra 256GB', 'price' => 35900],
            ['name' => 'Xiaomi 14 Ultra 512GB', 'price' => 39900],
            ['name' => 'Xiaomi 14T Pro 256GB', 'price' => 24900],
            ['name' => 'OnePlus 12 256GB', 'price' => 28900],
            ['name' => 'vivo X100 Pro 512GB', 'price' => 38900],
            ['name' => 'OPPO Find X8 Pro 256GB', 'price' => 36900],
            ['name' => 'Motorola Edge 50 Ultra 512GB', 'price' => 22900],
        ];

        $laptopProducts = [
            ['name' => 'Apple MacBook Pro 16吋 M4 Pro 24GB 512GB', 'price' => 79900],
            ['name' => 'Apple MacBook Pro 14吋 M4 Pro 24GB 512GB', 'price' => 69900],
            ['name' => 'Apple MacBook Air 15吋 M3 16GB 512GB', 'price' => 49900],
            ['name' => 'Apple MacBook Air 13吋 M3 16GB 256GB', 'price' => 38900],
            ['name' => 'ASUS ROG Zephyrus G16 RTX4070 32GB 1TB', 'price' => 79900],
            ['name' => 'ASUS VivoBook Pro 16 OLED RTX4060 16GB 512GB', 'price' => 49900],
            ['name' => 'ASUS ZenBook 14 OLED Intel Ultra 7 32GB 1TB', 'price' => 45900],
            ['name' => 'ASUS Laptop 15 OLED AMD Ryzen 7 16GB 512GB', 'price' => 32900],
            ['name' => 'Dell XPS 15 Intel Ultra 9 32GB 1TB', 'price' => 75900],
            ['name' => 'Dell XPS 13 Intel Ultra 7 16GB 512GB', 'price' => 55900],
            ['name' => 'Lenovo ThinkPad X1 Carbon Intel Ultra 7 32GB 1TB', 'price' => 72900],
            ['name' => 'Lenovo IdeaPad Slim 5 AMD Ryzen 7 16GB 512GB', 'price' => 30900],
            ['name' => 'HP Spectre x360 14吋 Intel Ultra 7 32GB 1TB', 'price' => 65900],
            ['name' => 'HP ENVY x360 15吋 AMD Ryzen 7 16GB 512GB', 'price' => 42900],
            ['name' => 'MSI Raider GE78 HX RTX4090 64GB 2TB', 'price' => 79900],
            ['name' => 'MSI Creator Z17 RTX4070 32GB 1TB', 'price' => 69900],
            ['name' => 'Acer Swift Go 16 Intel Ultra 7 32GB 1TB', 'price' => 39900],
            ['name' => 'Acer Predator Helios 18 RTX4080 32GB 1TB', 'price' => 76900],
            ['name' => 'Microsoft Surface Laptop 7 Snapdragon X Elite 32GB 1TB', 'price' => 65900],
            ['name' => 'Razer Blade 15 RTX4070 32GB 1TB', 'price' => 78900],
        ];

        $wearableProducts = [
            [
                'name' => 'Apple Watch Series 10 GPS 46mm',
                'price' => 14900,
                'discount' => 1.00,
                'description' => '全新 Apple Watch Series 10 採用最新 S10 晶片，運算效能與省電表現大幅提升。超薄 9.7mm 機身搭配更寬廣的 Always-On Retina 顯示器，讓健康數據與通知一目了然。內建心電圖（ECG）感測器、血氧偵測及睡眠呼吸暫停警示，全面守護您的身體健康。防水深度達 50 公尺，游泳模式精準追蹤每一圈成績。支援跑步、騎車、瑜珈等超過 60 種運動模式，內建精準雙頻 GPS 不遺漏任何路徑細節。搭配 watchOS 11 全新個人化功能，讓智慧手錶真正融入您的生活節奏，是重視健康與科技品味人士的不二之選。',
            ],
            [
                'name' => 'Xiaomi Smart Band 9 Pro',
                'price' => 1999,
                'discount' => 0.85,
                'description' => '小米手環 9 Pro 搭載 1.74 吋 AMOLED 高清螢幕，色彩鮮豔飽和，戶外陽光下依然清晰可見。內建 150 種運動模式，精準記錄跑步、游泳、騎車等各類運動數據。全天候心率監測與血氧偵測，即時掌握身體狀況。超長 14 天續航力讓您減少充電困擾，防水等級達 5ATM，日常佩戴完全無壓力。支援 NFC 感應支付，出門消費更便利。輕薄舒適的矽膠錶帶設計，久戴不悶熱，搭配多款時尚錶帶可自由替換，兼顧運動與日常穿搭需求，是入門智慧穿戴的超值之選。',
            ],
            [
                'name' => 'Samsung Galaxy Fit3',
                'price' => 2490,
                'discount' => 0.90,
                'description' => 'Samsung Galaxy Fit3 以輕盈纖薄的設計打造極致舒適的全天候穿戴體驗。搭載 1.6 吋 AMOLED 彩色觸控螢幕，亮麗顯示運動紀錄與通知訊息。支援心率連續監測、血氧偵測及壓力指數分析，協助您全面了解身體健康狀態。內建 100 種以上運動模式，無論健走、慢跑、游泳或重訓皆能精準追蹤。超薄 9.9mm 機身搭配輕量化設計，佩戴幾乎無感。一次充電可使用長達 13 天，告別頻繁充電的困擾。5ATM 防水規格讓您游泳時也能安心配戴，是追求健康生活的最佳入門智慧手環。',
            ],
        ];

        $this->insertProducts($phoneProducts, $phones->id, $adminId);
        $this->insertProducts($laptopProducts, $laptops->id, $adminId);
        $this->insertProducts($wearableProducts, $wearables->id, $adminId);
    }

    private function insertProducts(array $products, int $categoryId, ?int $adminId): void
    {
        foreach ($products as $index => $item) {
            $hasDiscount = $index < 10;
            $discount = $item['discount'] ?? ($hasDiscount ? round(mt_rand(70, 95) / 100, 2) : 1.00);

            Product::create([
                'product_category_id' => $categoryId,
                'admin_id' => $adminId,
                'name' => $item['name'],
                'description' => $item['description'] ?? $this->generateDescription($item['name']),
                'price' => $item['price'],
                'discount' => $discount,
                'stock' => mt_rand(5, 100),
                'image' => null,
                'is_active' => true,
            ]);
        }
    }

    private function generateDescription(string $name): string
    {
        $templates = [
            "【{$name}】搭載最新一代高效能處理器，無論是日常辦公、影音娛樂還是專業創作，均能輕鬆勝任。精緻的機身工藝採用航太級鋁合金材質，兼顧輕薄與耐用性。內建大容量電池提供全天候續航力，讓您無需頻繁充電。豐富的連接埠設計與廣泛的裝置相容性，滿足各種使用場景需求。搭配高解析度螢幕與精準色彩校正，帶來專業級的視覺體驗。完善的散熱系統確保長時間高負載下依然穩定運行，是追求品質生活用戶的最佳選擇。",
            "全新上市的 {$name} 以卓越效能與優雅設計征服市場。採用先進製程打造的核心晶片，運算速度大幅提升，多工處理更加流暢。獨特的散熱設計確保長時間使用下維持穩定效能，不因高負載而降速。超薄邊框設計搭配高色域顯示面板，帶來沉浸式視覺享受。多層次安全防護機制保障您的個人資料安全，讓您安心使用。智慧化的系統優化讓每一次操作都更加直覺順暢，現在購入更享專屬售後保固服務，品質絕對有保障。",
            "{$name} 代表著科技與美學的完美融合。頂級工藝打造的機身線條流暢優雅，握持舒適且質感十足。搭載業界領先的核心技術，效能表現超越同級競品，帶給您前所未有的流暢體驗。智慧型電源管理系統自動調節耗電，在高效能與省電之間取得最佳平衡。全方位的無線連接支援確保您隨時保持連線，不錯過任何重要時刻。高品質的鏡頭系統捕捉每個珍貴瞬間，購入即享一年原廠保固與專業技術支援服務，安心有保障。",
            "專為追求極致體驗的用戶而生，{$name} 在每一個細節上都精益求精。超高規格的核心硬體配置，搭配經過深度優化的系統軟體，帶來行雲流水般的操作手感。高品質的音訊系統讓音樂與影片欣賞更加立體真實。多重生物辨識安全功能讓解鎖更快速便捷，同時確保個人隱私安全無虞。出色的相機系統記錄生活每一個精彩瞬間，讓您的創作不受任何限制，盡情揮灑創意，留下屬於自己的美好回憶與精彩故事。",
            "這款 {$name} 是當前市場上最具競爭力的產品之一，以合理的價格提供頂尖的使用體驗。強大的處理效能輕鬆應對各類高要求應用程式，流暢無延遲。精心調校的顯示系統提供準確自然的色彩還原，適合專業設計與日常使用。輕量化的機身設計便於攜帶，無論出差或旅遊都是最佳旅伴。豐富的擴充介面滿足多元連接需求，完善的售後服務網絡遍布全台，讓您購機無後顧之憂，是您升級換機的最佳時機。",
        ];

        return $templates[array_rand($templates)];
    }
}
