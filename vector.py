#!/usr/local/bin/python3
# -*- coding: utf-8 -*-
import MeCab
import unicodedata
import json
import re  # 追加
from gensim.models import KeyedVectors
import sys
from scipy import spatial
from collections import deque

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
        # print(f"The word '{word}' does not exist in the model's vocabulary.")
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

# JSON形式で結果を出力
print("Content-Type: application/json\n")
print(json.dumps(response_data))

# Load all data from file
with open("/Applications/XAMPP/xamppfiles/htdocs/php01/whyme_vector/data.txt", "r") as f:
    all_data = f.read()

# Split data into separate JSON strings
json_strings = re.split('\n(?={)', all_data)

# Parse each JSON string and extract sentence vectors
sentences_and_vectors = deque(maxlen=10)

for json_str in json_strings:
    data = json.loads(json_str)
    # Check if both 'sentence_vector' and 'sentence' keys exist in the data
    if "sentence_vector" in data and "sentence" in data:
        sentences_and_vectors.append((data["sentence_vector"], data["sentence"]))

similar_sentences = []
for vec, sent in sentences_and_vectors:
    similarity = 1 - spatial.distance.cosine(sentence_vector, vec)
    similar_sentences.append((similarity, sent))

# Sort sentences by similarity
similar_sentences.sort(reverse=True)

# Print the top 10 similar sentences
print("Top 10 similar sentences:")
for sim, sent in similar_sentences[:10]:
    print(f"{sim}: {sent}")