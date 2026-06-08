<?php

/**
 * ---------------------------------------------------------
 * ANKETA SYSTEM BOOTSTRAP
 * ---------------------------------------------------------
 * Centralno učitavanje:
 * - session
 * - database
 * - constants
 * - helpers
 * - module helpers
 * - timezone
 * - app config
 * ---------------------------------------------------------
 */

/*
|--------------------------------------------------------------------------
| OUTPUT BUFFER
|--------------------------------------------------------------------------
*/

ob_start();

/*
|--------------------------------------------------------------------------
| SESSION
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| ERROR REPORTING
|--------------------------------------------------------------------------
| Lokalno: prikazuj greške
| Produkcija: loguj greške
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);

if (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['SERVER_NAME'] === '127.0.0.1'
) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    define('APP_ENV', 'local');
} else {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);

    define('APP_ENV', 'production');
}

/*
|--------------------------------------------------------------------------
| MYSQLI STRICT MODE
|--------------------------------------------------------------------------
*/

mysqli_report(
    MYSQLI_REPORT_ERROR |
        MYSQLI_REPORT_STRICT
);

/*
|--------------------------------------------------------------------------
| ROOT PATH
|--------------------------------------------------------------------------
| Apsolutna putanja projekta
|--------------------------------------------------------------------------
*/

define(
    'ROOT_PATH',
    dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| PUBLIC PATHS
|--------------------------------------------------------------------------
*/

define(
    'UPLOADS_PATH',
    ROOT_PATH . '/uploads'
);

define(
    'MODULES_PATH',
    ROOT_PATH . '/modules'
);

define(
    'HELPERS_PATH',
    ROOT_PATH . '/helpers'
);

define(
    'LAYOUTS_PATH',
    ROOT_PATH . '/layouts'
);

define(
    'PARTIALS_PATH',
    ROOT_PATH . '/partials'
);

/*
|--------------------------------------------------------------------------
| BASE URL AUTO DETECTION
|--------------------------------------------------------------------------
*/

$https =
    (!empty($_SERVER['HTTPS']) &&
        $_SERVER['HTTPS'] !== 'off')
    || ($_SERVER['SERVER_PORT'] ?? 80) == 443;

$protocol = $https
    ? 'https://'
    : 'http://';

$host = $_SERVER['HTTP_HOST'];

/*
|--------------------------------------------------------------------------
| DETECT PROJECT FOLDER
|--------------------------------------------------------------------------
*/

$scriptPath = str_replace(
    '\\',
    '/',
    dirname($_SERVER['SCRIPT_NAME'])
);

/*
|--------------------------------------------------------------------------
| REMOVE MODULE PATHS
|--------------------------------------------------------------------------
*/

$removeParts = [
    '/modules',
    '/admin',
    '/ajax',
    '/api',
    '/user'
];

$basePath = $scriptPath;

foreach ($removeParts as $part) {

    if (strpos($basePath, $part) !== false) {

        $basePath = strstr(
            $basePath,
            $part,
            true
        );

        break;
    }
}

$basePath = trim($basePath, '/');

/*
|--------------------------------------------------------------------------
| BASE URL
|--------------------------------------------------------------------------
*/

define(
    'BASE_URL',
    rtrim(
        $protocol .
            $host .
            (
                $basePath
                ? '/' . $basePath
                : ''
            ),
        '/'
    )
);


/*
|--------------------------------------------------------------------------
| APP INFO
|--------------------------------------------------------------------------
*/

define('APP_NAME', 'Interni informacioni sistem');

define('APP_SHORT_NAME', 'IIS');

define('APP_VERSION', '1.0.0');

define('APP_TIMEZONE', 'Europe/Belgrade');

define('APP_URL', rtrim(BASE_URL, '/'));

define('APP_DEBUG', APP_ENV === 'local');

/*
|--------------------------------------------------------------------------
| TIMEZONE
|--------------------------------------------------------------------------
*/

date_default_timezone_set(
    APP_TIMEZONE
);

/*
|--------------------------------------------------------------------------
| DATABASE CONFIG
|--------------------------------------------------------------------------
*/

require_once ROOT_PATH . '/config/env.php';

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
|--------------------------------------------------------------------------
*/

$conn = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);

