import csv
import pprint
import sys

args = sys.argv
word = args[1]

with open('category.csv') as f:
    reader = csv.reader(f)
    for row in reader:
        if row[0] == word:
            print(row[1])

#親側を呼び出す
#例：一つ前に戻る、Artistから親のカテゴリPersonに戻す