# 管理画面 認証・セキュリティ条件定義仕様

## 1. システム概要・前提条件
* **運用環境**: 顧客社内のローカルLAN（同一ルーター内）での運用。
* **アクセス経路**: 職員のデバイスからWi-Fiおよび有線LANを経由してXAMPPサーバーに接続。
* **ユーザー定義**: システムを利用する主体アカウントは3つのみ。会員・講師等の外部アカウントは作成しない。
* **セキュリティ方針**: コストを抑えつつ、ローカルネットワーク内の盗聴およびテナント周辺からのWi-Fiハッキング（総当たり攻撃）に対処する。

---

## 2. アカウント権限（Role）定義
本システムは単一のアカウントを共有して利用する特性上、以下の3つの役割（権限）をあらかじめ定義し、レコードとして登録する。

| ログインID (username) | 権限 (role) | 許可する操作 | 用途 |
| :--- | :--- | :--- | :--- |
| `developer_admin` | `developer` | 全機能へのアクセス、システム設定、アカウント管理、デバッグ機能 | 開発者（あなた）のメンテナンス用 |
| `super_admin` | `super` | 会員・講座・講師データの閲覧、登録、編集、および **「削除」** | 顧客側責任者・管理者用 |
| `staff_admin` | `staff` | 会員・講座・講師データの閲覧、登録、編集（※ **削除は不可**） | 一般スタッフ・受付端末の共有用 |

---

## 3. データベース（認証関連）テーブル定義

### 3.1 管理者マスターテーブル (`db_admin`)
ログインユーザー情報を管理する。パスワードは生のデータでは保持しない。

```sql
CREATE TABLE db_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,       -- ログインID
    password_hash VARCHAR(255) NOT NULL,        -- password_hash()による暗号化文字列
    role VARCHAR(20) NOT NULL DEFAULT 'staff',  -- 権限 ('developer', 'super', 'staff')
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```
