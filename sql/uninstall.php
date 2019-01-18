<?php
/**
 * NOTICE OF LICENSE
 *
 * EuCookieSmart is a module for display a cookie law banner.
 * Copyright (C) 2017 Yuri Blanc
 * Email: yuxblank@gmail.com
 * Website: www.yuriblanc.it
 *
 * This program is distributed WITHOUT ANY WARRANTY
 * @license GNU General Public License v3.0
 */

$sql = array();

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
