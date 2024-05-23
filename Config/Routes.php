<?php

$routes->add("/", "\Ppci\Controllers\Defaultpage");
$routes->add('default', '\Ppci\Controllers\Defaultpage');
$routes->add('droitko', '\Ppci\Controllers\Droitko::index');
$routes->add('gestiondroits', '\Ppci\Controllers\Gestiondroits::index');
/**
 * Connection
 */
$routes->add('connexion', '\Ppci\Controllers\Login::index');
$routes->add('login', '\Ppci\Controllers\Login::index');
$routes->add('totp', '\Ppci\Controllers\Totp::index');
$routes->add('loginValid', '\Ppci\Controllers\Login::valid');
$routes->post('loginExec', '\Ppci\Controllers\Login::loginExec');
$routes->add('loginCasExec', '\Ppci\Controllers\Login::loginCasExec');
$routes->add('disconnect', '\Ppci\Controllers\Login::disconnect');
/**
 * Manage loginGestion
 */
$routes->add('loginGestionList', '\Ppci\Controllers\LoginGestion::index');
$routes->add('loginList', '\Ppci\Controllers\LoginGestion::index');
$routes->add('loginChange', '\Ppci\Controllers\LoginGestion::change');
$routes->post('loginWrite', '\Ppci\Controllers\LoginGestion::write');
$routes->post('loginDelete', '\Ppci\Controllers\LoginGestion::delete');
$routes->add('loginChangePassword', '\Ppci\Controllers\LoginGestion::changePassword');
$routes->post('loginChangePasswordExec', '\Ppci\Controllers\LoginGestion::changePasswordExec');
/**
 * password lost
 */
$routes->add("passwordlostIslost", '\Ppci\Controllers\Passwordlost::isLost');
$routes->post("passwordlostSendmail", '\Ppci\Controllers\Passwordlost::sendMail');
$routes->add("passwordlostReinitchange", '\Ppci\Controllers\Passwordlost::reinitChange');
$routes->post("passwordlostReinitwrite", '\Ppci\Controllers\Passwordlost::reinitWrite');

/**
 * TOTP
 */
$routes->add('totpCreate', '\Ppci\Controllers\Totp::create');
$routes->post('totpCreateVerify', '\Ppci\Controllers\Totp::createVerify');
$routes->add('totpGetQrcode', '\Ppci\Controllers\Totp::getQrcode');
$routes->post('totpVerifyExec', '\Ppci\Controllers\Totp::verify');
$routes->add('totpAdmin', '\Ppci\Controllers\Totp::admin');
/**
 * Miscellaneous
 */
$routes->add('getLastConnections', '\Ppci\Controllers\Log::getLastConnections');
$routes->add('apropos', '\Ppci\Controllers\Miscellaneous::about');
$routes->add('about', '\Ppci\Controllers\Miscellaneous::about');
$routes->add('phpinfo', '\Ppci\Controllers\Miscellaneous::phpinfo');
$routes->add('quoideneuf', '\Ppci\Controllers\Miscellaneous::news');
$routes->add('news', '\Ppci\Controllers\Miscellaneous::news');
$routes->add('test', '\Ppci\Controllers\Miscellaneous::test');
$routes->add('systemShowServer', '\Ppci\Controllers\Miscellaneous::systemServer');
$routes->add('systemShowSession', '\Ppci\Controllers\Miscellaneous::systemSession');
$routes->add('logList', '\Ppci\Controllers\Log::index');
$routes->add('backupDisplay', '\Ppci\Controllers\Backup::index');
$routes->post('backupExec', '\Ppci\Controllers\Backup::exec');

$routes->add('dbparamList', '\Ppci\Controllers\Miscellaneous::dbparamList');
$routes->post('dbparamWriteGlobal', '\Ppci\Controllers\Miscellaneous::dbparamWriteGlobal');

$routes->add('dbstructureHtml', '\Ppci\Controllers\Miscellaneous::structureHtml');
$routes->add('dbstructureLatex', '\Ppci\Controllers\Miscellaneous::structureLatex');
$routes->add('dbstructureSchema', '\Ppci\Controllers\Miscellaneous::structureSchema');

$routes->add("getLastRelease", '\Ppci\Controllers\Miscellaneous::getLastRelease');

