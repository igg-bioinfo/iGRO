# Clinical Trial Web app
Create the following files in your web application folder:
- class/Config.php
- .htaccess


## Config.php
Insert the correct value for all the constants
```
ini_set('display_errors', 2);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_PARSE);
error_reporting(E_ERROR | E_WARNING);
error_reporting(E_ALL);

class Config {
    const TITLE = ''; //TITLE OF YOUR STUDY
    const SITEVERSION = ''; //TEST OR EMPTY
	const PATH_SEP = ''; //PHYSICAL PATH SEPARATOR
	const PATH_RELATIVE = ''; //PHYSICAL PATH RELATIVE
    const PHYSICAL_PATH = ''; //PHYSICAL PATH
    const DOMAIN_URL = ''; //URL PATH
    const URL_RELATIVE = ''; //URL PATH RELATIVE
    const DBSERVERNAME = '';
 	const DBNAME = '';
    const DBUSERNAME = '';
    const DBPASSWORD = '';
    const DBADMIN = '';
    const DBADMINPW = '';
    const USE_HTTPS = false;
    const USE_SECURE_COOKIES = false;
    const UNDER_MAINTENANCE = false; //SHOW A HTML PAGE WHEN SITE IS DOWN / UNDER MAINTENANCE
    const KEY_URL = '';
    const PW_IV = '';
    const LANGUAGEISO = 'it';
    const EMAIL_ADMIN = '';
    const ADMIN_CHECK = false;
    const INVESTIGATOR_CAN_UNLOCK_VISIT = false;
    const INVESTIGATOR_CAN_DELETE_VISIT = false;
    const PROJECT_CLOSED = false;
    const FOOTER = ''; //THE FOOTER WHICH WILL BE SHOWN IN ANY PAGE
}
```
## .htaccess
Replace **APP FOLDER** with the name of your web application folder
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


ErrorDocument 400 /APP FOLDER/investigator/error/400
ErrorDocument 401 /APP FOLDER/investigator/error/401
ErrorDocument 402 /APP FOLDER/investigator/error/402
ErrorDocument 403 /APP FOLDER/investigator/error/403
ErrorDocument 404 /APP FOLDER/investigator/error/404
ErrorDocument 405 /APP FOLDER/investigator/error/405
ErrorDocument 406 /APP FOLDER/investigator/error/406
ErrorDocument 407 /APP FOLDER/investigator/error/407
ErrorDocument 408 /APP FOLDER/investigator/error/408
ErrorDocument 409 /APP FOLDER/investigator/error/409
ErrorDocument 410 /APP FOLDER/investigator/error/410
ErrorDocument 411 /APP FOLDER/investigator/error/411
ErrorDocument 412 /APP FOLDER/investigator/error/412
ErrorDocument 413 /APP FOLDER/investigator/error/413
ErrorDocument 414 /APP FOLDER/investigator/error/414
ErrorDocument 415 /APP FOLDER/investigator/error/415
ErrorDocument 500 /APP FOLDER/investigator/error/500
ErrorDocument 501 /APP FOLDER/investigator/error/501
ErrorDocument 502 /APP FOLDER/investigator/error/502
ErrorDocument 503 /APP FOLDER/investigator/error/503
ErrorDocument 504 /APP FOLDER/investigator/error/504
ErrorDocument 505 /APP FOLDER/investigator/error/505

DirectoryIndex pagetemplate.php       
order deny,allow
allow from *
```


