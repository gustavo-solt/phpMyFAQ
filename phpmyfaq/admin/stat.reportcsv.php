<?php
/**
 * Export report to csv
 *
 * PHP Version 5.2
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * @category  phpMyFAQ
 * @package   Administration
 * @author    Gustavo Solt <gustavo.solt@mayflower.de>
 * @copyright 2003-2011 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/MPL-1.1.html Mozilla Public License Version 1.1
 * @link      http://www.phpmyfaq.de
 * @since     2011-01-12
 */

define('PMF_ROOT_DIR', dirname(dirname(__FILE__)));

// Define the named constant used as a check by any included PHP file
define('IS_VALID_PHPMYFAQ', null);

// Autoload classes, prepend and start the PHP session
require_once PMF_ROOT_DIR.'/inc/Init.php';
PMF_Init::cleanRequest();
session_name(PMF_COOKIE_NAME_AUTH.trim($faqconfig->get('main.phpMyFAQToken')));
session_start();

// get language (default: english)
$Language = new PMF_Language();
$LANGCODE = $Language->setLanguage($faqconfig->get('main.languageDetection'), $faqconfig->get('main.language'));
// Preload English strings
require_once (PMF_ROOT_DIR.'/lang/language_en.php');

if (isset($LANGCODE) && PMF_Language::isASupportedLanguage($LANGCODE)) {
    // Overwrite English strings with the ones we have in the current language
    require_once PMF_ROOT_DIR.'/lang/language_'.$LANGCODE.'.php';
} else {
    $LANGCODE = 'en';
}

// Initalizing static string wrapper
PMF_String::init($LANGCODE);

$auth = false;
$user = PMF_User_CurrentUser::getFromSession($faqconfig->get('main.ipCheck'));
if ($user) {
    $auth = true;
} else {
    $user = null;
    unset($user);
}

// Get current user rights
$permission = array();
if ($auth === true) {
    // read all rights, set them FALSE
    $allRights = $user->perm->getAllRightsData();
    foreach ($allRights as $right) {
        $permission[$right['name']] = false;
    }
    // check user rights, set them TRUE
    $allUserRights = $user->perm->getAllUserRights($user->getUserId());
    foreach ($allRights as $right) {
        if (in_array($right['right_id'], $allUserRights))
            $permission[$right['name']] = true;
    }
}

/**
 * Convert string to the correct encoding
 *
 * @param string $outputString String to encode.
 *
 * @return string Encoded string.
 */
function convertEncoding($outputString)
{
    $outputString = html_entity_decode($outputString, ENT_QUOTES);
    $outputString = str_replace(',', ' ', $outputString);

    if (extension_loaded('mbstring')) {
        $detected = mb_detect_encoding($outputString);

        if ($detected !== 'ASCII') {
            $outputString = mb_convert_encoding($outputString, 'UTF-16', $detected);
        }
    }

    return $outputString;
}

