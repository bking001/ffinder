სტარტი

# პროდაქშენის ოპტიმიზაცია
php artisan optimize

# ssh terminal ის  გაშვება ssh ტერმინალისთვის ერთჯერადად
cd terminal && npm run start

# ოპერაციული სისტემის სტარტაპში დამატენა ssh terminal ის დასტარტვა 
cd /var/www/html/Finder/terminal
sudo npm install forever-monitor
forever start index.js
 
# ფოლდერის დალინკვა 
php artisan storage:link

# ფერმიშენები იმ ფოლდერის სადაც არის პროექტი
chmod 777  ფოლდერის პაჩი 
chmod 777  /var/www/html/Finder/
chmod 777 /var/www/html/Finder/public/storage/chat/
 
sudo chown -R apache:apache /var/www/html/Finder
sudo chmod -R 775 /var/www/html/Finder/storage /var/www/html/Finder/bootstrap/cache
sudo systemctl restart httpd

 
sudo chown -R apache:apache /var/www/html/Finder/
sudo chown -R apache:apache /var/www/html/Finder/public/storage/chat/
php artisan storage:link
npm audit fix

# შედულერის დასტარტვა სულ უნდა იყოს გაშვებული 
php artisan schedule:work   

# შედულერის დასტარტვა სამუდამოდ კრონში ვამატებთ ამ ჩანაწერს
* * * * * cd /var/www/html/Finder && php artisan schedule:run >> /dev/null 2>&1

# ერთჯერადად კონკრეტული რომელიმე შედულერის კომანდის გაშვება რო ნახო როგორ იმუშავებს კრონით 
php artisan app:clone-onu-check


# httpd პარამეტრები 

Listen 80

<VirtualHost *:80>
    ServerName finder.airlink.ge
    Redirect permanent / https://finder.airlink.ge/

    # Enable RewriteEngine for further rewrite rules
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

</VirtualHost>


<VirtualHost *:443>
    ServerName finder.airlink.ge
    DocumentRoot /var/www/html/Finder/public
    SSLEngine on
    SSLCertificateFile /etc/ssl/airlink.crt
    SSLCertificateKeyFile /etc/ssl/airlink.key
   
    # Set timeout values (adjust as needed)
    Timeout 300
    ProxyTimeout 300

    #ProxyPass "/vite" "http://finder.airlink:5173/"
    #ProxyPassReverse "/vite" "http://finder.airlink:5173/"

    #ProxyPass "/ssh" "http://finder.airlink:2222/"
    #ProxyPassReverse "/ssh" "http://finder.airlink:2222/"
</VirtualHost>


# php.ini პარამეტრები 

max_execution_time = 120
max_input_time = 60
memory_limit = 128M
file_uploads = On
upload_max_filesize = 20M
max_file_uploads = 20
allow_url_fopen = On


 

  