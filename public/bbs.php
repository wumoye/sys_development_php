<?php
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

if (isset($_POST['body'])) {
    // POSTで送られてくるフォームパラメータ body がある場合

    $image_filename = null;
    if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
        // アップロードされた画像がある場合
        if (preg_match('/^image\//', mime_content_type($_FILES['image']['tmp_name'])) !== 1) {
            // アップロードされたものが画像ではなかった場合
            header("HTTP/1.1 302 Found");
            header("Location: ./bbs.php");
        }

        // 元のファイル名から拡張子を取得
        $pathinfo = pathinfo($_FILES['image']['name']);
        $extension = $pathinfo['extension'];
        // 新しいファイル名を決める。他の投稿の画像ファイルと重複しないように時間+乱数で決める。
        try {
            $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;
        } catch (Exception $e) {
        }
        $filepath = '/var/www/public/image/' . $image_filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $filepath);
    }

    // insertする
    $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :image_filename)");
    $insert_sth->execute([
        ':body' => $_POST['body'],
        ':image_filename' => $image_filename,
    ]);

    // 処理が終わったらリダイレクトする
    // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
    header("HTTP/1.1 302 Found");
    header("Location: ./bbs.php");
    return;
}

// いままで保存してきたものを取得
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
$select_sth->execute();
?>
<head>
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" type="text/css" href="./css/bbs.css"/>
</head>
<body>
<div class="box">
    <div class="header"></div>
    <div class="main">
        <div class="left">
            <div class="show">
                <?php foreach ($select_sth as $entry): ?>
                    <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
                        <dt>連番</dt>
                        <dd><?= $entry['id'] ?></dd>
                        <dt>日時</dt>
                        <dd><?= $entry['created_at'] ?></dd>
                        <dt>内容</dt>
                        <dd>
                            <?= nl2br(htmlspecialchars($entry['body'])) // 必ず htmlspecialchars() すること                    ?>
                            <?php if (!empty($entry['image_filename'])): ?>
                                <div>
                                    <img src="/image/<?= $entry['image_filename'] ?>" style="max-height: 10em;">
                                </div>
                            <?php endif; ?>
                        </dd>
                    </dl>
                <?php endforeach ?>
            </div>
        </div>
        <div class="right">
            <div class="send">
                <!-- フォームのPOST先はこのファイル自身にする -->
                <form method="POST" action="./bbs.php" enctype="multipart/form-data">
                    <textarea id="body" name="body" cols="40" rows="10"></textarea>
                    <div style="margin: 1em 0;">
                        <input type="file" accept="image/*" name="image" id="imageInput">
                        <!--                        <input type="file" accept="image/*" id="picFile" onchange="readFile(this)"/>-->
                        <br>
                        <!--                        <img style="" id="img" src="" alt=""/>-->
                    </div>
                    <button id="subBtn" type="submit">送信</button>
                </form>
            </div>
        </div>
    </div>
    <div class="footer" style="text-align: center;"><br>
        <p>プログラマー専攻 19d2030019</p>
    </div>
</div>
</body>
<hr>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const imageInput = document.getElementById("imageInput");
        imageInput.addEventListener("change", () => {
            if (imageInput.files.length < 1) {
                // 未選択の場合
                return;
            }
            if (imageInput.files[0].size > 5 * 1024 * 1024) {
                // ファイルが5MBより多い場合
                alert("5MB以下のファイルを選択してください。");
                imageInput.value = "";
            }
        });
    });
</script>
