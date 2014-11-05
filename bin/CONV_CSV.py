#!/usr/bin/python
# -*- coding: utf-8 -*-

##
##
## sudo apt-get install python-pip
## sudo pacman -S python-pip

# sudo pip install Pillow

import shutil
import sys
import os
import csv
import base64
import datetime
import re
import time
from PIL import Image, ImageFile




def OtimizeImg(ImgFileName):
  img = Image.open(TMP_PATH+ImgFileName)

  # get the image's width and height in pixels
  width, height = img.size
  print("W is : ", width)
  print ("H is : ", height)
  
  # get the largest dimension
  max_dim = max(img.size)

  # resize the image using the largest side as dimension
  R = IMG_SCALE_FACTOR
  s_w = int(R*width)
  s_h = int(R*height)

  img = img.resize((s_w, s_h), Image.ANTIALIAS)
  img.save(IMG_PATH+ImgFileName, quality=IMG_QUALITY, optimize=True, progressive=True)


# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
#   INPUTS
# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

#FileName = "WEB.CSV"
IMG_SCALE_FACTOR = 1
IMG_QUALITY = 60
#IMG_PATH = "/usr/share/nginx/html/TMP_IMG/"
IMG_PATH = "/home/kdresdell/Desktop/TMP_IMG/"
WEB_URL_PAHT = "http://www.sportsjmd.com/TMP_IMG/"
CSV_PATH = "/home/kdresdell/Desktop/"
TMP_PATH = "/home/kdresdell/Desktop/TMP/"


# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

shutil.rmtree(TMP_PATH, ignore_errors=True)
os.mkdir(TMP_PATH,0755)

## dd/mm/yyyy format
print (time.strftime("%Y%m%d"))
TSTAMP = (time.strftime("%Y%m%d"))

if len(sys.argv)>1:
  InputFileName = sys.argv[1]
  OutputFileName = CSV_PATH +"CLEAN_" + TSTAMP + ".csv" 

  csv.field_size_limit(1000000000)

  with open(OutputFileName, 'wb') as csvfile:
    CVSOUT = csv.writer(csvfile, delimiter=';', quoting=csv.QUOTE_MINIMAL)

    # SI JE VEUX METTRE LES HEADERS DANS LE CSV
    #

    CVSOUT.writerow(["CODE","DOMAINE","THEME","PRODUIT","MARQUE_MODELE", "GENRE", "DESCRIPTION_1", "DESCRIPTION_2",
      	             "DESCRIPTION_3", "DESCRIPTION_4", "FORMAT","PRIX_DETAIL","QT_POUR_RABAIS","PRIX_VENTE","TYPE_RABAIS",
                     "DIM_1","DIM_2","DIM_3","POID_KG","F_NON_TRANSPORTABLE","DISPO_INVENTAIRE", "IMG_URL"])

    csv2 = csv.reader(open(InputFileName, "r"), delimiter=';')
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
  
      ImgFileName = "JMD-" + CODE + ".jpg"

      print("Image File name is ", ImgFileName)
  
      decoded_string = base64.b64decode(IMAGE_B64) 
  
      f = open(TMP_PATH +ImgFileName, "wb")
      f.write(decoded_string)
      f.close()
  
      OtimizeImg(ImgFileName)

      IMG_URL = WEB_URL_PAHT + ImgFileName

      CVSOUT.writerow([CODE,DOMAINE,THEME,PRODUIT,MARQUE_MODELE,GENRE,DESCRIPTION_1,DESCRIPTION_2,DESCRIPTION_3,
        	           DESCRIPTION_4,FORMAT,PRIX_DETAIL,QT_POUR_RABAIS,PRIX_VENTE,TYPE_RABAIS,
    	               DIM_1,DIM_2,DIM_3,POID_KG,F_NON_TRANSPORTABLE,DISPO_INVENTAIRE,IMG_URL])


  shutil.rmtree(TMP_PATH, ignore_errors=True)

else :
  print("Error this script need an argument")
  sys.exit()