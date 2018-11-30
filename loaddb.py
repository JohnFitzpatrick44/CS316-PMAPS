import csv

cf = open('base.csv')
csv_f = csv.reader(cf)

f = open('load.sql', 'w')

timestamp = []
name = []
description = []
location = []
relevant = []
category = []

for row in csv_f:
    timestamp.append(row[0])
    name.append(row[1])
    description.append(row[3])
    location.append(row[4])
    relevant.append(row[5])
    category.append(row[6])

personIDs = []
placeIDs = []
personEntries = []
commentEntries = []
placeEntries = []
relevantEntries = []

personIDs.append('Unknown')

for x in range(1,len(timestamp)):
    if name[x] not in personIDs:
        personIDs.append(name[x])
    if location[x] not in placeIDs:
    	placeIDs.append(location[x])


for x in range(0, len(personIDs)-1):
    personEntries.append('(%d,\'%s\',NULL,NULL),\n' % (x,personIDs[x].replace('\'','\'\'')))
personEntries.append('(%d,\'%s\',NULL,NULL);\n\n' % (len(personIDs)-1, personIDs[-1].replace('\'','\'\'')))

for x in range(0, len(placeIDs) - 1):
	placeEntries.append('(%d,\'%s\',\'%s\',0),\n' % (x, placeIDs[x].split(',')[0], placeIDs[x].split(',')[1]))
placeEntries.append('(%d,\'%s\',\'%s\',0);\n\n' % (len(placeIDs)-1, placeIDs[-1].split(',')[0], placeIDs[-1].split(',')[1]))


for x in range(1,len(timestamp)-1):
	cat = 'general'
	if category[x] == 'Landmark' or category[x] == 'VAM (View Appreciation Moment)' or category[x] == 'Waterfall':
		cat = 'vam'
	if category[x] == 'Pro-Tip!':
		cat = 'tip'
	if category[x] == 'Water':
		cat = 'water'
	if category[x] == 'Safety':
		cat = 'safety'
	if category[x] == 'Campsite':
		cat = 'campsite'
	if category[x] == 'Solos':
		cat = 'solos'
	commentEntries.append('(%d,\'%s\',\'%s\',\'%s\',\'true\',%d,%d),\n' % (x,description[x].replace('\'','\'\'').replace('\n','\\n').replace('"','\\"'),cat,timestamp[x],personIDs.index(name[x]),placeIDs.index(location[x])))
	for month in relevant[x].split(', '):
		relevantEntries.append('(%d,\'%s\'),\n' % (x, month.lower()))


cat = 'general'
if category[-1] == 'Landmark' or category[-1] == 'VAM (View Appreciation Moment)' or category[-1] == 'Waterfall':
	cat = 'vam'
if category[-1] == 'Pro-Tip!':
	cat = 'tip'
if category[-1] == 'Water':
	cat = 'water'
if category[-1] == 'Safety':
	cat = 'safety'
if category[-1] == 'Campsite':
	cat = 'campsite'
if category[-1] == 'Solos':
	cat = 'solos'
commentEntries.append('(%d,\'%s\',\'%s\',\'%s\',\'true\',%d,%d);\n\n' % (len(timestamp)-1,description[-1].replace('\'','\'\''),cat,timestamp[-1],personIDs.index(name[-1]),placeIDs.index(location[-1])))

relevantEntries.append('(%d,\'%s\'),\n' % (len(timestamp)-1, 'august'))
relevantEntries.append('(%d,\'%s\'),\n' % (len(timestamp)-1, 'march'))
relevantEntries.append('(%d,\'%s\');\n' % (len(timestamp)-1, 'step'))



f.write('INSERT INTO Person VALUES\n')
f.writelines(personEntries)

f.write('INSERT INTO Place VALUES\n')
f.writelines(placeEntries)

f.write('INSERT INTO Comment VALUES\n')
f.writelines(commentEntries)

f.write('INSERT INTO RelevantFor VALUES\n')
f.writelines(relevantEntries)


f.close()

