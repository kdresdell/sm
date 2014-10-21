#!/usr/bin/python
# -*- coding: utf-8 -*-

##
##
## sudo apt-get install python-pip
## sudo pacman -S python-pip

# sudo pip install Pillow

import csv
import base64
import datetime
import ftplib
import re
from PIL import Image, ImageFile


# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
#   INPUTS
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

F_CATALOG = "WEB.CSV"
WEB_URL_PAHT = "http://www.sportsjmd.com/OPT_IMG/"

# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

csv.field_size_limit(1000000000)
csv2 = csv.reader(open(F_CATALOG, "r"), delimiter=';')
for row in csv2:
  CODE = row[0]
  DOMAINE = row[1]
  THEME = row[2]
  PRODUIT = row[3]
  MARQUE_MODELE = row[4]
  GENRE = row[5]
  DESCRIPTION_1 = row[6]
  DESCRIPTION_2 = row[7]
  DESCRIPTION_3 = row[8]
  DESCRIPTION_4 = row[9]
  FORMAT = row[10]
  PRIX_DETAIL = row[11]
  QT_POUR_RABAIS = row[12]
  PRIX_VENTE = row[13]
  TYPE_RABAIS = row[14]
  DIM_1 = row[15]
  DIM_2 = row[16]
  DIM_3 = row[17]
  POID_KG = row[18]
  F_NON_TRANSPORTABLE = row[19]
  DISPO_INVENTAIRE = row[20]
  IMAGE_B64 = row[21]
  print(IMAGE_B64)

print("> The End.")