/**
 * GACL configuration
 */
$routes->add('appliList', '\Ppci\Controllers\Gacl::appliindex');
$routes->add('appliDisplay', '\Ppci\Controllers\Gacl::applidisplay');
$routes->add('appliChange', '\Ppci\Controllers\Gacl::applichange');
$routes->post('appliWrite', '\Ppci\Controllers\Gacl::appliwrite');
$routes->post('appliDelete', '\Ppci\Controllers\Gacl::applidelete');
$routes->add('aclloginList', '\Ppci\Controllers\Gacl::loginindex');
$routes->add('aclloginChange', '\Ppci\Controllers\Gacl::loginchange');
$routes->post('aclloginWrite', '\Ppci\Controllers\Gacl::loginwrite');
$routes->post('aclloginDelete', '\Ppci\Controllers\Gacl::logindelete');
$routes->add('groupList', '\Ppci\Controllers\Gacl::groupindex');
$routes->add('groupChange', '\Ppci\Controllers\Gacl::groupchange');
$routes->post('groupWrite', '\Ppci\Controllers\Gacl::groupwrite');
$routes->post('groupDelete', '\Ppci\Controllers\Gacl::groupdelete');
$routes->add('acoDisplay', '\Ppci\Controllers\Gacl::acodisplay');
$routes->add('acoChange', '\Ppci\Controllers\Gacl::acochange');
$routes->post('acoWrite', '\Ppci\Controllers\Gacl::acowrite');
$routes->post('acoDelete', '\Ppci\Controllers\Gacl::acodelete');

/**
 * SQL requests
 */
$routes->add('requestList', '\Ppci\Controllers\Request::list');
$routes->add('requestChange', '\Ppci\Controllers\Request::change');
$routes->post('requestWrite', '\Ppci\Controllers\Request::write');
$routes->post('requestDelete', '\Ppci\Controllers\Request::delete');
$routes->post('requestExec', '\Ppci\Controllers\Request::exec');
$routes->add('requestExecList', '\Ppci\Controllers\Request::execList');
$routes->post('requestWriteExec', '\Ppci\Controllers\Request::writeExec');
$routes->add('requestCopy', '\Ppci\Controllers\Request::copy');

$routes->add('gestion', 'Gestion\Index::index');
$routes->add('errorbefore', '\Ppci\Controllers\Errorbefore::index');
$routes->add('errorlogin', '\Ppci\Controllers\Errorlogin::index');
$routes->add('test', 'Test::index');

$routes->add('setlanguage', '\Ppci\Controllers\Locale::index');
$routes->add('setlanguagefr', '\Ppci\Controllers\Locale::index/fr');
$routes->add('setlanguageen', '\Ppci\Controllers\Locale::index/en');
$routes->add('setlanguageus', '\Ppci\Controllers\Locale::index/us');
$routes->add("setlocale", "\Ppci\Controllers\Locale::index");

$routes->add('documentationGetFile', '\Ppci\Controllers\Utils\File::documentationGetFile');

$routes->add('passwordlostIslost', '\Ppci\Controllers\PasswordLost::isLost');
$routes->add('passwordlostSendmail', '\Ppci\Controllers\PasswordLost::sendMail');
$routes->add('passwordlostReinitchange', '\Ppci\Controllers\PasswordLost::reinitChange');
$routes->post('passwordlostReinitwrite', '\Ppci\Controllers\PasswordLost::reinitWrite');

$routes->add('lexicalGet', '\Ppci\Controllers\Utils\Lexical::index');

/**
 * Submenus
 */
$routes->add('administration', '\Ppci\Controllers\Utils::submenu/administration');
$routes->add('documentation_fr', '\Ppci\Controllers\Utils::submenu/documentation_fr');
$routes->add('documentation_en', '\Ppci\Controllers\Utils::submenu/documentation_en');
/**
 * Documentation
 */
$routes->add('doctotp_fr', '\Ppci\Controllers\Utils::markdown/vendor/equinton/ppci/Documentation/totp_fr.md');
$routes->add('doctotp_en', '\Ppci\Controllers\Utils::markdown/vendor/equinton/ppci/Documentation/totp_en.md');

