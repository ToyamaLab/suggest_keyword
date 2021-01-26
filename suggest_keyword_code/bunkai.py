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

results = word.split(',')

for item in results:
    print(item)

