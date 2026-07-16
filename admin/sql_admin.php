<?php

/****************************************************
  設定情報
 ****************************************************/
// タイムゾーン
date_default_timezone_set("Asia/Tokyo");
// デバッグモード（ログをコンソールに出力）
$debug_mode = true;

/****************************************************
  日付・日時のフォームコントロール間での変換関数
 ****************************************************/
//日付をフォーム用の日付に変換(0000-00-00)
function sqlDate2form($date)
{
	return (
		$date == "0000-00-00" || $date == "--" || $date == "" || $date == null)
		? ""
		: date('Y-m-d', strtotime($date));
}

//フォーム用の日付をSQLの日付に変換
function sqlForm2Date($date)
{
	return ($date == "0000-00-00" || $date == "--" || $date == "" || $date == null)
		? ""
		: date('Y-m-d', strtotime($date));
}

//SQLの日時をフォーム用の日時に変換(0000-00-00T00:00:00)
function sqlDatetime2form($date)
{
	return ($date == "0000-00-00 00:00:00" || $date == "-- ::" || $date == "" || $date == null)
		? ""
		: date('Y-m-d\TH:i:s', strtotime($date));
}

//フォーム用の日時をSQLの日時に変換
function sqlForm2Datetime($date)
{
	return ($date == "0000-00-00T00:00:00" || $date == "-- ::"  || $date == "" || $date == null)
		? ""
		: date('Y-m-d H:i:s', strtotime($date));
}

/****************************************************
  ログ・エラーログ出力
 ****************************************************/

//ログ出力
function console_log($message)
{
	// debugモード時のみ実行
	global $debug_mode;
	if ($debug_mode) {
		echo <<<EOM
			<script>
				console.log("{$message}");
			</script>
		EOM;
		return;
	}
}

//エラーログ出力
function err_log($e, $message)
{
	// debugモード時のみ実行
	global $debug_mode;
	if ($debug_mode) {
		$getfile = preg_replace('/\\\/u', '/', $e->getFile());
		echo <<<EOM
			<script>
				console.log(`
					{$message}
					{$e->getMessage()}
					FILE:{$getfile}
					LINE:{$e->getLine()}
				`);
			</script>
		EOM;
		return;
	}
}


/****************************************************
  データベースログイン
 ****************************************************/
function join_server()
{

	try {

		// データベースの情報を取得
		if ($_SERVER['HTTP_HOST'] == 'localhost') {
			//DB接続情報(テスト環境)
			$myServer = array(
				'mysql:host' => 'localhost',
				'dbname' => 'blog',
				'port' => '3306',
				'id' => 'root',
				'pass' => ''
			);
		} else {
			// DB接続情報(サーバー環境)
			$myServer = array(
				'mysql:host' => '',
				'dbname' => '',
				'port' => '3306',
				'id' => '',
				'pass' => ''
			);
		}

		$dbh = new PDO(
			'mysql:host=' . $myServer['mysql:host'] . ';dbname=' . $myServer['dbname'] . ';port=' . $myServer['port'],
			$myServer['id'],
			$myServer['pass'],
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
		);

		//文字化けを防ぐ
		$dbh->query("set names utf8mb4");

		//完了ログ出力
		$result = $dbh;
		console_log("データベース接続成功");
	} catch (PDOException $e) {
		$result = null;
		err_log($e, "データベース接続失敗");
	} finally {
		return $result;
	}
}


/****************************************************
  ログイン
 ****************************************************/
function sql_login_admin($id = null, $pass = null)
{
	try {

		// IDのチェック
		if ($id == null) {
			throw new Exception("IDエラー");
		}
		// パスワードのチェック
		if ($pass == null) {
			throw new Exception("パスワードエラー");
		}

		// データベースのログイン情報を取得
		$dbh = join_server();
		if ($dbh == null) {
			throw new Exception('データベースの取得に失敗しました。');
		}

		// SQL文を作成
		$sql = "SELECT * FROM db_admin WHERE ID=:id AND pass=:pass;";

		// プリペアドステートメントを作成
		$stmt = $dbh->prepare($sql);

		// バインド
		$stmt->bindValue(":id", $id, PDO::PARAM_STR);
		$stmt->bindValue(":pass", $pass, PDO::PARAM_STR);

		//検索クエリの実行
		$stmt->execute();

		// レコードが取得できた（ログインできた）かどうかをBooleanで取得
		$result = $stmt->fetch() ? true : false;

		console_log('ログイン完了');
	} catch (\Throwable $e) {
		$result = false;
		console_log($e->getMessage());
	} finally {
		return $result;
	}
}
