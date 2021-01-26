from sklearn.feature_extraction.text import CountVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from SPARQLWrapper import JSON, SPARQLWrapper
import re

def get_synonyms(text):
    uri = '<http://ja.dbpedia.org/resource/{0}>'.format(text)

    sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
    sparql.setReturnFormat(JSON)
    sparql.setQuery('''
        SELECT DISTINCT *
        WHERE {{
            {{ ?redirect <http://dbpedia.org/ontology/wikiPageRedirects> {0} }}
            UNION
            {{ {0} <http://dbpedia.org/ontology/wikiPageRedirects> ?redirect }}
            ?redirect <http://www.w3.org/2000/01/rdf-schema#label> ?synonym
        }}
    '''.format(uri))

    results = []
    for x in sparql.query().convert()['results']['bindings']:
        word = x['synonym']['value']
        results.append({'':word})
    return results

def get_info(text):
    uri = '<http://ja.dbpedia.org/resource/{}>'.format(text)

    sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
    sparql.setReturnFormat(JSON)
    sparql.setQuery('''
        SELECT DISTINCT *
        WHERE {{
            {} <http://dbpedia.org/ontology/abstract> ?summary
        }}
    '''.format(uri))
    results = sparql.query().convert()
    return results

def get_match(text):
    sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
    sparql.setReturnFormat(JSON)
    sparql.setQuery('''                                                                                                                                                                      
        PREFIX dbpedia:  <http://dbpedia.org/ontology/>
        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
        PREFIX category: <http://ja.dbpedia.org/resource/Category:>

        select distinct ?artist where {{
            ?artist dbpedia:wikiPageWikiLink category:{0}.
        }}LIMIT 5
    '''.format(text))
    results = sparql.query().convert()
    return results

def get_category(text):
    sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
    sparql.setReturnFormat(JSON)

    if '(' in text or '!' in text or ':' in text or '/' in text \
            or ';' in text or '{' in text or ',' in text or '.' in text:
        text_2 = re.sub(r"[,.!?:;'()/{}]", " ", text)
        text_2 = text_2.replace('  ', ' ')
        text_4 = text_2.split(' ')

        sparql.setQuery("""
        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
        PREFIX resource: <http://ja.dbpedia.org/resource/>
        select distinct ?category where {{
        ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
        FILTER (regex (?a, "{0}") && regex (?a, "{1}"))
        }}
        """.format(text_4[0], text_4[1]))

    else:
        sparql.setQuery("""
        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
        PREFIX resource: <http://ja.dbpedia.org/resource/>
        select distinct ?category where {{
        resource:{0} <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
        }}
        """.format(text))

    results = sparql.query().convert()
    return results

def get_category_2(text):
    sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
    sparql.setReturnFormat(JSON)
    sparql.setQuery("""
            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
            PREFIX resource: <http://ja.dbpedia.org/resource/>
            select distinct ?category where {{
            ?a <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?category .
            FILTER (regex (?a, "{0}"))
            }}
            """.format(text))
    results = sparql.query().convert()
    return results

def get_partial_match(text):
    sparql = SPARQLWrapper('http://ja.dbpedia.org/sparql')
    sparql.setReturnFormat(JSON)

    sparql = SPARQLWrapper(endpoint='http://ja.dbpedia.org/sparql', returnFormat='json')
    sparql.setQuery("""
        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
        select distinct ?label where {{
        ?a rdfs:label ?label.
        FILTER (regex (?label, "^{0} ") || (regex (?label, "^{0}") && regex (?label, "{0}$")))
    }}
    """.format(text))
    results = sparql.query().convert()
    return results


#Wikipediaにある情報しか使えない
#時間がかかりすぎる、情報が多すぎるのかな