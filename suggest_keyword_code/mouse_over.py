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
    select distinct ?p ?o where {{
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
results_4 = json.dumps(results_2["results"]["bindings"],ensure_ascii=False)
results_4 = results_4.replace('{"p": {"type": "uri", "value": "http://dbpedia.org/ontology/', '')
results_4 = results_4.replace('{"p": {"type": "uri", "value": "http://ja.dbpedia.org/property/', '')
results_4 = results_4.replace('"}, "o": {"type": "literal", "xml:lang": "ja", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "uri", "value": "http://ja.dbpedia.org/resource/', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://dbpedia.org/datatype/centimetre", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://www.w3.org/2001/XMLSchema#date", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://www.w3.org/2001/XMLSchema#integer", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://www.w3.org/2001/XMLSchema#gMonthDay", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://www.w3.org/2001/XMLSchema#nonNegativeInteger", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://www.w3.org/2001/XMLSchema#double", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://www.w3.org/2001/XMLSchema#gYear", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "uri", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "uri", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "typed-literal", "datatype": "http://dbpedia.org/datatype/kilogram", "value": "', '::')
results_4 = results_4.replace('"}, "o": {"type": "literal", "value": "', '::')
results_4 = results_4.replace('"}}', '')
results_4 = results_4.replace('[', '')
results_4 = results_4.replace(']', '')# ここまで文字削除

results_6 = results_4.split(', ')

for item in results_6:
    print(item)

#完全一致で年齢、出生地などのサジェストキーワードが提示できる、だけ
