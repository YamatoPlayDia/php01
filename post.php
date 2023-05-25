<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS only -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <script src="bootstrap.min.js"></script>
    <title>Word Vector Form</title>
</head>
<body class="bg-light">
    <div class="container my-5 bg-white p-5 rounded-3">
        <h1 class="mb-4">単語ベクトル分析</h1>
        <form method="POST" action="result.php">
        <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp">
            <div id="nameHelp" class="form-text">例：名瀬 私</div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">例：whyme@example.com</div>
        </div>
        <div class="mb-3">
            <label for="birth" class="form-label">生年月日</label>
            <input type="date" class="form-control" id="birth" name="birth" aria-describedby="birthHelp">
            <div id="birthHelp" class="form-text">例：1990/01/01</div>
        </div>
        <div class="mb-3">
            <label for="sentence" class="form-label">Why me?</label>
            <input type="text" class="form-control" id="sentence" name="sentence" aria-describedby="sentenceHelp">
            <div id="sentenceHelp" class="form-text">価値観を記入してください。</div>
        </div>
        <button type="submit" class="btn btn-primary">送信</button>
        </form>
        <a href="read.php" class="btn btn-secondary mt-3">登録データ一覧へ</a>
    </div>
</body>
</html>
