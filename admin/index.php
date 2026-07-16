<?php
require_once("sql_user.php");
$fields = ['title', 'content'];
$dbget = sql_search_user('post', ['title', 'content'], 'カフェ 市販のルー', 'post_at DESC');
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .container {
            width: 100%;
            max-width: 700px;
            margin: auto;
        }
    </style>
    <!-- <link rel="stylesheet" href="style.css"> -->
    <!--<script src="script.js" defer></script>-->
</head>

<body>
    <div class="container">
        <?php foreach ($dbget['records'] as $get_data): ?>
            <div class="get">
                <p><?= $get_data['post_at'] ?></p>
                <p><?= $get_data['title'] ?></p>
                <p><?= $get_data['content'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>