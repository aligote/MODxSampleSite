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
 * @package modxss
 * @subpackage build
 */
$templates = array();

$templates[1]= $modx->newObject('modTemplate');
$templates[1]->fromArray(array(
    'id' => 1,
    'templatename' => 'MODxHost',
    'description' => 'The main template for the design.',
    'content' => file_get_contents($sources['data'].'templates/template.modxhost.tpl'),
),'',true,true);

$templates[2]= $modx->newObject('modTemplate');
$templates[2]->fromArray(array(
    'id' => 2,
    'templatename' => 'BlogPost',
    'description' => 'The template for blog posts.',
    'content' => file_get_contents($sources['data'].'templates/template.blogpost.tpl'),
),'',true,true);

return $templates;