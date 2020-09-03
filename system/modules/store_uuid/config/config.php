<?php

/**
 * Contao Open Source CMS
 *
 * simple extension to automatically save a file field as an UUID instead of the path
 * 
 * @copyright inspiredminds 2015
 * @package   store_uuid
 * @link      http://www.inspiredminds.at
 * @author    Fritz Michael Gschwantner <fmg@inspiredminds.at>
 * @license   GPL-2.0
 */


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['storeFormData'][] = array('StoreUUID', 'storeFormData');
