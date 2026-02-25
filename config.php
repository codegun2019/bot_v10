<?php

/* Configuration of the site */
define('DATABASE_SERVER',   'localhost');
define('DATABASE_USERNAME', 'root');
define('DATABASE_PASSWORD', 'root');
define('DATABASE_NAME',     'bio');
define('SITE_URL',          'http://localhost/bot_v10/');

/* ImgBB API Key */
define('IMGBB_API_KEY',     '768b1db02883640d5b9dbc2e61e63d5a');

/* Only for local development - Set to 0 if you get "SSL certificate problem" errors */
define('IMGBB_SSL_VERIFY', 0);

/* Only modify this if you want to use redis for caching instead of the default file system caching */
define('REDIS_IS_ENABLED', 0);
define('REDIS_SOCKET_PATH', null);
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_PASSWORD', null);
define('REDIS_DATABASE', 0);
define('REDIS_TIMEOUT', 2);


