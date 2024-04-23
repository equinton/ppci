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
 * TOTP
 */
$routes->add('totpCreate', '\Ppci\Controllers\Totp::create');
$routes->post('totpCreateVerify', '\Ppci\Controllers\Totp::createVerify');
$routes->add('totpGetQrcode', '\Ppci\Controllers\Totp::getQrcode');
$routes->post('totpVerifyExec', '\Ppci\Controllers\Totp::verify');
$routes->add('totpAdmin', '\Ppci\Controllers\Totp::admin');


$routes->add('administration', '\Ppci\Controllers\Utils\Submenu::index');
$routes->add('gestion', 'Gestion\Index::index');
$routes->add('errorbefore', '\Ppci\Controllers\Errorbefore::index');
$routes->add('errorlogin', '\Ppci\Controllers\Errorlogin::index');
$routes->add('test', 'Test::index');

$routes->add('apropos', '\Ppci\Controllers\Utils\About::index');
$routes->add('about', '\Ppci\Controllers\Utils\About::index');
$routes->add('phpinfo', '\Ppci\Controllers\Phpinfo::index');
$routes->add('quoideneuf', '\Ppci\Controllers\Utils\News::index');
$routes->add('news', '\Ppci\Controllers\Utils\News::index');
$routes->add('getLastConnections', '\Ppci\Controllers\Log\Log::getLastConnections');
$routes->add('setlanguage', '\Ppci\Controllers\Setlanguage::index');
$routes->add('setlanguagefr', '\Ppci\Controllers\Setlanguage::fr');
$routes->add('setlanguageen', '\Ppci\Controllers\Setlanguage::en');
$routes->add('setlanguageus', '\Ppci\Controllers\Setlanguage::us');
$routes->add('documentation_fr', '\Ppci\Controllers\Utils\Submenu::index');
$routes->add('documentation_en', '\Ppci\Controllers\Utils\Submenu::index');
$routes->add('documentationGetFile', '\Ppci\Controllers\Utils\File::documentationGetFile');
$routes->add('appliList', '\Ppci\Controllers\Droits\Appli::index');
$routes->add('appliDisplay', '\Ppci\Controllers\Droits\Appli::display');
$routes->add('appliChange', '\Ppci\Controllers\Droits\Appli::change');
$routes->post('appliWrite', '\Ppci\Controllers\Droits\Appli::write');
$routes->post('appliDelete', '\Ppci\Controllers\Droits\Appli::delete');
$routes->add('aclloginList', '\Ppci\Controllers\Droits\Login::index');
$routes->add('aclloginChange', '\Ppci\Controllers\Droits\Login::change');
$routes->post('aclloginWrite', '\Ppci\Controllers\Droits\Login::write');
$routes->post('aclloginDelete', '\Ppci\Controllers\Droits\Login::delete');
$routes->add('groupList', '\Ppci\Controllers\Droits\Group::index');
$routes->add('groupChange', '\Ppci\Controllers\Droits\Group::change');
$routes->post('groupWrite', '\Ppci\Controllers\Droits\Group::write');
$routes->post('groupDelete', '\Ppci\Controllers\Droits\Group::delete');
$routes->add('acoDisplay', '\Ppci\Controllers\Droits\Aco::display');
$routes->add('acoChange', '\Ppci\Controllers\Droits\Aco::change');
$routes->post('acoWrite', '\Ppci\Controllers\Droits\Aco::write');
$routes->post('acoDelete', '\Ppci\Controllers\Droits\Aco::delete');
$routes->add('passwordlostIslost', '\Ppci\Controllers\PasswordLost::isLost');
$routes->add('passwordlostSendmail', '\Ppci\Controllers\PasswordLost::sendMail');
$routes->add('passwordlostReinitchange', '\Ppci\Controllers\PasswordLost::reinitChange');
$routes->post('passwordlostReinitwrite', '\Ppci\Controllers\PasswordLost::reinitWrite');
$routes->add('dbparamList', '\Ppci\Controllers\Dbparam\Dbparam::index');
$routes->post('dbparamWriteGlobal', '\Ppci\Controllers\Dbparam\Dbparam::writeGlobal');
$routes->add('logList', '\Ppci\Controllers\Log\Log::index');
$routes->add('requestList', '\Ppci\Controllers\Request\Request::index');
$routes->add('requestChange', '\Ppci\Controllers\Request\Request::change');
$routes->post('requestWrite', '\Ppci\Controllers\Request\Request::write');
$routes->post('requestDelete', '\Ppci\Controllers\Request\Request::delete');
$routes->post('requestExec', '\Ppci\Controllers\Request\Request::exec');
$routes->post('requestExecList', '\Ppci\Controllers\Request\Request::execList');
$routes->post('requestWriteExec', '\Ppci\Controllers\Request\Request::write');
$routes->add('requestCopy', '\Ppci\Controllers\Request\Request::copy');
$routes->add('lexicalGet', '\Ppci\Controllers\Utils\Lexical::index');
$routes->add('backupDisplay', '\Ppci\Controllers\Utils\Backup::display');
$routes->post('backupExec', '\Ppci\Controllers\Utils\Backup::exec');
$routes->post('backupSend', '\Ppci\Controllers\Utils\Backup::send');

$routes->add('doctotp_fr', '\Ppci\Controllers\Utils\Markdown::framework/documentation/totp_fr.md');
$routes->add('doctotp_en', '\Ppci\Controllers\Utils\Markdown::framework/documentation/totp_en.md');
$routes->add('systemShowServer', '\Ppci\Controllers\Utils\System::SERVER');
$routes->add('systemShowSession', '\Ppci\Controllers\Utils\System::SESSION');