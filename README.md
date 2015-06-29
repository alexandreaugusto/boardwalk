# boardwalk
Final project of the Agile Design Software discipline.

### Follow the steps below in order to install this application

#### System requirements

- PHP 5.5.9 +
- Composer
- CUrl PHP Module

#### Steps

Initially, run the command below to download needed third libraries

```
composer install
```

After it, edit existing file web/.htaccess to map the rewrite base of your application, like this, for example:

    <IfModule mod_rewrite.c>
            Options -MultiViews

            RewriteEngine On
            RewriteBase /
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^ index.php [QSA,L]
    </IfModule>