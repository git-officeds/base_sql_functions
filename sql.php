<?php

/****************************************************
  設定情報
****************************************************/ 
date_default_timezone_set("Asia/Tokyo");

/****************************************************
  日付・日時のフォームコントロール間での変換関数
****************************************************/
//日付をフォーム用の日付に変換(0000-00-00)
function sqlDate2form($date){
	return (
		$date == "0000-00-00" || $date == "--" || $date == "" || $date == null)
		? ""
		: date('Y-m-d', strtotime($date));
}

//フォーム用の日付をSQLの日付に変換
function sqlForm2Date($date){
	return ($date == "0000-00-00" || $date == "--" || $date == "" || $date == null)
		? ""
		: date('Y-m-d', strtotime($date));
}

//SQLの日時をフォーム用の日時に変換(0000-00-00T00:00:00)
function sqlDatetime2form($date){
	return ($date == "0000-00-00 00:00:00" || $date == "-- ::" || $date == "" || $date == null)
		? ""
		: date('Y-m-d\TH:i:s', strtotime($date));
}

//フォーム用の日時をSQLの日時に変換
function sqlForm2Datetime($date){
	return ($date == "0000-00-00T00:00:00" || $date == "-- ::"  || $date == "" || $date == null)
		? ""
		: date('Y-m-d H:i:s', strtotime($date));
}

/****************************************************
  ログ・エラーログ出力
****************************************************/

//ログ出力
function console_log($message){
	echo <<<EOM
		<script>
			console.log("{$message}");
		</script>
	EOM;
	return;
}

//エラーログ出力
function err_log($e, $message){
	$getfile = preg_replace('/\\\/u', '/',$e->getFile());
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


/****************************************************
  データベースログイン
****************************************************/
function join_server(){

	try{

		// データベースの情報を取得
        if($_SERVER['HTTP_HOST'] == 'localhost'){
            //DB接続情報(テスト環境)
            $myServer = array(
            'mysql:host' => 'localhost',
            'dbname' => 'blog',
            'port' => '3306',
            'id' => 'root',
            'pass' => ''
            );
        }else{
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
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
		);
	
		//文字化けを防ぐ
		$dbh->query("set names utf8mb4");

		//完了ログ出力
		$result = $dbh;
		console_log("データベース接続成功");

	}catch(PDOException $e){
		$result = null;
		err_log($e, "データベース接続失敗");
	}finally{
		return $result;
	}

}


/****************************************************
 フリーワード検索のSQLフォーマット
 SELECT * FROM users 
  WHERE CONCAT_WS(' ', col1, col2, col3) LIKE '%キーワード1%'
  AND CONCAT_WS(' ', col1, col2, col3) LIKE '%キーワード2%';
****************************************************/

/****************************************************
  フリーワード検索（半角スペースでOR検索）
  $tablename: テーブル名
  $fields: 検索対象のフィールド名（配列）
  $keyword: 検索キーワード（複数可）
  $orderby: 並べ替え指定
  返り値：配列
  エラー：Exception
****************************************************/
function sql_search_user($tablename = null, $fields = null, $keyword = null, $orderby = null){

	try {
		// 配列の初期化
		$result = array();

		// テーブル名のチェック
		if($tablename == null){ throw new Exception("テーブル参照エラー"); }
		// フィールド名のチェック
		if($fields == null){ throw new Exception("フィールド指定エラー"); }	 
		// 並べ替え指定が無い場合は無し
		$or = ($orderby == null) ? "" : " ORDER BY ".$orderby;

		// キーワードが未指定の場合はすべて表示
		if($keyword == null){
			$sql = "SELECT * FROM {$tablename} WHERE 1" . $or . ";";
		// キーワードありの場合
		}else{   
			// ユーザーからの入力を安全に分割する
			$keyword_trimmed = preg_replace('/^[ \s]+|[ \s]+$/u', '', $keyword);
			$keys = preg_split('/[\s ]+/u', $keyword_trimmed);
			$flds = implode(',',$fields);

			// 検索対象のカラムを1つに結合
			$search_target = "CONCAT_WS(' ', {$flds})";

			// SQLの「LIKE ?」の部分をキーワードの数だけ作る
			$conditions = [];
			foreach ($keys as $index => $key) {
				// プレースホルダーを「:word0, :word1...」と名前付きにする
				$conditions[] = "{$search_target} LIKE :word{$index}";
			}

			// SQL文を組み立てる
			$sql = "SELECT * FROM {$tablename} WHERE " . implode(' OR ', $conditions);
			// 生成されるSQL例（2語の場合）: 
			// SELECT * FROM users WHERE CONCAT_WS(' ', col1, col2, col3) LIKE :word0 OR CONCAT_WS(' ', col1, col2, col3) LIKE :word1
		}

		// データベースのログイン情報を取得
		$dbh = join_server();
		if($dbh == null){ throw new Exception('データベースの取得に失敗しました。');}

		// プリペアドステートメントを作成
		$stmt = $dbh -> prepare($sql);

		// ここで bindValue（プレースホルダーに値をバインド）
		foreach ($keys as $index => $key) {
			// 検索用のワイルドカード「%」を前後に付与する
			$bind_value = "%{$key}%";
			
			// 第1引数：プレースホルダー名（例：:word0）
			// 第2引数：検索キーワード（例：%カフェ%）
			// 第3引数：データの型（文字列を指定）
			$stmt->bindValue(":word{$index}", $bind_value, PDO::PARAM_STR);
		}

		//検索クエリの実行
		$stmt -> execute();

		// 全レコードの内容を変数に転写
		$i = 0;
		while( $get_sql = $stmt -> fetch( PDO::FETCH_ASSOC ) ){
			$result[$i] = $get_sql;
			$i++;
		}

		//配列の中身が空の場合
		if(is_array($result) && empty($result)){
			throw new Exception('情報が見つかりません');
		}

		console_log('データベース検索完了');

	} catch (\Throwable $e) {
		
		console_log($e->getMessage());

	} finally {

		return $result;

	}

}