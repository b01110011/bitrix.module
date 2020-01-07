<?php

/**
 * Получаем айди модуля.
 */
function bx_module_id()
{
    static $module_id = '';

    if (empty($module_id))
        $module_id = basename(__DIR__);

    return $module_id;
}

/**
 * Получаем префикс айди модуля, используется для получения префикса для ключей локализации или файлов для копирования в админ часть.
 */
function bx_module_id_prefix()
{
    static $module_id_prefix = '';

    if (empty($module_id_prefix))
        $module_id_prefix = str_replace('.', '_', bx_module_id());

    return $module_id_prefix;
}

/**
 * Получаем префикс для ключей локализации.
 */
function bx_loc_prefix()
{
    static $loc_prefix = '';

    if (empty($loc_prefix))
        $loc_prefix = strtoupper(bx_module_id_prefix()) .'_';

    return $loc_prefix;
}

/**
 * Получаем префикс файлов для копирования в админ часть.
 */
function bx_file_prefix()
{
    static $file_prefix = '';

    if (empty($file_prefix))
        $file_prefix = bx_module_id_prefix() .'_';

    return $file_prefix;
}