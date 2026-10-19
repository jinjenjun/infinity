# CLAUDE.md

Infinity — Laravel 13 + Vue 3 + Inertia.js 電商平台，含 Filament 4 後台管理。

## 技術棧

**後端**：Laravel 13、PHP 8.3+、MariaDB（生產）/ SQLite（開發）、Spatie Laravel Permission（角色權限）、Filament 4（後台管理）、DomPDF（PDF 匯出）、Pest（測試框架）

**前端**：Vue 3、Inertia.js、Vuex 4、Vite 8、Element Plus、Tailwind CSS v3、SCSS、Remixicon

## 常用指令

```bash
# 初次建置（安裝依賴、執行 migration、建置前端）
composer run setup

# 啟動完整開發環境（nginx + php-fpm + mariadb + vite + artisan serve）
npm run start

# 只啟動 artisan serve + vite（不需要系統服務時）
npm run dev:full

# 或用 composer 啟動（含 queue、pail log 監聽）
composer run dev

# 建置前端
npm run build

# PHP 程式碼格式化（Pint）
./vendor/bin/pint

# JS/Vue 程式碼格式化（ESLint + Prettier）
npm run lint

# 執行所有測試
composer test
# 或
php artisan test

# 執行單一測試
php artisan test --filter TestName

# 資料庫 migration
php artisan migrate

# 執行 Seeder（建立角色與 superadmin 帳號）
php artisan db:seed
```

## 關鍵規則

- 所有頁面透過 `Inertia::render()` 回傳，無需另建 API 端點；若需 AJAX，使用 `ProductController::indexApi()` 的方式單獨建立 JSON 回傳方法
- `Product` 與 `ProductCategory` 的公開 URL 使用 **UUID**（非 id），路由為 `/products/{uuid}`
- 商品與分類的可見性由 `HasVisibility` trait 的 `visibleTo($viewer)` scope 控管，新增查詢一律加上此 scope
- 購物車採「一使用者一 active 購物車」設計，`CartController::getOrCreateCart()` 統一取得
- 功能開發使用 `docs/plans/` 記錄計畫；完成後移至 `docs/plans/archive/`

## 架構概覽

### 請求流程

```
瀏覽器 → nginx → php-fpm → Laravel Router → Controller → Inertia::render() → Vue Page
                                                                ↑
                                           HandleInertiaRequests（自動注入 auth.user、auth.roles）
```

### 路由總覽

| 方法 | 路徑 | Controller | 認證 | 說明 |
|------|------|-----------|------|------|
| GET | `/` | HomeController@index | ✓ | 首頁 |
| GET | `/products` | ProductController@index | - | 商品列表（支援 category、search 篩選） |
| GET | `/products/{uuid}` | ProductController@show | - | 商品詳頁 |
| GET | `/cart` | CartController@index | ✓ | 購物車 |
| POST | `/cart/add` | CartController@add | ✓ | 加入購物車 |
| PATCH | `/cart/items/{item}` | CartController@update | ✓ | 更新數量 |
| DELETE | `/cart/items/{item}` | CartController@remove | ✓ | 移除品項 |
| GET | `/checkout` | CheckoutController@index | ✓ | 結帳頁 |
| POST | `/checkout` | CheckoutController@store | ✓ | 送出訂單 |
| GET | `/checkout/success/{order}` | CheckoutController@success | ✓ | 訂單完成頁 |
| GET | `/orders/{order}/pdf` | CheckoutController@downloadPdf | ✓ | 下載 PDF 帳單 |
| GET | `/orders` | OrderController@index | ✓ | 訂單列表 |
| GET | `/orders/{order}` | OrderController@show | ✓ | 訂單詳頁 |
| GET/PATCH/DELETE | `/profile` | ProfileController | ✓ | 個人資料 |
| POST | `/profile/invite-code` | ProfileController@joinInviteCode | ✓ | 加入邀請碼 |
| `/admin/*` | Filament | — | ✓ | 後台管理 |
| `/auth/*` | Breeze | — | - | 認證路由 |

### 後台管理（Filament）

後台路徑 `/admin`，設定於 `app/Providers/Filament/AdminPanelProvider.php`。每個 Resource 放在 `app/Filament/Resources/{Name}/`，拆分四個子目錄：

