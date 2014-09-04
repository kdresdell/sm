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
from PIL import Image, ImageFile



def SendToFtp(ImgFileName):
  s = ftplib.FTP(FTP_S,FTP_U,FTP_P) 
  f = open(IMG_PATH+ImgFileName,'rb')               
  s.storbinary('STOR '+ImgFileName, f)       
  f.close()          
  s.quit()





def OtimizeImg(ImgFileName):
  img = Image.open(ImgFileName)

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


F_CATALOG = "WEB.CSV"

FTP_S = "ftp.sportsjmd.com"
FTP_U = "ftpsyncuser"
FTP_P = "monsterinc00"

IMG_SCALE_FACTOR = 0.75
IMG_QUALITY = 50
IMG_PATH = "OPT_IMG/"

WEB_URL_PAHT = "http://www.sportsjmd.com/TMP_IMG/"


# ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~




csv2 = csv.reader(open(F_CATALOG, "r"), delimiter=';')
for row in csv2:
  SKU = row[0]
  P_NAME = row[3]
  S_P_Description = row[5]
  #L_P_Description = row[4]
  #R_Price = row[5]
  #S_Price - row[6]
  Base64_Img = row[16]
  ImgFileName = SKU + "-" + P_NAME + ".jpg"

  print("FileName : ", ImgFileName)
  
  ##
  ## CHANGING CSV HEADER DEPENDING ON...
  ##

  if row[16] != "IMAGE_PRODUIT":
    IMG_INFO = WEB_URL_PAHT + ImgFileName
  else:
  	IMG_INFO = IMAGE_PRODUIT


  decoded_string = base64.b64decode(Base64_Img) 
  
  f = open(ImgFileName, "wb")
  f.write(decoded_string)
  f.close()
  
  OtimizeImg(ImgFileName)
  #SendToFtp(ImgFileName)

  with open('eggs.csv', 'w', newline='') as csvfile:
    CVSOUT = csv.writer(csvfile, delimiter=';')
    
    ##
    ## STRUCTURE DU NOUVEAU FICHIER CSV
    ##

    CVSOUT.writerow([SKU, P_NAME, S_P_Description, IMG_INFO])
   


print("> The End.")

