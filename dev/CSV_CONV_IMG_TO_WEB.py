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


def SendToFtp(ImgFileName):
  s = ftplib.FTP(FTP_S,FTP_U,FTP_P) 
  f = open(IMG_PATH+ImgFileName,'rb')               
  s.storbinary('STOR '+ImgFileName, f)       
  f.close()          
  s.quit()



def OtimizeImg(ImgFileName):
  img = Image.open("ToDelete/"+ImgFileName)

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


F_CATALOG = "INV_JMD.CSV"

FTP_S = "ftp.sportsjmd.com"
FTP_U = "ftpsyncuser"
FTP_P = "monsterinc00"

IMG_SCALE_FACTOR = 1
IMG_QUALITY = 60
IMG_PATH = "OPT_IMG/"

WEB_URL_PAHT = "http://www.sportsjmd.com/OPT_IMG/"


# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

csv.field_size_limit(1000000000)

with open('CLEAN_OUT_FILE.csv', 'wb') as csvfile:
  CVSOUT = csv.writer(csvfile, delimiter=';', quoting=csv.QUOTE_MINIMAL)

#
# SI JE VEUX METTRE LES HEADERS DANS LE CSV
#
  CVSOUT.writerow(["INDEX","DOMAINE","THEME","PRODUIT","MARQUE_MODELE","DESCRIPTION",
    	           "FORMAT","PRIX_DETAIL","QT_POUR_RABAIS","PRIX_VENTE","TYPE_RABAIS",
    	           "DIM_LONGUEUR","DIM_LARGEUR","DIM_HAUTEUR","POID_KG","F_NON_TRANSPORTABLE",
    	           "IMG_URL","DISPO_INVENTAIRE"])


  csv2 = csv.reader(open(F_CATALOG, "r"), delimiter=';')
  for row in csv2:
    INDEX = row[0]
    DOMAINE = row[1]
    THEME = row[2]
    PRODUIT = row[3]
    MARQUE_MODELE = row[4]
    DESCRIPTION = row[5]
    FORMAT = row[6]
    PRIX_DETAIL = row[7]
    QT_POUR_RABAIS = row[8]
    PRIX_VENTE = row[9]
    TYPE_RABAIS = row[10]
    DIM_LONGUEUR = row[11]
    DIM_LARGEUR = row[12]
    DIM_HAUTEUR = row[13]
    POID_KG = row[14]
    F_NON_TRANSPORTABLE = row[15]
    IMAGE_B64 = row[16]
    DISPO_INVENTAIRE = row[17]

    ImgFileName = "JMD-" + INDEX + ".jpg"

    print("Image File name is ", ImgFileName)
  
    decoded_string = base64.b64decode(IMAGE_B64) 
  
    f = open("ToDelete/" +ImgFileName, "wb")
    f.write(decoded_string)
    f.close()
  
    OtimizeImg(ImgFileName)
    #SendToFtp(ImgFileName)

    IMG_URL = WEB_URL_PAHT + ImgFileName

    CVSOUT.writerow([INDEX,DOMAINE,THEME,PRODUIT,MARQUE_MODELE,DESCRIPTION,
    	             FORMAT,PRIX_DETAIL,QT_POUR_RABAIS,PRIX_VENTE,TYPE_RABAIS,
    	             DIM_LONGUEUR,DIM_LARGEUR,DIM_HAUTEUR,POID_KG,F_NON_TRANSPORTABLE,IMG_URL,DISPO_INVENTAIRE])


print("> The End.")