```
app/Filament/Resources/
├── Users/          → UserResource.php + Pages/ + Schemas/ + Tables/
├── Products/       → ProductResource.php + Pages/ + Schemas/ + Tables/
├── ProductCategories/
├── ProductSubCategories/
└── InviteCodes/
app/Filament/Pages/
└── BusinessChart.php   ← 自訂業績圖表頁
```

### 角色與權限

使用 Spatie Laravel Permission，三個角色由 `RoleSeeder` 建立：

| 角色 | 說明 |
|------|------|
| `superadmin` | 最高權限，初始帳號 `superadmin@example.com` / 密碼 `password` |
| `admin` | 可建立商品、分類、邀請碼，管理會員 |
| `user` | 一般使用者，可瀏覽商品、購物 |

`RoleMiddleware` 用法：`Route::middleware('role:admin,superadmin')`。`HandleInertiaRequests` 將 `auth.roles`（角色陣列）共享至所有頁面。

### 可見性系統（HasVisibility）

`Product` 與 `ProductCategory` 使用 `HasVisibility` trait，`visibleTo($viewer)` scope 邏輯：

| visibility 值 | 可見對象 |
|--------------|---------|
| `public` | 所有人（含未登入） |
| `member` | 透過邀請碼加入該 admin 的會員 |
| `private` | 僅建立者（admin_id 擁有者） |

### Admin-Member 關係（邀請碼機制）

```
admin（User）─── 建立 ──→ InviteCode（XXXX-XXXX 格式，可設到期日）
                               │
                    user 輸入邀請碼後 attach
                               ↓
                         admin_user（pivot）
                    admin_id | user_id | invite_code_id
```

- `User::admins()` — 我加入的所有 admin（belongs-to-many via admin_user）
- `User::members()` — 我管理的所有會員（belongs-to-many via admin_user）

### 購物流程

```
user 加入商品 → Cart (status=active) + CartItem
                      ↓
        CheckoutController::store()
        1. 建立 Order（status=pending）
        2. 迴圈建立 OrderItem（快照商品名稱與價格）
        3. 逐一扣庫存（product.stock decrement）
        4. Cart 標記為 checked_out
                      ↓
              /checkout/success/{order}
                      ↓
              GET /orders/{order}/pdf（下載帳單）
```

> 注意：`order_items.product_name` 為下單時快照，商品改名不影響歷史訂單。

### 前端結構

```
resources/
├── js/
│   ├── app.js            # Vue + Inertia 入口
│   ├── bootstrap.js
│   ├── Pages/            # Inertia 頁面（對應路由）
│   │   ├── Auth/         # 認證頁（Login、Register、ForgotPassword 等）
│   │   ├── Cart/Index.vue
│   │   ├── Checkout/     # Index.vue（結帳）、Success.vue（完成）
│   │   ├── Home/Index.vue
│   │   ├── Orders/       # Index.vue（列表）、Show.vue（詳頁）
│   │   ├── Products/     # Index.vue（列表）、Show.vue（詳頁）
│   │   ├── Profile/      # Edit.vue + Partials/
│   │   └── Dashboard.vue
│   ├── Components/       # 可重用 Vue 元件（El 前綴為自訂元件）
│   ├── Templates/        # 大型佈局模板（非 Inertia 頁面，如 BookHomepage）
│   ├── Layouts/          # AuthenticatedLayout、GuestLayout、PlatformLayout
│   ├── Libs/             # 工具函式庫（axios、epub、日期、theme 等）
│   ├── APIs/             # Axios API 模組
│   │   ├── infinity/     # 本平台 API（product.js 等）
│   │   └── unlock/       # 外部 unlock 服務 API
│   └── filament/         # Filament 自訂 JS（business-chart.js）
├── store/                # Vuex 模組（注意：不在 js/ 底下）
│   └── unlock/           # head、headMenu、member、music、information 等
└── scss/                 # 全域樣式
```

### Vuex Store 持久化

`resources/store/index.js` 內建 localStorage 插件，以下模組的指定 mutation 觸發後自動同步：

