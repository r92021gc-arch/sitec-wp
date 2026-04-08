<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// Cargar variables desde archivo .env si existe (permite configurar DB sin exponer secretos)
$sitecEnvPath = __DIR__ . '/.env';
if (is_readable($sitecEnvPath)) {
    $lines = @file($sitecEnvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            if ($trimmed === '' || $trimmed[0] === '#') { continue; }
            $pos = strpos($line, '=');
            if ($pos === false) { continue; }
            $key = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last  = substr($value, -1);
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            if ($key !== '') {
                if (function_exists('putenv')) { @putenv($key . '=' . $value); }
                $_ENV[$key] = $value;
                if (!isset($_SERVER[$key])) { $_SERVER[$key] = $value; }
            }
        }
    }
}

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', getenv('DB_NAME') ?: 'db_sitecweb' );

/** Database username */
define( 'DB_USER', getenv('DB_USER') ?: 'root' );

/** Database password */
define( 'DB_PASSWORD', getenv('DB_PASSWORD') ?: '' );

/** Database hostname */
define( 'DB_HOST', getenv('DB_HOST') ?: 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '1ZymB[$tT^(`y:o*KI(K1N>p&Q&mf6Nt5BwV1IE;h)CV9j/f<8Y#9CzZiF8Q;C3=' );
define( 'SECURE_AUTH_KEY',  ')F6 sF2Y9U;kSHUM8z~=TKMm=AKko[&UrLVkP_7_m$)0gkfPxM5o&nQ<Ruj7(W,a' );
define( 'LOGGED_IN_KEY',    'TZfzlBxwOx/Aj)52s_ZJhdfE+%o:+yMBM7 jnMs`d}d*H(L,vocp4Qw]Mq?gaGhY' );
define( 'NONCE_KEY',        '}N<!Q^7H7j+raai2vfx14/yBHj/wmXqajG@.M#4eo4h$U%dMrUi?Axt0dj#Abf8l' );
define( 'AUTH_SALT',        'St1Wd==V^@ZHqZE>:V9EDCTh1FVl@WKPopfo`llgT9e<s{Vnp9z@0&,8th/[@),Q' );
define( 'SECURE_AUTH_SALT', 'TDY<am_R`KI`Z2`9?(q_es~iMOOI#%#+4%f/bTCCH)f6J1NG 1+~sp*fW%LN3Q5n' );
define( 'LOGGED_IN_SALT',   'WrT+0X_3zpnrW(/AS)sCnhF_Q#$.PL9~60O0 &]DnwGPhfsFNV-N^MmJy~>#xOd|' );
define( 'NONCE_SALT',       '*yR:,9n$XZm|EbP%8{rxS`ucl)~fr$j8ZfYqDuIrm}:54z]+zz~$3~xP2v^AA@^U' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */


// ===== Ajustes de publicación / producción =====
// Detección simple de entorno (puedes establecer WP_ENVIRONMENT_TYPE en el host)
if ( ! defined('WP_ENVIRONMENT_TYPE') ) {
    $host = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : '';
    if ($host === 'localhost' || strpos($host, '.local') !== false) {
        define('WP_ENVIRONMENT_TYPE', 'development');
    } else {
        define('WP_ENVIRONMENT_TYPE', getenv('WP_ENVIRONMENT_TYPE') ?: 'production');
    }
}

// Dominios configurables por variables de entorno (no rompe local)
if (getenv('WP_HOME'))    { define('WP_HOME',    rtrim(getenv('WP_HOME'), '/')); }
if (getenv('WP_SITEURL')) { define('WP_SITEURL', rtrim(getenv('WP_SITEURL'), '/')); }

// Seguridad básica en producción
if (!defined('DISALLOW_FILE_EDIT')) { define('DISALLOW_FILE_EDIT', true); }
// Si gestionas actualizaciones por despliegue, puedes desactivar modificaciones desde el admin
// if (!defined('DISALLOW_FILE_MODS')) { define('DISALLOW_FILE_MODS', true); }

// Memoria y revisiones
if (!defined('WP_MEMORY_LIMIT'))      { define('WP_MEMORY_LIMIT', '256M'); }
if (!defined('WP_MAX_MEMORY_LIMIT'))  { define('WP_MAX_MEMORY_LIMIT', '256M'); }
if (!defined('WP_POST_REVISIONS'))    { define('WP_POST_REVISIONS', 10); }
if (!defined('AUTOSAVE_INTERVAL'))    { define('AUTOSAVE_INTERVAL', 120); }
if (!defined('EMPTY_TRASH_DAYS'))     { define('EMPTY_TRASH_DAYS', 7); }

// Cron: en producción suele gestionarse por cron del sistema
if (filter_var(getenv('DISABLE_WP_CRON') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
    define('DISABLE_WP_CRON', true);
}

// Forzar SSL en admin si el entorno lo indica (no forzar en local)
if (filter_var(getenv('FORCE_SSL_ADMIN') ?: '0', FILTER_VALIDATE_BOOLEAN)) {
    if (!defined('FORCE_SSL_ADMIN')) { define('FORCE_SSL_ADMIN', true); }
}

// Soporte behind proxy para HTTPS
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Actualizaciones del core: solo menores por defecto
if (!defined('WP_AUTO_UPDATE_CORE')) { define('WP_AUTO_UPDATE_CORE', 'minor'); }

// Cache: actívala si existe advanced-cache.php (plugins de cache)
if ( !defined('WP_CACHE') && defined('WP_CONTENT_DIR') && file_exists(WP_CONTENT_DIR . '/advanced-cache.php') ) {
    define('WP_CACHE', true);
}
// ===== Fin ajustes =====



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
