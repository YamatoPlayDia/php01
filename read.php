<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS only -->
    <link rel="stylesheet" href="bootstrap.min.css">
    <script src="bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>登録したwhyme一覧表</title>
</head>
<body class="bg-light">
    <div class="container my-5 bg-white p-5 rounded-3">
        <h1 class="mb-4">登録したwhyme一覧表</h1>
        <?php
        // JSONデータをファイルから読み込む
        $jsonData = file_get_contents('data.txt');

        // 改行で区切られたJSONデータを配列に分割
        $jsonArray = preg_split('/\n(?={)/', $jsonData, -1, PREG_SPLIT_NO_EMPTY);

        // 年齢を計算する関数
        function calculateAge($birth)
        {
            $birthDate = new DateTime($birth);
            $currentDate = new DateTime();
            $age = $currentDate->diff($birthDate)->y;
            return $age;
        }
        // 年齢のデータを配列に格納
        $ages = [];
        // 各JSONデータをループして年齢を取得
        foreach ($jsonArray as $jsonString) {
            // JSONデータを連想配列に変換
            $data = json_decode($jsonString, true);

            // JSONデータが空でない場合のみ処理
            if (!empty($data)) {
                $age = calculateAge($data['birth']);
                $ages[] = $age;
            }
        }
        // 星座のデータをカウントする配列を初期化
        $zodiacCounts = [
            'おひつじ座' => 0,
            'おうし座' => 0,
            'ふたご座' => 0,
            'かに座' => 0,
            'しし座' => 0,
            'おとめ座' => 0,
            'てんびん座' => 0,
            'さそり座' => 0,
            'いて座' => 0,
            'やぎ座' => 0,
            'みずがめ座' => 0,
            'うお座' => 0
        ];
        // 各JSONデータをループして星座をカウント
        foreach ($jsonArray as $jsonString) {
            // JSONデータを連想配列に変換
            $data = json_decode($jsonString, true);

            // JSONデータが空でない場合のみ処理
            if (!empty($data)) {
                $zodiacSign = $data['zodiacsign'];
                if (array_key_exists($zodiacSign, $zodiacCounts)) {
                    $zodiacCounts[$zodiacSign]++;
                }
            }
        }
        // 年齢データをJavaScriptの配列として出力
        echo '<script>';
        echo 'var agesData = ' . json_encode($ages) . ';';
        echo 'var zodiacCountsData = ' . json_encode(array_values($zodiacCounts)) . ';';
        echo 'var zodiacLabels = ' . json_encode(array_keys($zodiacCounts)) . ';';
        echo '</script>';

        // テーブルの開始タグを出力
        echo '<table class="table table-light table-striped table-hover">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Sentence</th>';
        echo '<th>Birth</th>';
        echo '<th>Zodiac Sign</th>';
        echo '<th>Age</th>';
        echo '<th>Timestamp</th>';
        echo '<th>Similar Words</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // 各JSONデータをループして出力
        foreach ($jsonArray as $jsonString) {
            // JSONデータを連想配列に変換
            $data = json_decode($jsonString, true);

            // JSONデータが空でない場合のみ処理
            if (!empty($data)) {
                echo '<tr>';
                echo '<td>' . $data['name'] . '</td>';
                echo '<td>' . $data['email'] . '</td>';
                echo '<td>' . $data['sentence'] . '</td>';
                echo '<td>' . $data['birth'] . '</td>';
                echo '<td>' . $data['zodiacsign'] . '</td>';
                echo '<td>' . calculateAge($data['birth']) . '</td>';
                echo '<td>' . $data['timestamp'] . '</td>';

                // Similar Wordsをカンマ区切りで列挙
                $similarWords = '';
                foreach ($data['similar_words'] as $wordData) {
                    $similarWords .= $wordData['word'] . ', ';
                }
                $similarWords = rtrim($similarWords, ', ');
                echo '<td>' . $similarWords . '</td>';

                echo '</tr>';
            }
        }

        // テーブルの終了タグを出力
        echo '</tbody>';
        echo '</table>';
        ?>

        <a href="post.php" class="btn btn-primary mt-3">入力フォームへ</a>
        <div class="d-flex row p-3">
            <div class="col-6 p-3"><canvas id="ageChart"></canvas></div>
            <div class="col-6 p-3"><canvas id="zodiacChart"></canvas></div>
        </div>

        <a href="post.php" class="btn btn-primary mt-3">入力フォームへ</a>
    </div>
    <script>
        // Chart.jsを使用して年齢の円グラフを作成
        var ctxAge = document.getElementById('ageChart').getContext('2d');
        var ageChart = new Chart(ctxAge, {
            type: 'doughnut',
            data: {
                labels: ['10代', '20代', '30代', '40代', '50代以上'],
                datasets: [{
                    label: '年齢',
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // 年齢データを反映
        for (var i = 0; i < agesData.length; i++) {
            if (agesData[i] < 20) {
                ageChart.data.datasets[0].data[0]++;
            } else if (agesData[i] < 30) {
                ageChart.data.datasets[0].data[1]++;
            } else if (agesData[i] < 40) {
                ageChart.data.datasets[0].data[2]++;
            } else if (agesData[i] < 50) {
                ageChart.data.datasets[0].data[3]++;
            } else {
                ageChart.data.datasets[0].data[4]++;
            }
        }
        ageChart.update();

        // Chart.jsを使用して星座の円グラフを作成
        var ctxZodiac = document.getElementById('zodiacChart').getContext('2d');
        var zodiacChart = new Chart(ctxZodiac, {
            type: 'doughnut',
            data: {
                labels: zodiacLabels,
                datasets: [{
                    label: '星座',
                    data: zodiacCountsData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(94, 230, 230, 0.7)',
                        'rgba(220, 220, 220, 0.7)',
                        'rgba(135, 206, 250, 0.7)',
                        'rgba(255, 192, 203, 0.7)',
                        'rgba(64, 224, 208, 0.7)',
                        'rgba(255, 228, 225, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }

        });
        zodiacChart.update();

        console.log(zodiacCountsData);
        console.log(zodiacLabels);
    </script>
</body>
</html>