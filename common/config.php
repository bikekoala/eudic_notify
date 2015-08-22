<?PHP

/**
 * Eudic 配置
 */
const E_USERNAME    = '';
const E_PASSWORD    = '';
const E_LOGIN_URL   = 'http://dict.eudic.net/Account/Login?returnUrl=';
const E_LIST_URL    = 'http://dict.eudic.net/StudyList/GridData?rows=%d&page=%d';
const E_COOKIE_PATH = '/tmp/eudic.cookie';

/**
 * Mail 配置
 */
const MAIL_USERNAME = '';
const MAIL_PASSWORD = '';
const MAIL_HOST     = 'smtp.yeah.net';
const MAIL_PORT     = 25;

/**
 * Mysql PDO 配置
 */
const PDO_DSN = 'mysql:dbname=eudic_notify;host=localhost;port=3306';
const PDO_USR = 'root';
const PDO_PWD = '';
