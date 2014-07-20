#!/usr/bin/python
# -*- coding: utf-8 -*-

import csv
import base64
import datetime, xmlrpclib

from wordpress_xmlrpc import Client
from wordpress_xmlrpc.methods import posts
from wordpress_xmlrpc import WordPressPost
import wordpress_xmlrpc



csv2 = csv.reader(open("WEB.csv", "rb"), delimiter=';')
for row in csv2:
  SKU = row[0]
  Produit = row[3]
  AsciiImg = row[16]
  Description = row[5]
  FileName = SKU + "-" + Produit + ".jpg"

decoded_string = base64.b64decode(AsciiImg)

f = open(FileName, "w")
f.write(decoded_string)
f.close()

print("Description du produit : ", Description)

client = Client('http://192.168.1.78/wordpress/xmlrpc.php', 'kdresdell', 'monsterinc00')
#posts = client.call(posts.GetPosts())
#print(type(posts))
#print(posts[0])

post = WordPressPost()
post.title = 'My post de Ken pour test python et post'
post.content = 'This is a wonderful blog post about XML-RPC.'
post.id = client.call(posts.NewPost(post))
post.post_status = 'publish'
client.call(posts.EditPost(post.id, post))


print("> The End.")

