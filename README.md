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
php artisan key:generate
php artisan migrate:fresh --seed
```

`.env.example` には Docker 用の MySQL・MailHog・タイムゾーン設定が含まれています。必要に応じて `MAIL_FROM_ADDRESS` などを変更してください。

```env
APP_URL=http://localhost:8080
APP_TIMEZONE=Asia/Tokyo
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=attendance_user
DB_PASSWORD=password
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@example.com"
```

## 使用技術

- PHP
- Laravel
- MySQL
- Docker
- MailHog
- Fortify
- PHPUnit

## URL

| 用途 | URL |
|------|-----|
| アプリ | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| MailHog（メール確認） | http://localhost:8026 |

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

Seeder で 5 名の一般ユーザーが作成されます。いずれもパスワードは `password` です（メール認証済み）。

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

- 一般ユーザー登録・メール認証・ログイン
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

## データベース設計

### テーブル一覧

| テーブル名 | 説明 |
|-----------|------|
| users | ユーザー（一般・管理者） |
| attendances | 勤怠記録 |
| break_times | 休憩記録 |
| attendance_correction_requests | 修正申請 |
| attendance_correction_breaks | 修正申請の休憩 |

### リレーション

- `users` 1 — N `attendances`
- `attendances` 1 — N `break_times`
- `attendances` 1 — N `attendance_correction_requests`
- `attendance_correction_requests` 1 — N `attendance_correction_breaks`

## メール認証の確認方法

1. http://localhost:8080/register から新規登録
2. **メール認証誘導画面**が表示される（「認証はこちらから」「認証メール再送」ボタンあり）
3. http://localhost:8026 （MailHog）で認証メールを確認するか、誘導画面の「認証はこちらから」をクリック
4. 認証完了後、**勤怠打刻画面（/attendance）** に遷移
5. 未認証のままログインした場合も誘導画面へ遷移

## テスト実行方法

コンテナ内の `/var/www` で以下を実行します。

```bash
php artisan test
```

ホストから実行する場合の例:

```bash
docker compose exec php php artisan test
```
