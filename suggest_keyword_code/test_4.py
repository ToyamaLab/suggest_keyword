# -*- coding: utf-8 -*-
from SPARQLWrapper import JSON, SPARQLWrapper
import dbpediaknowledge
import json
import io, sys
import csv
import pprint

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8")
sys.stdin = io.TextIOWrapper(sys.stdin.buffer, encoding="utf-8")

args = sys.argv
word = args[1]
category = args[2]

results = word.split(',')

with open('category.csv') as f:
    reader = csv.reader(f)
    for row in reader:
        for item in results:
            if item == row[0] and row[1] == category:
                print(row[0])

