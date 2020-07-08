<?php
ini_set('upload_max_size', '800M');
ini_set('post_max_size', '800M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '2048M');

$GLOBALS['SITE_SETTINGS'] = json_decode(file_get_contents(dirname(__FILE__) . '/.siteconf.json'), true);

/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', $SITE_SETTINGS['DB_NAME']);

/** Имя пользователя MySQL */
define('DB_USER', $SITE_SETTINGS['DB_USER']);

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', $SITE_SETTINGS['DB_PASS']);

/** Имя сервера MySQL */
define('DB_HOST', $SITE_SETTINGS['DB_HOST']);

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', $SITE_SETTINGS['DB_CHARSET']);

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Zps@c@}KM]yPd_t*<GioyK!g&0k2}o1w2?XfZ0!DyAFQTL8~+&WCZ2XO)tk.Iz~6');
define('SECURE_AUTH_KEY',  '_jPz>W6u#1_B{-&FygwB^Mt?HWTF49tfIje|o!%`E2dPvk+kW*`s&dN`FsFD<>Zk');
define('LOGGED_IN_KEY',    'zWIfvQ+Y@[DlOMKYpF2+?0`B>Wn.Nsa[|i;.)}5PcGS$DnDdKdDltA1}xw7 p?L}');
define('NONCE_KEY',        'v/?q<@M,q%Hv^2HCqP,lGV3]A1`f<c)vzQ>3.,j~4~veX4)LdX826KsU.Z+LO*l=');
define('AUTH_SALT',        '#$[gY+^Gj&ZH=_l54:2GxcJEbKRJ3ZDt8a+b*%[#~z@{d}1WIZ(a*7SH;T;O&!0x');
define('SECURE_AUTH_SALT', 'KJ!_}&(.f2,aiY=s+~J)fKpcll%bzNdtbwQuuS&R!rFS1SgjwYW+*~e7y8,t}:%J');
define('LOGGED_IN_SALT',   'l,jVi(t<b-k4~LK9P2S0R{xX/{YV><X}U_@Gx]+j*L8G$sf(Am?<W-A-|4IC1i9&');
define('NONCE_SALT',       'sOdal0Bw^Fy{n.wrRX:KPU{EwxHSyz{[Pij7!mOg8W*U/C[]?KXPdv@wNV_+]P+J');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', $SITE_SETTINGS['WP_DEBUG']);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if (!defined('ABSPATH')) {
	define('ABSPATH', dirname(__FILE__) . '/');
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
