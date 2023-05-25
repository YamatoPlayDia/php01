#!/usr/local/bin/python3
# -*- coding: utf-8 -*-
print("Content-Type: text/html\n")
print(f"<h1>Hello World</h1>")

import MeCab
import unicodedata
from gensim.models import KeyedVectors

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

model = KeyedVectors.load_word2vec_format('/Applications/XAMPP/xamppfiles/htdocs/php01/python/entity_vector.model.bin', binary=True)

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
sentence = "よくばりに自分の人生を全うしたい"
words = preprocess_text(sentence)
vectors = []  # 各単語のベクトルを格納するリスト

# 単語のベクトルを取得
for word in words:
    vector = get_word_vector(word)
    print(f"<p>Vector for '{word}': {vector}</p>")
    if vector is not None:
        vectors.append(vector)

if vectors:
    # 文章全体のベクトルを計算（平均化）
    sentence_vector = sum(vectors) / len(vectors)
    print(f"<p>Vector for sentence: {sentence_vector}</p>")
else:
    print("No valid word vectors found.")

# sentence_vector に近い単語を取得
similar_words = model.similar_by_vector(sentence_vector, topn=10)

# 結果の表示
for word, similarity in similar_words:
    print(f"Similar word: {word}, Similarity: {similarity}")
