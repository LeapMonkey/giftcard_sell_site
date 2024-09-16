# giftcard_sell_site
This PHP website is using for collect Gift_card code from Sellers

## 1 this site use old php version please download XAMPP to set up the development environment
- XAMPP FOR php 5.6 version downloand url('https://jaist.dl.sourceforge.net/project/xampp/XAMPP%20Windows/5.6.14/xampp-win32-5.6.14-4-VC11-installer.exe?viasf=1')

## DATABASE confirguration file is database.sql

## This php is using old version thinkphp 3.2.0  framework

Thinkphp(https://www.thinkphp.cn/)

## Apache or ngnix should configure like 

for ngnix
<code>
location / {

if (!-e $request_filename) {

rewrite ^(.*)$ /index.php?s=$1 last;

break;

}

}
</code>
for apache
<code>
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?$1 [L,QSA]    
</IfModule>
</code>

## admin dashboard： 

http://localhost/Sqnbcadmin
用户：admin  密码 www.ohbbs.cn

## front-end user account： 

http://localhost/
account：13800000000 / password: 8373185
