# -*- coding: utf-8 -*-
from SPARQLWrapper import JSON, SPARQLWrapper
import io, sys
import re
import json
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8")
sys.stdin = io.TextIOWrapper(sys.stdin.buffer, encoding="utf-8")

args = sys.argv
word = args[1]

sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
sparql.setReturnFormat(JSON)
sparql.setQuery("""
    PREFIX resource:  <http://ja.dbpedia.org/resource/>
    PREFIX wikiPageWikiLink: <http://dbpedia.org/ontology/>
    PREFIX wikiPageUsesTemplate: <http://ja.dbpedia.org/property/>
    select distinct ?o where {{
    {{
    resource:{0} wikiPageWikiLink:wikiPageWikiLink ?o .
    }}
    }}
""".format(word))

results_2 = sparql.query().convert()
results_4 = json.dumps(results_2["results"]["bindings"],ensure_ascii=False)
results_4 = results_4.replace('{"p": {"type": "uri", "value": "http://dbpedia.org/ontology/', '')
results_4 = results_4.replace('{"p": {"type": "uri", "value": "http://ja.dbpedia.org/property/', '')
results_4 = results_4.replace('{"o": {"type": "uri", "value": "http://ja.dbpedia.org/resource/', '')
results_4 = results_4.replace('"}}', '')
results_4 = results_4.replace('Template:', '')
results_4 = results_4.replace('Category:', '')
results_4 = results_4.replace('[', '')
results_4 = results_4.replace(']', '')# ここまで文字削除
results_6 = results_4.split(', ')

for item in results_6:
    if word != item and item != '複数の問題' and item != '言葉を濁さない' and item != '存命人物の出典明記' and item != '脚注ヘルプ' and item != 'ActorActress' and item != '要出典' and item != 'Refnest' and item != 'Seealso' and item != 'Authority_control' and item != 'Kinejun_name':
        if item != 'Allcinema_name' and item != 'Imdb_name' and item != '更新' and item != 'Jmdb_name' and item != 'Main' and item != 'Navboxes' and item != 'Reflist' and item != 'See_also' and item != 'Commonscat' and item != '出典の明記':
            if item != 'Cit' and item != '特筆性' and item != 'Cite' and item != '節stub' and item != '要出典範囲' and item != '要検証範囲' and item != 'Otheruses' and item != 'See_also' and item != 'Commonscat' and item != '出典の明記':
                if item != '人名の曖昧さ回避' and item != '特筆性' and item != 'Cite' and item != '節stub' and item != '要出典範囲' and item != '要検証範囲' and item != 'Otheruses' and item != 'See_also' and item != 'Commonscat' and item != '出典の明記':
                    if ('ファイル:' not in item) and ('Infobox_' not in item) and ('_box' not in item) and ('N:' not in item):
                        print(item)

#目的語取得
#ファイル:Shintarō_Abe_with_his_oldest_son_Hironobu_in_1956.jpg
