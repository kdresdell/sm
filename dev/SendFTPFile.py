#!/usr/bin/python
# -*- coding: utf-8 -*-


import ftplib


s = ftplib.FTP('ftp.sportsjmd.com','ftpsyncuser','monsterinc00') 

f = open('kitesurf.jpg','rb')                # file to send
s.storbinary('STOR kitesurf.jpg', f)         # Send the file

f.close()                                # Close file and FTP
s.quit()




print("> The End.")

