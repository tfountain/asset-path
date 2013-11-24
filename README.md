
This module provides an easy way to version static files (images, stylesheets, javascript etc.), which, when combined with some cache-friendly server configuration, can improve the rendering time of your pages.

## Installation

The easiest way to install the module is using Composer (http://getcomposer.org/). Add to your `composer.json`:

    "require": {
        "tfountain/asset-path": "dev-master"
    }

and then update your application's `application.config.php` to add `TfAssetPath` to your modules array.

You will also need to modify your `mod_rewrite` rules to rewrite versioned asset requests to the non-versioned equivalent. Modify your `.htaccess` file (or vhost), and add:

    # Versioned assets
    RewriteRule ^(images|js|css)/(([\w.-]+/)+)?([\w.-]+)\.[\w]+\.([\w]+)$ $1/$2$4.$5 [L]

above the final rewrite block in the file. So if you're using the `.htaccess` file from the Zend Framework Skeleton application, your modified `.htaccess` would look like this:

    RewriteEngine On
    # The following rule tells Apache that if the requested filename
    # exists, simply serve it.
    RewriteCond %{REQUEST_FILENAME} -s [OR]
    RewriteCond %{REQUEST_FILENAME} -l [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^.*$ - [NC,L]

    # Versioned assets
    RewriteRule ^(images|js|css)/(([\w.-]+/)+)?([\w.-]+)\.[\w]+\.([\w]+)$ $1/$2$4.$5 [L]

    # The following rewrites all other queries to index.php. The
    # condition ensures that if you are using Apache aliases to do
    # mass virtual hosting, the base path will be prepended to
    # allow proper resolution of the index.php file; it will work
    # in non-aliased environments as well, providing a safe, one-size
    # fits all solution.
    RewriteCond %{REQUEST_URI}::$1 ^(/.+)(.+)::\2$
    RewriteRule ^(.*) - [E=BASE:%1]
    RewriteRule ^(.*)$ %{ENV:BASE}index.php [NC,L]

Change `(images|js|css)` to contain a pipe separated list of static file folders you might want to use this module on (i.e. folders you have in your `public` folder).

To see any benefit, you'll need to serve these files with far in the future 'Expires' headers. If you're using Apache, the easiest way to do this is using `mod_expires`. Either modify your vhost (preferred) to add a `<Directory>` block for each folder:

    <Directory /path/to/public/css>
        <IfModule mod_expires.c>
            ExpiresActive On
            ExpiresByType text/css "access plus 60 days"
        </IfModule>
    </Directory>

or add a `.htaccess` file to that folder:

    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css "access plus 60 days"
    </IfModule>

## Usage

The module provides a view helper that will, for a given file path, return the same path with a versioning hash inserted just before the file extension. Use the helper in place of situations where you would otherwise put paths to static files, e.g.:

    <link rel="stylesheet" type="text/css" href="<?=$this->assetPath('/css/styles.css')?>">

this will output:

    <link rel="stylesheet" type="text/css" href="/css/style.mwq02x.css">

(where `mwq02x` is a hash uniquely generated based on the last modified time of the file).

Assuming your expires headers are setup as suggested above, the browser will then cache this file for up to 60 days, and won't rerequest it as the user browses around your site. However if you modify the file, the hash will change automatically, the browser will see it as a different file and rerequest it from the server.
