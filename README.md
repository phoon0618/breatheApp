Breathe is an application designed for time management while supporting the human fundamental need to slow down. 

Required Tools for demo
- XAMPP (https://www.apachefriends.org/download.html)
- composer (https://getcomposer.org/download/)

Getting Started
1. Download the file and move it in the htdocs of xampp
2. Download  https://curl.haxx.se/docs/caextract.html
3. Put it in C:\xampp\php\extras\ssl\cacert.pem 
4. Add this to php.ini: curl.cainfo = "C:\xampp\php\extras\ssl\cacert.pem". In your XAMPP control panel, click on config button of Apache and you can see PHP(php.ini) as an option. Add that line at the end of the file
5. In command prompt, direct the path to the folder and run 'composer require google/apiclient'
6. Start xampp
7. Running the following link in your web browser or locate the file in the xampp/htdocs and set the path as required.
http://localhost/breatheApp/LoginPage.php
