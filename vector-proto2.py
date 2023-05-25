#!/usr/local/bin/python3
# -*- coding: utf-8 -*-
import MeCab
import unicodedata
import json
from gensim.models import KeyedVectors
import sys
from sklearn.metrics.pairwise import cosine_similarity
import numpy as np

# PHPからPOSTで送られてきたテキストデータを取得
sentence = sys.argv[1]

# MeCabの初期化
m = MeCab.Tagger("-Owakati -r /usr/local/lib/python3.11/site-packages/unidic_lite/dicdir/mecabrc")

# ストップワードのリスト
stop_words = ["の", "に", "は", "を", "た", "が", "で", "て", "と", "し", "れ", "さ", "ある", "いる", "も", "する", "から", "な", "こと", "として", "い", "や", "れる", "など", "なっ", "ない", "この", "ため", "その", "あっ", "よう", "また", "もの", "という", "あり", "まで", "られ", "なる", "へ", "か", "だ", "これ", "によって", "により", "おり", "より", "による", "ず", "なり", "られる", "において", "ば", "なかっ", "なく", "しかし", "について", "せ", "だっ", "その後", "できる", "それ", "う", "ので", "なお", "のみ", "でき", "つつ", "における", "おいて", "にて", "ほか", "ながら", "うち", "そして", "とともに", "ただし", "かつて", "それぞれ", "または", "お", "ほど", "ものの", "に対して", "ほとんど", "と共に", "といった", "です", "とも", "ところ", "ここ"]

def preprocess_text(text):
    # MeCabで形態素解析を行い、単語に分割
    words = m.parse(text).split()

    # ストップワードの除去
    words = [word for word in words if word not in stop_words]

    # 単語の正規化（すべてをひらがなに変換）
    words = [unicodedata.normalize('NFKC', word) for word in words]

    return words

model = KeyedVectors.load_word2vec_format('/Applications/XAMPP/xamppfiles/htdocs/php01/whyme_vector/entity_vector.model.bin', binary=True)

def get_word_vector(word):
    # 単語がモデルの語彙に存在するか確認
    if word in model:
        # 単語をベクトルに変換
        vector = model[word]
        return vector
    else:
        print(f"The word '{word}' does not exist in the model's vocabulary.")
        return None

# テキストの前処理
words = preprocess_text(sentence)
vectors = []  # 各単語のベクトルを格納するリスト

# 単語のベクトルを取得
for word in words:
    vector = get_word_vector(word)
    if vector is not None:
        vectors.append(vector)

response_data = {"word_vectors": [], "sentence_vector": None, "similar_words": []}

if vectors:
    # 文章全体のベクトルを計算（平均化）
    sentence_vector = sum(vectors) / len(vectors)
    response_data["sentence_vector"] = sentence_vector.tolist()

    # sentence_vector に近い単語を取得
    similar_words = model.similar_by_vector(sentence_vector, topn=10)

    # 結果の保存
    for word, similarity in similar_words:
        response_data["similar_words"].append({"word": word, "similarity": similarity})

# data.txtから既存の文とそれぞれの'sentence_vector'を読み込む
with open("data.txt", "r") as f:
    sentence_vector_db = json.load(f)

# 最も類似度が高い文とその類似度を保存する変数
most_similar_sentence = None
highest_similarity = -1

# 入力文の'sentence_vector'と既存の各文の'sentence_vector'の類似度を計算
for sentence, vector in sentence_vector_db.items():
    similarity = cosine_similarity([sentence_vector], [np.array(vector)])

    # 類似度が最高の文を更新
    if similarity > highest_similarity:
        most_similar_sentence = sentence
        highest_similarity = similarity

# 最も類似度が高い文を結果に追加
response_data["most_similar_sentence"] = most_similar_sentence

# JSON形式で結果を出力
print("Content-Type: application/json\n")

# JSON形式で結果を出力
print("Content-Type: application/json\n")
print(json.dumps(response_data))