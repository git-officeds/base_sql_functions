-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-07-15 06:44:52
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `blog`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL COMMENT '管理用ID（自動連番）',
  `public_id` char(10) NOT NULL COMMENT '参照用ID（数字10桁）',
  `post_at` datetime NOT NULL COMMENT '投稿日時',
  `category` varchar(50) NOT NULL COMMENT 'カテゴリー',
  `title` varchar(255) NOT NULL COMMENT '記事タイトル',
  `content` text NOT NULL COMMENT '記事本文',
  `eyecatch` varchar(100) NOT NULL COMMENT 'アイキャッチ画像ファイル名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `post`
--

INSERT INTO `post` (`id`, `public_id`, `post_at`, `category`, `title`, `content`, `eyecatch`) VALUES
(1, '2026070901', '2026-07-09 12:00:00', 'カフェ', 'お気に入りのカフェを見つけました', '今日は駅前に新しくできたカフェに行ってしてきました。落ち着いた雰囲気で、コーヒーもケーキもとても美味しかったです！ギャラリーに店内の写真を載せておきます。', ''),
(2, '2026070902', '2026-07-09 15:30:00', '旅行', '週末の日帰り温泉旅行', '週末に少し足を伸ばして日帰り温泉に行ってきました。露天風呂からの景色が最高で、日頃の疲れがすっかり癒されました。また行きたいです。', ''),
(3, '2026071001', '2026-07-10 09:00:00', 'ガジェット', '新しいスマートウォッチを購入！', 'ずっと気になっていた最新のスマートウォッチがついに届きました！画面が綺麗で、睡眠記録や通知機能がとても便利です。これから毎日使い倒します。', ''),
(4, '2026071101', '2026-07-11 18:00:00', '料理', '手作りスパイスカレーに挑戦！', '市販のルーを使わず、クミンやコリアンダーなどのスパイスからカレーを作ってみました。じっくり炒めた玉ねぎの甘みと辛さが絶妙にマッチして大成功！隠し味にヨーグルトを入れるのがポイントです。', ''),
(5, '2026071201', '2026-07-12 07:15:00', 'ライフハック', '朝活を1週間続けてみた結果', '今週から毎朝5時に起きる「朝活」を始めてみました。静かな時間に読書や勉強を集中して行えるため、1日の充実感が格段にアップしました！継続するためのコツをブログにまとめておきます。', ''),
(6, '2026071202', '2026-07-12 14:00:00', 'カフェ', '隠れ家風のブックカフェ', '裏路地で見つけた静かなブックカフェ。壁一面に本が並び、時間を忘れて読書に没頭できました。自家製のレモネードもすっきりした甘さで美味しかったです。読書好きにはたまらない空間でした。', ''),
(7, '2026071301', '2026-07-13 21:45:00', '映画', 'SF映画の金字塔を久々に見鑑賞', '不朽の名作と呼ばれるSF映画を久しぶりに配信で見返しました。何年経っても色あせない映像美と、深いストーリーの伏線回収に改めて鳥肌が立ちました。皆さんのイチオシ映画もぜひ教えてください。', '');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理用ID（自動連番）', AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
