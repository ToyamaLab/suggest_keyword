# -*- coding: utf-8 -*-
from SPARQLWrapper import SPARQLWrapper
import io, sys
import re
import json
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8")
sys.stdin = io.TextIOWrapper(sys.stdin.buffer, encoding="utf-8")

args = sys.argv
word = args[1]

sparql = SPARQLWrapper(endpoint='http://ja.dbpedia.org/sparql', returnFormat='json')
sparql.setQuery("""
    PREFIX resource:  <http://ja.dbpedia.org/resource/>
    select distinct ?p where {{
    {{
    resource:{0} ?p ?o .
    FILTER regex (?p, "http://dbpedia.org/ontology/")
    }}
    UNION
    {{
    resource:{0} ?p ?o .
    FILTER regex (?p, "http://ja.dbpedia.org/property/")
    }}
    }}
""".format(word))

results_2 = sparql.query().convert()
results_3 = json.dumps(results_2["results"]["bindings"],ensure_ascii=False)
results_4 = results_3.replace('{"p": {"type": "uri", "value": "http://dbpedia.org/ontology/', '')
results_4 = results_4.replace('{"p": {"type": "uri", "value": "http://ja.dbpedia.org/property/', '')
results_5 = results_4.replace('"}}', '')
results_7 = results_5.replace('[', '')
results_8 = results_7.replace(']', '')# ここまで文字削除
results_6 = results_8.split(', ')

for item in results_6:
    print(item)

#完全一致で年齢、出生地などのサジェストキーワードが提示できる、だけ