| 模組 | localStorage key | 觸發 mutation |
|------|-----------------|--------------|
| head | aitehubHeadData | head/updateTitle |
| headMenu | aitehubHeadMenu | headMenu/updateMenu |
| member | aitehubMember | member/updateLogin |
| music | aitehubMusic | music/updateMusic |
| information | aitehubInformation | information/updateTitle |
| bookmark | aitehubBookmark | bookmark/updateBookmark |
| shelf | aitehubShelf | shelf/updateShelf |
| news | aitehubNews | news/updateNews |
| category | aitehubCategory | category/updateCategory |

### HTTP 客戶端

`resources/js/Libs/axios_core.js` 的 `createUnlockClient` 工廠函式統一處理：
- CSRF token（從 `<meta name="csrf-token">` 讀取）
- `Accept-Language`（從 `<html lang="">` 或 `navigator.language` 讀取）
- Authorization header（cookie token 或 app token）
- 錯誤攔截（`onResponseError` 回呼）

`axios_vue.js` 與 `axios_debug.js` 為不同環境的包裝層。

## 資料庫 Schema

### users
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| password | string（hashed） | |
| phone | string nullable | |
| address | string nullable | |
| birthday | date nullable | |
| gender | tinyint nullable | 0=女、1=男、2=其他（Gender enum） |
| age | tinyint unsigned nullable | |
| uuid | string unique | |
| email_verified_at | timestamp nullable | |

### products
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| uuid | string unique | 公開 URL 識別碼 |
| product_category_id | FK → product_categories | cascade delete |
| admin_id | FK → users | 建立者 |
| name | string | |
| description | text nullable | |
| price | decimal(10,2) | 原價 |
| discount | decimal(3,2) | 折扣率，預設 1.00（無折扣） |
| stock | int | 庫存量 |
| image | string nullable | |
| is_active | boolean | 預設 true |
| visibility | enum(public/member/private) | 預設 public |

Computed（`$appends`）：`discounted_price`（price × discount）、`has_discount`（discount < 1.00）

### product_categories
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| uuid | string unique | |
| name | string | |
| slug | string unique | |
| admin_id | FK → users nullable | 建立者 |
| parent_id | FK → product_categories nullable | 父分類（支援二層）|
| visibility | enum(public/member/private) | 預設 public |

### carts
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| user_id | FK → users | cascade delete |
| status | enum(active/checked_out/abandoned) | 預設 active |

### cart_items
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| cart_id | FK → carts | cascade delete |
| product_id | FK → products | cascade delete |
| quantity | int | 預設 1 |
| price | decimal(10,2) | 加入時快照的折扣後單價 |

### orders
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| user_id | FK → users | cascade delete |
| order_number | string unique | 格式：ORD-{UNIQID大寫} |
| status | enum(pending/paid/failed/cancelled) | 預設 pending |
| total | decimal(10,2) | |
| recipient_name | string | |
| recipient_phone | string | |
| recipient_address | string | |
| payment_method | string | 預設 credit_card |
| ecpay_trade_no | string nullable | 綠界交易編號（預留串接） |

### order_items
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| order_id | FK → orders | cascade delete |
| product_id | FK → products | cascade delete |
| product_name | string | 下單當下快照，不受後續改名影響 |
| price | decimal(10,2) | 下單當下單價 |
| quantity | int | |

### invite_codes
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| admin_id | FK → users | cascade delete |
| code | string unique | 格式：XXXX-XXXX（隨機大寫英數） |
| expires_at | timestamp nullable | 到期時間 |

### admin_user（pivot）
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint PK | |
| admin_id | FK → users | cascade delete |
| user_id | FK → users | cascade delete |
| invite_code_id | FK → invite_codes nullable | set null on delete |
| unique(admin_id, user_id) | | 防止重複加入 |

## 詳細文件

- `./docs/plans/` — 開發計畫目錄
- `./docs/plans/archive/` — 已完成計畫歸檔

## 必要遵守項目

- 商品查詢務必加上 `->visibleTo(auth()->user())` scope，避免洩漏不該顯示的商品
- 新增 Filament Resource 須遵循既有四目錄結構（Pages/Schemas/Tables/Resource.php）
- 訂單建立（`CheckoutController::store`）含庫存扣除，屬多步驟操作，修改時留意資料一致性
- `order_items.product_name` 快照設計為刻意行為，不可改為關聯查詢
- Vuex store 新模組若需持久化，須在 `resources/store/index.js` 的 `persistedModules` 陣列中登記
