<?php
/**
 * Created by JetBrains PhpStorm.
 * User: wangjuqing
 * Date: 13-6-6
 * Time: 上午10:09
 * To change this template use File | Settings | File Templates.
 */
function loadBaseClass($name,$value){
    require "base.class.php";
    $link = new Base($name,$value);
    return $link;
}