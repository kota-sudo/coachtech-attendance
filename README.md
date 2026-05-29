# coachtech 勤怠管理アプリ

CoachTech 課題用の勤怠管理 Web アプリケーションです。一般ユーザーは打刻・勤怠確認・修正申請ができ、管理者は日次勤怠の確認、スタッフ管理、修正申請の承認、CSV 出力ができます。

## 環境構築手順

リポジトリのルート（`docker-compose.yml` があるディレクトリ）で作業してください。Laravel アプリケーションは `src/` ディレクトリにあります。

```bash
git clone <リポジトリURL>
cd coachtech-attendance
docker compose up -d --build
docker compose exec php bash
```

コンテナ内（作業ディレクトリ `/var/www` = ホストの `src/`）で以下を実行します。

```bash
composer install
cp .env.example .env
```

`.env` のデータベース設定を Docker 用に変更してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=attendance_user
DB_PASSWORD=password
```

続けて以下を実行します。

```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

## 使用技術

- PHP
- Laravel
- MySQL
- Docker
- Fortify
- PHPUnit

## URL

| 用途 | URL |
|------|-----|
| アプリ | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |

## phpMyAdmin ログイン情報

| 項目 | 値 |
|------|-----|
| サーバ | mysql |
| ユーザー名 | attendance_user |
| パスワード | password |

## テスト用ログイン情報

### 管理者

| 項目 | 値 |
|------|-----|
| メールアドレス | admin@example.com |
| パスワード | password |
| ログイン URL | http://localhost:8080/admin/login |

### 一般ユーザー

Seeder で 5 名の一般ユーザーが作成されます。いずれもパスワードは `password` です。

| メールアドレス |
|----------------|
| user1@example.com |
| user2@example.com |
| user3@example.com |
| user4@example.com |
| user5@example.com |

| 項目 | 値 |
|------|-----|
| ログイン URL | http://localhost:8080/login |

## 主な機能

### 一般ユーザー

- 一般ユーザー登録・ログイン
- 勤怠打刻（出勤・休憩・退勤）
- 勤怠一覧（月次）
- 勤怠詳細・修正申請
- 修正申請一覧（承認待ち / 承認済み）

### 管理者

- 管理者ログイン
- 日次勤怠一覧
- スタッフ一覧
- スタッフ別月次勤怠一覧
- 勤怠 CSV 出力
- 修正申請一覧・承認

## テスト実行方法

コンテナ内の `/var/www` で以下を実行します。

```bash
php artisan test
```

ホストから実行する場合の例:

```bash
docker compose exec php php artisan test
```
