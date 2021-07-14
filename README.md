# Clinical Trial Web app
Create the following files:
- class/Config.php
- .htaccess


## Config.php
```
ini_set('display_errors', 2);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ERROR | E_WARNING);
error_reporting(E_ALL);

class Config {
    const TITLE = 'TITLE OF YOUR STUDY';
    const SITEVERSION = 'TEST OR EMPTY';
	  const PATH_SEP = 'THE PHYSICAL PATH SEPARATOR';
	  const PATH_RELATIVE = 'THE URL PATH SEPARATOR';
    const PHYSICAL_PATH = '';
    const DOMAIN_URL = '';
    const URL_RELATIVE = '';
    const DBSERVERNAME = '';
 	  const DBNAME = '';
    const DBUSERNAME = '';
    const DBPASSWORD = '';
    const USE_HTTPS = false;
    const USE_SECURE_COOKIES = false;
    const UNDER_MAINTENANCE = false;
    const KEY_URL = '';
    const PW_PATIENT = '';
    const PW_USER = '';
    const PW_IV = '';
    const LANGUAGEISO = '';
}
```
## .htaccess
```
RewriteEngine on
RewriteBase /APP FOLDER/

RewriteRule ^$ pagetemplate.php?area=investigator&pg=login

RewriteRule ^([A-Za-z]+)/error/([0-9]+)$ error.php?area=$1&err=$2
RewriteRule ^([A-Za-z]+)/error/([A-Za-z0-9=]+)$ error.php?area=$1&aes=$2

RewriteRule ^([A-Za-z]+)/ajax_auto/([A-Za-z0-9_]+)/([A-Za-z0-9]+)?$ pagetemplate.php?area=$1&pg=ajax_auto&at=$2&av=$3

RewriteRule ^([A-Za-z]+)/([A-Za-z0-9-_]+)$ pagetemplate.php?area=$1&pg=$2
RewriteRule ^([A-Za-z]+)/([A-Za-z0-9-_]+)/([A-Za-z0-9=]+)([\/0-9]+)?$ pagetemplate.php?area=$1&pg=$2&aes=$3
RewriteRule ^([A-Za-z]+)([\/]+)?$ pagetemplate.php?area=$1&pg=login


ErrorDocument 400 /investigator/error/400
ErrorDocument 401 /investigator/error/401
ErrorDocument 402 /investigator/error/402
ErrorDocument 403 /investigator/error/403
ErrorDocument 404 /investigator/error/404
ErrorDocument 405 /investigator/error/405
ErrorDocument 406 /investigator/error/406
ErrorDocument 407 /investigator/error/407
ErrorDocument 408 /investigator/error/408
ErrorDocument 409 /investigator/error/409
ErrorDocument 410 /investigator/error/410
ErrorDocument 411 /investigator/error/411
ErrorDocument 412 /investigator/error/412
ErrorDocument 413 /investigator/error/413
ErrorDocument 414 /investigator/error/414
ErrorDocument 415 /investigator/error/415
ErrorDocument 500 /investigator/error/500
ErrorDocument 501 /investigator/error/501
ErrorDocument 502 /investigator/error/502
ErrorDocument 503 /investigator/error/503
ErrorDocument 504 /investigator/error/504
ErrorDocument 505 /investigator/error/505

DirectoryIndex pagetemplate.php       
order deny,allow
allow from *
```


