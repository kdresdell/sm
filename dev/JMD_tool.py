#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import base64
import datetime



csv2 = csv.reader(open("WEB.CSV", "r"), delimiter=';')
for row in csv2:
  SKU = row[0]
  Produit = row[3]
  AsciiImg = str(row[16])
  type(AsciiImg)
  Description = row[5]
  FileName = SKU + "-" + Produit + ".jpg"

  print("Description du produit : ", SKU)
  print("FileName is ", FileName)
  
  decoded_string = base64.b64decode(AsciiImg) 
  print("-->",decoded_string)

  f = open(FileName, "wb")
  f.write(decoded_string)
  f.close()

print("> The End.")

