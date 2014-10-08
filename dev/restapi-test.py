#!/usr/bin/python
# -*- coding: utf-8 -*-

##
##
## sudo apt-get install python-pip
## sudo pacman -S python-pip

# sudo pip install Pillow

import util
import json
import urllib
import requests


U_ID = "ck_924108a0eed17226f061c654eea68d25"
U_KEY = "cs_50848f449b04465cdb191d537030ca6b"

response = requests.get('https://www.sportsjmd.com/wc-api/v2/orders',
                         auth=(U_ID, U_KEY))
data = response.json()



print data

# Set the request URL

#url = 'http://www.sportsjmd.com/wcapi/v2/Albums/?artist=' + urllib.quote_plus(artistName) + '&format=json'

# Send the GET request

#resp = util.request(url, U_ID, U_KEY)

# Interpret the JSON response 

#data = json.loads(resp.decode('utf8'))

# Get the collection of albums from the result set

#albums = data['ResultSet']

# Output the search results

#for album in albums:
#    print(album['Name'] + ' - ' + album['Artists'])