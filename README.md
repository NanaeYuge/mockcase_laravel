# mockcase_laravel
for mockcase laravel1

# フリマアプリ（模擬案件）

本リポジトリは、Laravelを用いて開発したフリマアプリの模擬案件です。  
ユーザー管理から商品出品・購入、コメントやお気に入り機能までを実装し、基本設計書に準拠した形で構築されています。

---

## 1. 開発環境

- 言語: PHP 8.2 / JavaScript / HTML / CSS
- フレームワーク: Laravel 10
- データベース: MySQL 8.0
- 環境構築: Docker / Laravel Sail
- 認証: Laravel Fortify

---

## 2. 実装機能

### ユーザー関連
- ユーザー登録 / ログイン / ログアウト
- プロフィール編集（プロフィール画像アップロード対応）
- 住所登録・変更機能

### 商品関連
- 商品一覧表示（検索機能、カテゴリ別表示）
- 商品出品（複数カテゴリ選択対応、画像アップロード機能）
- 商品詳細表示（画像、価格、説明、カテゴリ、状態等）
- 商品編集 / 削除機能
- お気に入り（いいね）機能
- コメント機能（投稿・削除、バリデーション対応）

### 購入関連
- 商品購入処理（支払い方法選択、住所確認）
- Stripe決済（Checkout画面遷移、応用要件）
- 購入履歴の確認

### 管理画面（管理者用）
- ユーザー一覧・詳細・削除
- お問い合わせ一覧・詳細・検索・削除
- CSVエクスポート機能（検索条件反映）

---

## 3. データベース設計

本アプリケーションの主要なテーブルは以下の通りです。

- **users** : ユーザー情報（名前、メールアドレス、パスワード 等）
- **addresses** : 住所情報（郵便番号、住所、建物名）
- **items** : 商品情報（商品名、価格、状態、説明、画像 等）
- **categories** : カテゴリ情報（カテゴリ名）
- **category_item** : 商品とカテゴリの中間テーブル
- **orders** : 注文情報（支払い方法、配送先、Stripe決済ID 等）
- **favorites** : お気に入り（いいね機能管理）
- **comments** : コメント情報（ユーザーによる商品へのコメント）

---

## 4. ER図

下記のER図にてデータベース構成を示します。

![ER図](./mockcase_laravel_ER.png)

参考:  
- [LaravelでのDB設計（Laraweb）](https://laraweb.net/surrounding/7477/)  
- [Zenn - LaravelのER図設計](https://zenn.dev/bloomer/articles/3f73f7d02e5a63)

---

## 5. セットアップ手順

```bash
# リポジトリのクローン
git clone git@github.com:yourname/mockcase-laravel.git
cd mockcase-laravel

# Docker 起動
./vendor/bin/sail up -d

# 環境変数設定
cp .env.example .env
php artisan key:generate

# マイグレーション & シーディング
php artisan migrate --seed

# 開発サーバー起動
php artisan serve

6. テスト
本アプリケーションでは PHPUnit を用いた自動テストを実装しています。

php artisan test
7. ライセンス
本ソフトウェアは学習用の模擬案件として作成されています。
営利目的での利用・配布は禁止されています。

