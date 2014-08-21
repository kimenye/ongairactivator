Installing with Phusion
=======================

sudo apt-get install apache2
sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt

sudo nano /etc/apache2/mods-enabled/dir.conf

```
<IfModule mod_dir.c>

	DirectoryIndex index.php index.html index.cgi index.pl index.php index.xhtml index.htm

</IfModule>
```


```
	<VirtualHost *:8001>
        ServerAdmin webmaster@localhost

        DocumentRoot /var/www/ongairactivator/
        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory /var/www/ongairactivator/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                allow from all
        </Directory>

        ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/
        <Directory "/usr/lib/cgi-bin">
                AllowOverride None
                Options +ExecCGI -MultiViews +SymLinksIfOwnerMatch
                Order allow,deny
                Allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.log combined

	    Alias /doc/ "/usr/share/doc/"
	    <Directory "/usr/share/doc/">
	        Options Indexes MultiViews FollowSymLinks
	        AllowOverride None
	        Order deny,allow
	        Deny from all
	        Allow from 127.0.0.0/255.0.0.0 ::1/128
	    </Directory>

	</VirtualHost>
```