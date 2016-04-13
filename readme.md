## Tik Tok

Tik Tok is an attendance Marking application.

## Setup

1. composer update
2. php artisan key:generate
3. Give permission to Folders, Bootstrap, Storage, Vendor.
4. Make host file entry:

    127.0.0.1 mx.tiktok.org

5. Make vhost file entry:

    <VirtualHost mx.tiktok.org>
        DocumentRoot "C:/xampp/htdocs/TikTok/public"
        ServerName mx.tiktok.org
    </VirtualHost>

6. Uncomment extension=php_pdo_mysql.dll in 'php.ini' file by removing ';'

## Contributing

Thank you for considering contributing to the TikTok App!

## Security Vulnerabilities

If you discover a security vulnerability within TikTok, please send an e-mail to Nikhil Baby at nikhilbaby2000@gmail.com. All security vulnerabilities will be promptly addressed.

### License

The TikTok Application is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
