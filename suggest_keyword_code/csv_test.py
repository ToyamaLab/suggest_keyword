import csv
import pprint
import sys

args = sys.argv
category = args[1]

change = category.split(',')

with open('category.csv') as f:
    reader = csv.reader(f)
    l = [row for row in reader]

num=0
my_list=list()

for item in change:
    for category_csv in l:
        if item == category_csv[0]:
            my_list.append(category_csv[0])

#print(my_list)

for item in my_list:
    for category_csv in l:
        if item == category_csv[0]:
            if category_csv[2]=='1':
                ca_1 = item
            if category_csv[2]=='2':
                ca_2 = item
            if category_csv[2]=='3':
                ca_3 = item
            if category_csv[2]=='4':
                ca_4 = item
            if category_csv[2]=='5':
                ca_5 = item

sorting = ca_1
if 'ca_2' in locals():
    sorting = sorting + ',' + ca_2
if 'ca_3' in locals():
    sorting = sorting + ',' + ca_3
if 'ca_4' in locals():
    sorting = sorting + ',' + ca_4
if 'ca_5' in locals():
    sorting = sorting + ',' + ca_5

print(sorting)