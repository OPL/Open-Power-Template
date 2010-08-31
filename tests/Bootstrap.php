<?php
/**
 * The bootstrap file for unit tests.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

$config = parse_ini_file(dirname(__FILE__).'/../paths.ini', true);
require($config['Opl'].'Opl/Loader.php');

$loader = new Opl_Loader('_');
$loader->addLibrary('Opl', $config['Opl']);
$loader->addLibrary('Opt', $config['Opt']);
$loader->addLibrary('Extra', './');
$loader->register();