if ($permission['viewlog']) {
    $useCategory      = PMF_Filter::filterInput(INPUT_POST, 'report_category', FILTER_VALIDATE_INT);
    $useSubcategory   = PMF_Filter::filterInput(INPUT_POST, 'report_sub_category', FILTER_VALIDATE_INT);
    //$useIdLinked      = PMF_Filter::filterInput(INPUT_POST, 'report_id_linked', FILTER_VALIDATE_INT);
    $useLanguage      = PMF_Filter::filterInput(INPUT_POST, 'report_language', FILTER_VALIDATE_INT);
    $useId            = PMF_Filter::filterInput(INPUT_POST, 'report_id', FILTER_VALIDATE_INT);
    $useSticky        = PMF_Filter::filterInput(INPUT_POST, 'report_sticky', FILTER_VALIDATE_INT);
    $useTitle         = PMF_Filter::filterInput(INPUT_POST, 'report_title', FILTER_VALIDATE_INT);
    $useCreadtionDate = PMF_Filter::filterInput(INPUT_POST, 'report_creadtion_date', FILTER_VALIDATE_INT);
    $useOwner         = PMF_Filter::filterInput(INPUT_POST, 'report_owner', FILTER_VALIDATE_INT);
    $useLastModified  = PMF_Filter::filterInput(INPUT_POST, 'report_last_modified_person', FILTER_VALIDATE_INT);
    $useUrl           = PMF_Filter::filterInput(INPUT_POST, 'report_url', FILTER_VALIDATE_INT);
    $useConnections   = PMF_Filter::filterInput(INPUT_POST, 'report_connections', FILTER_VALIDATE_INT);

    $text    = array();
    $text[0] = array();
    ($useCategory)      ? $text[0][] = $PMF_LANG['ad_stat_report_category'] : '';
    ($useSubcategory)   ? $text[0][] = $PMF_LANG['ad_stat_report_sub_category'] : '';
    //($useIdLinked)      ? $text[0][] = $PMF_LANG['ad_stat_report_id_linked'] : '';
    ($useLanguage)      ? $text[0][] = $PMF_LANG['ad_stat_report_language'] : '';
    ($useId)            ? $text[0][] = $PMF_LANG['ad_stat_report_id'] : '';
    ($useSticky)        ? $text[0][] = $PMF_LANG['ad_stat_report_sticky'] : '';
    ($useTitle)         ? $text[0][] = $PMF_LANG['ad_stat_report_title'] : '';
    ($useCreadtionDate) ? $text[0][] = $PMF_LANG['ad_stat_report_creadtion_date'] : '';
    ($useOwner)         ? $text[0][] = $PMF_LANG['ad_stat_report_owner'] : '';
    ($useLastModified)  ? $text[0][] = $PMF_LANG['ad_stat_report_last_modified_person'] : '';
    ($useUrl)           ? $text[0][] = $PMF_LANG['ad_stat_report_url'] : '';
    ($useConnections)   ? $text[0][] = $PMF_LANG['ad_stat_report_connections'] : '';
    $category = new PMF_Category($current_admin_user, $current_admin_groups, false);
    $category->buildTree();

    $query = sprintf("
        SELECT
            fd.id AS id,
            fd.lang AS lang,
            fd.sticky AS sticky,
            fd.thema AS thema,
            fd.author AS author,
            fd.datum AS datum,
            fd.sticky AS sticky,
            fcr.category_id AS category_id,
            c.name as category_name,
            fv.visits AS visits,
            u.display_name
        FROM %sfaqdata fd
        LEFT JOIN %sfaqcategoryrelations fcr ON (fd.id = fcr.record_id AND fd.lang = fcr.record_lang)
        LEFT JOIN %sfaqvisits fv ON (fd.id = fv.id AND fd.lang = fv.lang)
        LEFT JOIN faqchanges as fc ON (fd.id = fc.id AND fd.lang = fc.lang)
        LEFT JOIN %sfaquserdata as u ON (u.user_id = fc.usr)
        LEFT JOIN faqcategories as c ON (c.id = fcr.category_id AND c.lang = fcr.record_lang)
        ORDER BY fd.id ASC",
        SQLPREFIX,
        SQLPREFIX,
        SQLPREFIX,
        SQLPREFIX,
        SQLPREFIX,
        SQLPREFIX);

    $db     = PMF_Db::getInstance();
    $result = $db->query($query);

    while ($row = $db->fetch_object($result)) {
        $text[$i] = array();
        if ($useCategory) {
            $current = $row->category_id;
            $cat = 1;
            while ($cat > 0) {
                $cat = $category->categoryName[$current]['parent_id'];
                if ($cat != 0) {
                    $current = $cat;
                }
            }
            if ($row->category_id == $current) {
                $text[$i][] = '';
            } else {
                $text[$i][] = convertEncoding($category->categoryName[$current]['name']);
            }
        }
        if ($useSubcategory) {
            $text[$i][] = convertEncoding($row->category_name);
        }
        //if ($useIdLinked) {
        //    $text[$i][] = '';
        //}
        if ($useLanguage) {
            $text[$i][] = $row->lang;
        }
        if ($useId) {
            $text[$i][] = $row->id;
        }
        if ($useSticky) {
            $text[$i][] = $row->sticky;
        }
        if ($useTitle) {
            $text[$i][] = convertEncoding($row->thema);
        }
        if ($useCreadtionDate) {
            $text[$i][] = PMF_Date::createIsoDate($row->datum);
        }
        if ($useOwner) {
            $text[$i][] = convertEncoding($row->author);
        }
        if ($useLastModified) {
            $text[$i][] = convertEncoding($row->display_name);
        }
        if ($useUrl) {
            $text[$i][] = sprintf('<a href="../index.php?action=artikel&amp;cat=%d&amp;id=%d&amp;artlang=%s">Link</a>',
                $row->category_id,
                $row->id,
                $row->lang);;
        }
        if ($useConnections) {
            $text[$i][] = $row->visits;
        }
        $i++;
    }

    $outputString = '';
    foreach ($text as $row) {
        $line          = join(",", $row);
        $outputString .= $line . "\r\n";
    }

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header("Cache-Control: must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Length: ' . strlen($outputString));
    header("Content-Disposition: attachment; filename=\"export.csv\"");
    header("Content-type: application/octet-stream; charset=utf-8");

    echo $outputString;
} else {
    print $PMF_LANG['err_NotAuth'];
}
