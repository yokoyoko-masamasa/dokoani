# docs/ 資料の索引

ドコアニの設計資料を置くフォルダ。実装時に参照する資料は、すべてここから辿れる。
最終更新: 2026-09-20

## 資料一覧

| 資料 | ファイル | 原本（正） | 更新するとき |
|---|---|---|---|
| MVP 決定事項 | `docs/decisions.md` | このファイル | 範囲や方針を変えたら直す |
| MVP開発機能一覧 | `docs/features.md` | Notion「MVP開発機能一覧」（URLはファイル冒頭に記載） | Notion の分類（A/B/C）を直したら、このファイルも直す |
| 要件定義書 | `docs/requirements.md` | Googleドキュメント（URLはファイル冒頭に記載） | 原本を直したら、このファイルも同じ内容に直す |
| DB設計（SQL） | `docs/db/schema_ver3.sql` | drawDB で作成した ER 図（PostgreSQL 用 SQL を書き出したもの） | ER図を直したら SQL を書き出し直し、バージョンを上げる |
| ワイヤーフレーム目録 | `docs/wireframes/INDEX.md` | Figma | 画面を増減・変更したら目録を直す |
| ワイヤーフレーム画像（任意） | `docs/wireframes/images/*.png` | Figma | 画面を変更したら書き出し直す |

## 資料の優先順位

1. `features.md`: 作るかどうか（MVP は A の43件だけ）
2. `requirements.md`: どう動くか（**要件定義書を正とする**）
3. `db/schema_ver3.sql`: DB の構造
4. `wireframes/`: 画面の参考（要件定義書と違うときは、要件定義書に従う）

詳細は `docs/decisions.md`。

## 読み込み方式（資料ごと）

- **要件定義書・MVP開発機能一覧**: Markdown ファイルとして置く。Google ドキュメントや Notion の URL を渡す方式、および MCP 接続は使わない。
- **ワイヤーフレーム**: 「目録（テキスト）＋画像（任意）」で置く。Figma MCP は常用しない。
- **DB設計**: SQL ファイルとして置く。`schema_ver3.sql` は drawDB が出力した内容のまま（1文字も変えていない）。
- `ver2` の SQL は置かない。ver3 で `anime_id` → `anime_title_id` への改名と、`created_at` / `updated_at` 等の追加が入っており、ver2 は古い。

## DB（schema_ver3.sql）のテーブル

`users` / `streaming_services` / `anime_titles` / `user_subscriptions` / `anime_availabilities` / `user_anime_lists`（6テーブル）

- このSQLは設計の参照用（想定: 実装では Laravel の migration で作る）。
- SQL で取りうる値が定義されていない列（`availability_status`、`user_anime_lists.status`）は、実装時に決める（`decisions.md` の決定6）。
