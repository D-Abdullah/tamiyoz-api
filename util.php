<?php

/* * *************************************************************************
 *
 *   PROJECT: rhletak
 *   powerd by ashraf hamdy
 *   Copyright 2016 IT Plus Inc
 *   http://it-plus.co/s
 *
 * ************************************************************************* */

/**
 * Converts string to url valid string
 *
 * @param arr $aParams text string
 *
 * @return str
 */
function convert_str($aParams)
{
    $aParams['string'] = strtolower($aParams['string']);
    $aParams['string'] = preg_replace('/[^a-z0-9]+/i', '-', $aParams['string']);
    $aParams['string'] = preg_replace('/\-+/', '-', $aParams['string']);
    $aParams['string'] = trim($aParams['string'], '-');

    return $aParams['string'];
}

/**
 * Returns correct path for a directory
 *
 * @param str $aPath parent category path
 * @param str $aTitle category title
 *
 * @return str
 */
function dir_form_path($aPath, $aTitle)
{
    $params['string'] = $aTitle;
    $title = convert_str($params['string']);

    return $aPath . '/' . $title;
}

function poweredBy()
{

    return "<p> Powered by <a href='www.it-plus.co' target='_blank' >IT Plus </a> </p>";
}

/**
 * Prints navigation menu
 *
 * Note about url template!
 * Url template shoud contain %1 that will be replaced
 * with actual page number.  This is very convenient
 * in case url will be change (e.g. for mod_rewrite purpose
 * it changes from 'index.php?page=2' to 'index2.htm'
 */
function navigation($aTotal, $aStart, $aNum, $aUrl, $aItemsPerPage = 3, $aLinksPerPage = 5)
{
//    echo $aTotal;    echo '<br/>';
    //    echo $aStart;echo '<br/>';
    //    echo $aNum;echo '<br/>';
    //    echo $aUrl;echo '<br/>';
    //    echo $aItemsPerPage;echo '<br/>';
    //    echo $aLinksPerPage;echo '<br/>';
    //    die();
    global $Lang;
    if ($aTotal && $aTotal > $aItemsPerPage) {

        $num_pages = ceil($aTotal / $aItemsPerPage);
//        echo $num_pages;
        //        die();
        $current_page = (int) $_GET['page'];
        $current_page = ($current_page < 1) ? 1 : ($current_page > $num_pages ? $num_pages : $current_page);

        $left_offset = ceil($aLinksPerPage / 2) - 1;
        $first = $current_page - $left_offset;
        $first = ($first < 1) ? 1 : $first;

        $last = $first + $aLinksPerPage - 1;
        $last = ($last > $num_pages) ? $num_pages : $last;

        $first = $last - $aLinksPerPage + 1;
        $first = ($first < 1) ? 1 : $first;

        $pages = range($first, $last);

        $out = '<div class="pagination pull-left" style="margin: 0;">    <ul>';

        $delim = ('.php' == strtolower(substr($aUrl, -4))) ? '?' : '&amp;';

// Previous, First links
        if ($current_page > 1) {
            $prev = $current_page - 1;
            $out .= "<li ><a href=\"{$aUrl}{$delim}page={$prev}{$delim}items=" . (INT) $_GET['items'] . "\">&raquo;</a></li>";
        } else {
            $out .= '<li class="disabled"><a href="#">&raquo;</a></li>';
        }

        foreach ($pages as $page) {
            if ($current_page == $page) {
                $out .= " <li class=\"active\"><a href=\"#\">{$page}</a></li>";
            } else {
                $out .= "<li><a href=\"{$aUrl}{$delim}page={$page}{$delim}items=" . (INT) $_GET['items'] . "\"> {$page}</a> </li>";
            }
        }

        if ($current_page < $num_pages) {

            $next = $current_page + 1;
            $out .= "<li ><a href=\"{$aUrl}{$delim}page={$next}{$delim}items=" . (INT) $_GET['items'] . "\">&laquo;</a></li>";
        } else {
            $out .= '<li class="disabled"><a href="#">&laquo;</a></li>';
        }

        $out .= '</div>';
    }
    $from = ($aTotal > 0) ? $aStart + 1 : $aStart;
    $to = ($aTotal > ($aStart + 1 + $aItemsPerPage)) ? $aStart + $aItemsPerPage : $aTotal;
    $out .= 'من ' . $from . ' الى ' . $to . ' الاجمالي ' . $aTotal;
    return $out;
}

/**
 * Checks if reciprocal link exists
 *
 * @param str $aRecip reciprocal page url
 *
 * @return int
 */
function check_reciprocal($aRecip)
{
    global $gDirConfig;

    $reciprocal = "<(\s*)a (.*)href=(\"{0,1}){$gDirConfig['reciprocal_text']}(\"{0,1})(.*)>(.*)</a>";

    $res = 0;
    $content = @file($aRecip);
    if ($content) {
        if ($ftext = join('', $content)) {
            $res = eregi($reciprocal, $ftext) ? 1 : 0;
        }
    }

    return $res;
}

/**
 * Checks link and returns its header
 *
 * @param str $aUrl page url
 *
 * @return int
 */
