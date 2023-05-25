<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS only -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <script src="bootstrap.min.js"></script>
    <title>Word Vector Results</title>
</head>
<body class="bg-light">
    <div class="container my-5 bg-white p-5 rounded-3">
        <h1 class="mb-4">単語ベクトルの結果</h1>
        <?php

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        function h ($value) {
            return htmlspecialchars($value,ENT_QUOTES);
        }
        function getZodiacSign($birth) {
            $date = date_create($birth);
            $month = intval(date_format($date, 'm'));
            $day = intval(date_format($date, 'd'));
            switch ($month) {
                case 1: // 1月
                    if ($day >= 20) {
                        return 'みずがめ座';
                    } else {
                        return 'やぎ座';
                    }
                    break;
                case 2: // 2月
                    if ($day >= 19) {
                        return 'うお座';
                    } else {
                        return 'みずがめ座';
                    }
                    break;
                case 3: // 3月
                    if ($day >= 21) {
                        return 'おひつじ座';
                    } else {
                        return 'うお座';
                    }
                    break;
                case 4: // 4月
                    if ($day >= 20) {
                        return 'おうし座';
                    } else {
                        return 'おひつじ座';
                    }
                    break;
                case 5: // 5月
                    if ($day >= 21) {
                        return 'ふたご座';
                    } else {
                        return 'おうし座';
                    }
                    break;
                case 6: // 6月
                    if ($day >= 22) {
                        return 'かに座';
                    } else {
                        return 'ふたご座';
                    }
                    break;
                case 7: // 7月
                    if ($day >= 23) {
                        return 'しし座';
                    } else {
                        return 'かに座';
                    }
                    break;
                case 8: // 8月
                    if ($day >= 23) {
                        return 'おとめ座';
                    } else {
                        return 'しし座';
                    }
                    break;
                case 9: // 9月
                    if ($day >= 23) {
                        return 'てんびん座';
                    } else {
                        return 'おとめ座';
                    }
                    break;
                case 10: // 10月
                    if ($day >= 24) {
                        return 'さそり座';
                    } else {
                        return 'てんびん座';
                    }
                    break;
                case 11: // 11月
                    if ($day >= 23) {
                        return 'いて座';
                    } else {
                        return 'さそり座';
                    }
                    break;
                case 12: // 12月
                    if ($day >= 22) {
                        return 'やぎ座';
                    } else {
                        return 'いて座';
                    }
                    break;
                default:
                    return '無効な日付です';
            }
        }
        function calculateAge($birth){
            $birthDate = new DateTime($birth);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate)->y;
            return $age;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 入力された文章を取得
            $name = $_POST['name'];
            $email = $_POST['email'];
            $birth = $_POST['birth'];
            $sentence = $_POST['sentence'];

            // Pythonスクリプトのパス
            $python_script = '/Applications/XAMPP/xamppfiles/htdocs/php01/whyme_vector/vector.py';

            // Pythonスクリプトを実行し、結果を取得
            $python_path = '/usr/local/bin/python3';  // `which python3`の出力を使う
            exec($python_path . ' ' . $python_script . ' ' . escapeshellarg($sentence) . ' 2>&1', $output, $return_var);

            $output_json = mb_convert_encoding($output[2], "UTF-8", "ISO-8859-1");
            $output_array = json_decode($output_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo '<pre>';
                echo json_last_error_msg();
                echo '</pre>';
            }
            // JSONの解析に成功したら、以降の処理を行う

            // 追加したいフィールドを指定します
            $output_array['name'] = $name;
            $output_array['email'] = $email;
            $output_array['sentence'] = $sentence;
            $output_array['birth'] = $birth;
            $output_array['zodiacsign'] = getZodiacSign($birth);
            $output_array['timestamp'] = date('Y-m-d H:i:s');  // 現在の日時を格納します

            // データをJSON形式にエンコードします
            $json_data = json_encode($output_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // ファイルを開きます ('a'は追記モードを指定)
            $file = fopen('data.txt', 'a');

            // データをファイルに書き込みます
            fwrite($file, $json_data . "\n");  // \nは改行を意味します

            // ファイルを閉じます
            fclose($file);

            // 結果をテーブルで表示
            echo '<table class="table table-light table-striped table-hover">';
            echo '<thead><tr><th>入力情報</th><th></th></tr></thead>';
            echo '<tbody>';
            echo "<tr><td>名前</td><td>".h($name)."</td></tr>";
            echo "<tr><td>メールアドレス</td><td>".h($email)."</td></tr>";
            echo "<tr><td>生年月日</td><td>".h($birth)."</td></tr>";
            echo "<tr><td>年齢</td><td>".calculateAge($birth)."</td></tr>";
            echo "<tr><td>星座</td><td>".getZodiacSign($birth)."</td></tr>";
            echo "<tr><td>Whyme</td><td>".h($sentence)."</td></tr>";
            echo '</tbody>';
            echo '</table>';
            echo '<table class="table table-light table-striped table-hover">';
            echo '<thead><tr><th>似ている辞書の単語TOP10</th><th>類似度</th></tr></thead>';
            echo '<tbody>';
            if (isset($output_array['similar_words'])) {
                foreach ($output_array['similar_words'] as $word_data) {
                    echo "<tr><td>".h($word_data['word'])."</td><td>".h($word_data['similarity'])."</td></tr>";
                }
            } else {
                echo "<tr><td>Error</td><td>No similar words found.</td></tr>";
            }
            echo '</tbody>';
            echo '</table>';
            echo '<table class="table table-light table-striped table-hover">';
            echo '<thead><tr><th>似ているwhymeTOP10</th><th>類似度</th></tr></thead>';
            echo '<tbody>';

            if (isset($output[4])) {
                $startIndex = 4;
                $endIndex = min(4 + 10, count($output));

                for ($i = $startIndex; $i < $endIndex; $i++) {
                    $value = $output[$i];

                    $exploded = explode(': ', $value);
                    $similarity = $exploded[0];
                    $sentence = $exploded[1];

                    echo "<tr><td>$sentence</td><td>$similarity</td></tr>";
                }
            } else {
                echo '<tr><td>Error</td><td>No similar words found.</td></tr>';
            }

            echo '</tbody>';
            echo '</table>';
        }
        ?>
        <a href="post.php" class="btn btn-primary mt-3">戻る</a>
        <a href="read.php" class="btn btn-secondary mt-3">登録データ一覧へ</a>
    </div>
</body>
</html>