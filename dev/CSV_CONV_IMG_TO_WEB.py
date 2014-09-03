#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import base64
import datetime
import ftplib



F_CATALOG = "WEB.CSV"

FTP_S = "ftp.sportsjmd.com"
FTP_U = "ftpsyncuser"
FTP_P = "monsterinc00"



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

  print("FileName is ", ImgFileName)
  
  decoded_string = base64.b64decode(Base64_Img) 
  
  f = open(ImgFileName, "wb")
  f.write(decoded_string)
  f.close()

  s = ftplib.FTP(FTP_S,FTP_U,FTP_P) 
  f = open(ImgFileName,'rb')                # file to send
  s.storbinary('STOR '+ImgFileName, f)         # Send the file

  f.close()                                # Close file and FTP
  s.quit()






print("> The End.")

