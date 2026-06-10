# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 技術棧

**後端**：Laravel 13、PHP 8.3+、SQLite（開發環境）、Spatie Laravel Permission（角色權限）、Filament 4（後台管理）、DomPDF（PDF 匯出）

**前端**：Vue 3、Inertia.js、Vuex 4、Vite、Element Plus、Tailwind CSS、SCSS、Remixicon

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

## 架構概覽

### 請求流程

所有前端頁面透過 **Inertia.js** 渲染，後端 Controller 呼叫 `Inertia::render('PageName', [...props])` 即可將資料傳給 Vue 頁面，無需建立 API 端點。`HandleInertiaRequests` 中介層會將 `auth.user` 和 `auth.roles` 自動共享至所有頁面。

### 後台管理（Filament）

後台路徑為 `/admin`，由 `app/Providers/Filament/AdminPanelProvider.php` 設定。每個 Filament Resource 放在 `app/Filament/Resources/{ResourceName}/` 目錄下，並拆分為四個子目錄：

- `Pages/` — CRUD 頁面（Create、Edit、List）
- `Schemas/` — 表單欄位定義
- `Tables/` — 列表欄位與篩選定義
- `{ResourceName}Resource.php` — Resource 主檔

### 角色與權限

使用 Spatie Laravel Permission，角色定義於 `RoleSeeder`：`superadmin`、`admin`、`user`。`RoleMiddleware` 提供路由層級的角色守衛。`superadmin` 預設帳號由 `AdminSeeder` 建立（email: `superadmin@example.com`）。

### 購物流程

購物車採「一個使用者一個 active 購物車」設計（`Cart.status = active`）。結帳時建立 `Order` 與 `OrderItem`，並將購物車標記為 `checked_out`。訂單狀態預設為 `pending`，支援 PDF 訂單下載（`/orders/{order}/pdf`）。`Product` 以 UUID 作為公開 URL 識別碼（`/products/{uuid}`）。

### 前端結構

```
resources/
├── js/
│   ├── Pages/        # Inertia 頁面元件（對應路由）
│   ├── Components/   # 可重用 Vue 元件（El 前綴為自訂元件）
│   ├── Templates/    # 大型頁面佈局模板（非 Inertia 頁面）
│   ├── Layouts/      # 頁面外框（Authenticated、Guest、Platform）
│   ├── Libs/         # 工具函式庫（axios、epub、日期等）
│   └── APIs/         # Axios API 模組
├── store/            # Vuex 模組（注意：不在 js/ 底下）
└── scss/             # 全域樣式
```

### Vuex Store

`resources/store/index.js` 內建 **localStorage 持久化插件**，`head`、`headMenu`、`member`、`music`、`information`、`bookmark`、`shelf`、`news`、`category` 等模組會在特定 mutation 觸發時自動同步至 localStorage（key 前綴為 `aitehub`）。

### HTTP 客戶端

`resources/js/Libs/axios_core.js` 提供 `createUnlockClient` 工廠函式，統一處理 CSRF token、Accept-Language、Authorization header 注入。`axios_vue.js` 與 `axios_debug.js` 是針對不同環境的包裝層。