$conn->set_charset('utf8mb4');

/*
|--------------------------------------------------------------------------
| GLOBAL HELPERS
|--------------------------------------------------------------------------
*/

require_once HELPERS_PATH . '/auth.php';

require_once HELPERS_PATH . '/csrf.php';

require_once HELPERS_PATH . '/helpers.php';

require_once HELPERS_PATH . '/response.php';

require_once HELPERS_PATH . '/redirect.php';

require_once HELPERS_PATH . '/permissions.php';

// require_once HELPERS_PATH . '/validation.php';

// require_once HELPERS_PATH . '/formatting.php';

require_once HELPERS_PATH . '/audit.php';

require_once HELPERS_PATH . '/request.php';

require_once HELPERS_PATH . '/url.php';

require_once HELPERS_PATH . '/security.php';



/*
|--------------------------------------------------------------------------
| ASSET MODULE HELPERS
|--------------------------------------------------------------------------
*/

if (
    file_exists(
        MODULES_PATH .
            '/assets/helpers/assets.php'
    )
) {
    require_once
        MODULES_PATH .
        '/assets/helpers/assets.php';
}

if (
    file_exists(
        MODULES_PATH .
            '/assets/helpers/assignments.php'
    )
) {
    require_once
        MODULES_PATH .
        '/assets/helpers/assignments.php';
}

if (
    file_exists(
        MODULES_PATH .
            '/assets/helpers/categories.php'
    )
) {
    require_once
        MODULES_PATH .
        '/assets/helpers/categories.php';
}

if (
    file_exists(
        MODULES_PATH .
            '/assets/helpers/types.php'
    )
) {
    require_once
        MODULES_PATH .
        '/assets/helpers/types.php';
}

/*
|--------------------------------------------------------------------------
| HR MODULE HELPERS (buduće)
|--------------------------------------------------------------------------
*/

if (
    file_exists(
        MODULES_PATH .
            '/hr/helpers/attendance.php'
    )
) {
    require_once
        MODULES_PATH .
        '/hr/helpers/attendance.php';
}

/*
|--------------------------------------------------------------------------
| SURVEY MODULE HELPERS
|--------------------------------------------------------------------------
*/

if (
    file_exists(
        MODULES_PATH .
            '/survey/helpers/survey.php'
    )
) {
    require_once
        MODULES_PATH .
        '/survey/helpers/survey.php';
}

/*
|--------------------------------------------------------------------------
| SECURITY HEADERS
|--------------------------------------------------------------------------
*/

header('X-Frame-Options: SAMEORIGIN');

header('X-Content-Type-Options: nosniff');

header('Referrer-Policy: strict-origin-when-cross-origin');

header('Content-Type: text/html; charset=utf-8');

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(
        random_bytes(32)
    );
}

/*
|--------------------------------------------------------------------------
| COMMON GLOBALS
|--------------------------------------------------------------------------
*/

$GLOBALS['conn'] = $conn;

/*
|--------------------------------------------------------------------------
| AUTOLOAD (kasnije za services/classes)
|--------------------------------------------------------------------------
*/

spl_autoload_register(function ($class) {

    $paths = [

        ROOT_PATH . '/classes/',

        ROOT_PATH . '/modules/assets/services/',

        ROOT_PATH . '/modules/hr/services/',

        ROOT_PATH . '/modules/survey/services/',
    ];

    foreach ($paths as $path) {

        $file =
            $path .
            $class .
            '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

/*
|--------------------------------------------------------------------------
| APPLICATION READY
|--------------------------------------------------------------------------
*/