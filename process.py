import sys
#print ('Number of arguments:', len(sys.argv), 'arguments.')
#print ('Argument List:', sys.argv)

#print("Hello World!")

with open('/var/www/html/images/'+sys.argv[1]) as infile:
    lines=0
    words=0
    characters=0
    for line in infile:
        wordslist=line.split()
        lines=lines+1
        words=words+len(wordslist)
        characters += sum(len(word) for word in wordslist)
print(characters)