function get_link_header($aUrl)
{
    if (preg_match("'^http://'", $aUrl)) {
        $content = @file($aUrl);
        if ($content) {
            $header = join("\n", $http_response_header);
            list(, $http_header) = split(" ", $header, 3);
        } else {
            $http_header = 0;
        }
    }
    return $http_header;
}

/**
 * Validates email
 *
 * @param str $aEmail email
 *
 * @return bool
 */
function valid_email($aEmail)
{
    return preg_match('/^[a-z0-9\-_\.]+@[a-z0-9\-_]+(\.[a-z0-9]{2,4})+$/i', $aEmail);
}

/**
 * Converts link description to html
 *
 * @param str $aParams parameters array
 *
 * @return str
 */
function text_to_html($aParams)
{
    $out = '';

    $aParams['aText'] = htmlentities($aParams['aText']);

    $aParams['aText'] = preg_replace('/\[b\]/', '<b>', $aParams['aText']);
    $aParams['aText'] = preg_replace('/\[\/b\]/', '</b>', $aParams['aText']);

    $aParams['aText'] = preg_replace('/\[i\]/', '<i>', $aParams['aText']);
    $aParams['aText'] = preg_replace('/\[\/i\]/', '</i>', $aParams['aText']);

    $aParams['aText'] = preg_replace('/\[u\]/', '<u>', $aParams['aText']);
    $aParams['aText'] = preg_replace('/\[\/u\]/', '</u>', $aParams['aText']);

    $aParams['aText'] = preg_replace('/\[hl\]/', '<span class="highlight">', $aParams['aText']);
    $aParams['aText'] = preg_replace('/\[\/hl\]/', '</span>', $aParams['aText']);

    $aParams['aText'] = nl2br($aParams['aText']);
    if ($aParams['aParagraph']) {
        $paragraphs = explode("\r\n", $aParams['aText']);
        foreach ($paragraphs as $paragraph) {
            if (strlen($paragraph) > 0) {
                $out .= "<p class=\"user\">{$paragraph}</p>\n";
            }
        }
    } else {
        $out = $aParams['aText'];
    }
    return $out;
}

function format($aParam)
{
    return number_format($aParam, 3);
}

function newPassword()
{
    $pass = rand(100000, 1000000);

    return $pass;
}

function generateKey()
{
    $key = rand(10000, 100000);
    return $key;
}

function rename_file($file_name)
{
    $date = date("YmdHms");
    $rand = rand(10000, 99999);
    $encr = md5($date . $rand);
    $mbss = mb_substr($encr, "4", "4", "utf-8");
    $new_name = $date . "_" . $mbss;
    return $new_name;
}

function print_box($aContent = '')
{
    if ($aContent) {
        foreach ($aContent as $key => $value) {
            for ($i = 0; $i < count($value); $i++) {
                $cont .= " <div class='alert alert-{$key}'>
                          <button class='close' data-dismiss='alert' type='button'>×</button>
                          {$value[$i]}
                      </div>";
            }
        }
        return $cont;
    }
}

function print_box2($aId = '', $aTitle = '', $aContent = '', $aIcon = '')
{
    if (MENU_PARENT == $aId) {
        $s = ' <li class = "start active">';
        $s .= '<a href = "javascript:;">
<i class = "icon-puzzle"></i>
<span class = "title">' . $aTitle . '</span>
<span class = "arrow open"></span>
</a>
<ul class="sub-menu" style="display: block;">';
        $s .= $aContent;
        $s .= '</ul>
</li>';
    } else {
        $s = '  <li class = "">';
        $s .= '<a href = "javascript:;">
            ' . $aIcon . '
<span class = "title">' . $aTitle . '</span>
<span class = "arrow open"></span>
</a>
<ul class="sub-menu" style="display: none;">';
        $s .= $aContent;
        $s .= '</ul>
</li>';
    }

    return $s;
}

/**
 * Returns all values of array as string
 *
 * @param arr $arr
 *
 * @return str
 */
function fetchArrayValue($arr)
{
    $str = "";
    foreach ($arr as $value) {
        $str .= $value;
        if (next($arr) == true) {
            $str .= ",";
        }

    }
    return $str;
}

function time_ago($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1) {
        return '0 seconds';
    }

    $a = array(12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second',
    );

    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
        }
    }
}

/**
 * Prints menu elements
 *
 * @param arr $aMenu array of menu elements
 *
 * @return str
 */
function print_menu($aMenu)
{

    foreach ($aMenu as $key => $value) {
        $caption = $value['caption'];
        $url = $value['url'];

        $icon = $value['icon'] ? "<i class=\"{$value['icon']}\"></i>" : '';

        if (THIS_PAGE == $key) {
            $out .= '<li class="active">
            <a href=' . $url . '>
                ' . $icon . '
            ' . $caption . '
            </a>
            </li>';
                    } else {
                        $out .= '<li class="">
            <a href=' . $url . '>
            ' . $icon . '
            ' . $caption . '
            </a>
            </li>';
        }
    }

    return $out;
}

function in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
