プロジェクト概要

Laravel 製のフリマアプリ。ユーザーは商品出品・一覧閲覧・詳細閲覧・いいね・コメント・購入ができます。
支払い情報は orders テーブルに統合し、payments テーブルは削除しています（シンプル化）。

主要機能

会員登録/ログイン（Fortify）

商品出品・編集・一覧/検索・詳細

カテゴリ（多対多）

いいね（Favorites）

コメント（Comments）

購入（Orders）… 購入方法・支払い状態を orders に保存

SOLD 表示（items.is_sold／計算値 is_sold_computed に対応）

技術スタック

PHP 8.2 / Laravel 10

MySQL

Blade / CSS

Stripe 連携予定フィールドあり（orders.stripe_payment_intent_id など）

セットアップ
cp .env.example .env
# DB設定を .env に記入
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve

主な URL

商品一覧：/ または /items

商品出品：/items/create

商品詳細：/items/{id}

データモデル（要点）

items.condition は 文字列運用（例：新品/未使用に近い/中古/良好）

支払い情報は orders に一元化

payment_method（クレジットカード/銀行振込/コンビニ払い）

payment_status（pending/succeeded/failed）

stripe_payment_intent_id（任意）

SOLD 表示

DB真値：items.is_sold（boolean）

計算値：$item->is_sold_computed（関連注文が1件でもあれば true）

購入フロー（概要）

ユーザーが商品詳細から購入

orders にレコード作成（payment_method / payment_status など）

運用に応じて items.is_sold を更新（注文時 / 決済確定時など）

ER 図（Mermaid）

Mermaid をサポートするビューアで表示可能。ドキュメントにも貼れます。

erDiagram
    USERS ||--o{ ITEMS : "has many"
    USERS ||--o{ ORDERS : "has many"
    USERS ||--o{ FAVORITES : "has many"
    USERS ||--o{ COMMENTS : "has many"
    USERS ||--o{ ADDRESSES : "has many"

    ITEMS }o--o{ CATEGORIES : "via category_item"
    ITEMS ||--o{ FAVORITES : "has many"
    ITEMS ||--o{ COMMENTS : "has many"
    ITEMS ||--o{ ORDERS : "has many"

    ORDERS }o--|| ITEMS : "belongs to"
    FAVORITES }o--|| ITEMS : "belongs to"
    FAVORITES }o--|| USERS : "belongs to"
    COMMENTS }o--|| ITEMS : "belongs to"
    COMMENTS }o--|| USERS : "belongs to"
    ADDRESSES }o--|| USERS : "belongs to"

    USERS {
      bigint id PK
      string name
      string email
      string password
      string postal_code
      string address
      string building
      string profile_image
      timestamps
    }

    ITEMS {
      bigint id PK
      bigint user_id FK
      string name
      text description
      int price
      string condition   "※文字列（新品/未使用に近い/中古/良好）"
      string image_path
      boolean is_sold
      timestamps
    }

    CATEGORIES {
      bigint id PK
      string name
      timestamps
    }

    CATEGORY_ITEM {
      bigint id PK
      bigint item_id FK
      bigint category_id FK
      unique (item_id, category_id)
      timestamps
    }

    ORDERS {
      bigint id PK
      bigint user_id FK
      bigint item_id FK
      string payment_method    "クレジットカード/銀行振込/コンビニ払い"
      string payment_status    "pending/succeeded/failed"
      string stripe_payment_intent_id
      string status            "購入完了/処理中 など"
      string shipping_address  "任意：文字列で保存"
      timestamps
    }

    FAVORITES {
      bigint id PK
      bigint user_id FK
      bigint item_id FK
      timestamps
    }

    COMMENTS {
      bigint id PK
      bigint user_id FK
      bigint item_id FK
      string content
      timestamps
    }

    ADDRESSES {
      bigint id PK
      bigint user_id FK
      string postal_code
      string address
      string building
      timestamps
    }

変更履歴（重要点）

2025-08-29: payments テーブルを削除。支払い項目は orders に統合。

2025-08-29: items.condition を 文字列運用に統一（casts から int を外す）