<?php
/**
 * MODx Sample Site
 *
 * Copyright 2010 by Shaun McCormick <shaun@collabpad.com>, excepting
 * subpackages installed by the component.
 *
 * This file is part of MODx Sample Site, a packaged sample site for MODx
 * Revolution.
 *
 * MODx Sample Site is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * MODx Sample Site is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MODx Sample Site; if not, write to the Free Software Foundation, Inc., 59
 * Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package modxss
 */
/**
 * MODx Revolution 2.0 Sample Site build script
 *
 * @package modxss
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* set package defines */
define('PKG_ABBR','modxss');
define('PKG_NAME','MODx Sample Site');
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','rc1');

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once dirname(__FILE__). '/includes/functions.php';

/* setup sources */
$root = dirname(dirname(__FILE__)).'/';
$assets = MODX_ASSETS_PATH.'components/'.PKG_ABBR.'/';
$sources= array (
    'root' => $root,
    'build' => $root .'_build/',
    'resolvers' => $root . '_build/resolvers/',
    'validators' => $root . '_build/validators/',
    'subpackages' => $root . '_build/subpackages/',
    'data' => $root . '_build/data/',
    'properties' => $root . '_build/properties/',
    'source_core' => $root.'core/components/'.PKG_ABBR,
    'source_assets' => $root.'assets/components/'.PKG_ABBR,
    'docs' => $root.'_build/docs/',
);
unset($root);

/* load modx */
$modx= new modX();
$modx->initialize('mgr');
echo '<pre>';
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_ABBR,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_ABBR,false,true,'{core_path}components/'.PKG_ABBR.'/');

/* load system settings */
$settings = include_once $sources['data'].'transport.settings.php';
if (!is_array($settings)) $modx->log(modX::LOG_LEVEL_FATAL,'No settings returned.');
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
);
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($settings).' system settings.'); flush();
unset($settings,$setting,$attributes);

/* load property sets */
$propertySets = include_once $sources['data'].'propertysets/transport.propertysets.php';
if (!is_array($propertySets)) $modx->log(modX::LOG_LEVEL_FATAL,'No property sets returned.');
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
);
foreach ($propertySets as $propertySet) {
    $vehicle = $builder->createVehicle($propertySet,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($propertySets).' property sets.'); flush();
unset($propertySets,$propertySet,$attributes);

/* load user groups */
$usergroups = include_once $sources['data'].'transport.usergroups.php';
if (!is_array($usergroups)) $modx->log(modX::LOG_LEVEL_FATAL,'No User Groups returned.');
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
);
foreach ($usergroups as $usergroup) {
    $vehicle = $builder->createVehicle($usergroup,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($usergroups).' User Groups.'); flush();
unset($usergroups,$usergroup,$attributes);

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);

/* add templates */
$templates = include $sources['data'].'transport.templates.php';
if (is_array($templates)) {
    $category->addMany($templates,'Templates');
} else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templates failed.'); }
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($templates).' templates.'); flush();
unset($templates);

/* add chunks */
$chunks = include $sources['data'].'transport.chunks.php';
if (is_array($chunks)) {
    $category->addMany($chunks,'Chunks');
} else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding chunks failed.'); }
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($chunks).' chunks.'); flush();
unset($chunks);

/* add snippets */
$snippets = include $sources['data'].'transport.snippets.php';
if (is_array($snippets)) {
    $category->addMany($snippets,'Snippets');
} else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding snippets failed.'); }
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($snippets).' snippets.'); flush();
unset($snippets);

/* add tvs */
$tvs = include $sources['data'].'transport.tvs.php';
if (is_array($tvs)) {
    $category->addMany($tvs,'TemplateVars');
} else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding tvs failed.'); }
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($tvs).' tvs.'); flush();
unset($tvs);

/* add subpackages */
$success = include $sources['data'].'transport.subpackages.php';
if (!$success) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding subpackages failed.'); }
$modx->log(modX::LOG_LEVEL_INFO,'Added in subpackages.'); flush();
unset($success);

/* load resources */
$resources = include_once $sources['data'].'transport.resources.php';
if (!is_array($resources)) $modx->log(modX::LOG_LEVEL_FATAL,'No resources returned.');
$attributes= array(
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'id',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'ContentType' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
foreach ($resources as $resource) {
    $vehicle = $builder->createVehicle($resource,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Added in '.count($resources).' Resources.'); flush();
unset($resources,$resource,$attributes);

/* create base category vehicle */
$attr = array(
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),
        'Templates' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),
        'TemplateVars' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
        ),
    )
);
$vehicle = $builder->createVehicle($category,$attr);
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.propertysets.php',
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.tv.template.php',
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.tv.resource.php',
));
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'setup-options' => array(
        'source' => $sources['build'].'setup.options.php',
    ),
));

$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();