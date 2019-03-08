import csv 

xmlFile = 'test_data.xml'
xmlData = open(xmlFile, 'w')
xmlData.write('<tweets>' + '\n')

with open('test_data.csv', newline='', encoding='ISO-8859-1') as csvfile:
    csv_reader = csv.reader(csvfile)
    for row in csv_reader:
       xmlData.write('    <tweet>' + '\n')
       xmlData.write('        <text>')
       xmlData.write(row[5])
       xmlData.write('</text>' + '\n')
       xmlData.write('        <sentiment>')
       xmlData.write(row[0])
       xmlData.write('</sentiment>' + '\n')
       xmlData.write('    </tweet>' + '\n')
xmlData.write('</tweets>')
xmlData.close()
