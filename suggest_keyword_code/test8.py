# -*- coding: utf-8 -*-
from SPARQLWrapper import JSON, SPARQLWrapper

import json
import sys
import re

args = sys.argv
word = args[1]

text_2 = re.sub(r"[,.!?:;'()/{}]", " ", word)
text_2 = text_2.replace('_', ' ')
text_2 = text_2.replace('  ', ' ')
text_2 = text_2.replace('  ', ' ')
text_2 = text_2.replace('  ', ' ')
text_4 = text_2.split(' ')
length = len(text_4)

sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
sparql.setReturnFormat(JSON)

if length == 1:
    sparql.setQuery("""
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX resource: <http://ja.dbpedia.org/resource/>
    select distinct ?category where {{
    resource:{0} <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
    }}
    """.format(text_4[0]))

if length == 2:
    sparql.setQuery("""
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX resource: <http://ja.dbpedia.org/resource/>
    select distinct ?category where {{
    ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
    FILTER (regex (?a, "{0}") && regex (?a, "{1}"))
    }}
    """.format(text_4[0], text_4[1]))

if length == 3:
    sparql.setQuery("""
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX resource: <http://ja.dbpedia.org/resource/>
    select distinct ?category where {{
    ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
    FILTER (regex (?a, "{0}") && regex (?a, "{1}") && regex (?a, "{2}"))
    }}
    """.format(text_4[0], text_4[1], text_4[2]))

if length == 4:
    sparql.setQuery("""
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX resource: <http://ja.dbpedia.org/resource/>
    select distinct ?category where {{
    ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
    FILTER (regex (?a, "{0}") && regex (?a, "{1}") && regex (?a, "{2}") && regex (?a, "{3}"))
    }}
    """.format(text_4[0], text_4[1], text_4[2], text_4[3]))

if length == 5:
    sparql.setQuery("""
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX resource: <http://ja.dbpedia.org/resource/>
    select distinct ?category where {{
    ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
    FILTER (regex (?a, "{0}") && regex (?a, "{1}") && regex (?a, "{2}") && regex (?a, "{3}") && regex (?a, "{4}"))
    }}
    """.format(text_4[0], text_4[1], text_4[2], text_4[3], text_4[4]))

results = sparql.query().convert()
results_4 = json.dumps(results["results"]["bindings"], ensure_ascii=False)
results_4 = results_4.replace('{"category": {"type": "uri", "value": "', '')
results_4 = results_4.replace('"}}', '')
results_4 = results_4.replace('[', '')
results_4 = results_4.replace(']', '')  # ここまで文字削除
results_6 = results_4.split(', ')

for item in results_6:
    if 'http://dbpedia.org/ontology/' in item:
        if 'Wikidata' not in item:
            item_change = item.replace('http://dbpedia.org/ontology/', '')
            print(item_change)

#if __name__ == '__main__':
#    category = dbpediaknowledge.get_category(word)
#    results_3 = json.dumps(category["results"]["bindings"], ensure_ascii=False)
#    results_4 = results_3.replace('{"category": {"type": "uri", "value": "', '')
#    results_5 = results_4.replace('"}}', '')
#    results_7 = results_5.replace('[', '')
#    results_8 = results_7.replace(']', '')  # ここまで文字削除
#    results_6 = results_8.split(', ')
#    for item in results_6:
#        if 'http://dbpedia.org/ontology/' in item:
#            item_change = item.replace('http://dbpedia.org/ontology/', '')
#            print(item_change)