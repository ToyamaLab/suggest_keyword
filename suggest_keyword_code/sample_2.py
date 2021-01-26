# -*- coding: utf-8 -*-
from SPARQLWrapper import SPARQLWrapper
import dbpediaknowledge
import json
import sys
import csv
import pprint

args = sys.argv
word = args[1]
category = args[2]

sparql = SPARQLWrapper(endpoint='http://ja.dbpedia.org/sparql', returnFormat='json')
sparql.setQuery("""
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
    PREFIX ontology: <http://dbpedia.org/ontology/>
    select distinct ?label where {{
    ?a rdfs:label ?label.
    ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ontology:{1} .
    FILTER regex (?label, "{0}")
}}
""".format(word, category))

results_2 = sparql.query().convert()
results_3 = json.dumps(results_2["results"]["bindings"],ensure_ascii=False)
results_4 = results_3.replace('{"label": {"type": "literal", "xml:lang": "ja", "value": "', '')
results_5 = results_4.replace('"}}', '')
results_7 = results_5.replace('[', '')
results_8 = results_7.replace(']', '')# ここまで文字削除
results_6 = results_8.split(', ')

for item in results_6:
    print(item)

#含むものを摘出
