<?php
/**
 * Show a report
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

if (!defined('IS_VALID_PHPMYFAQ')) {
    header('Location: http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']));
    exit();
}

if ($permission['viewlog']) {

    printf('<h2>%s</h2>', $PMF_LANG['ad_menu_report']);

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
?>
    <table class="list">
    <thead>
    <tr>
<?php
    ($useCategory)      ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_category']) : '';
    ($useSubcategory)   ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_sub_category']) : '';
    //($useIdLinked)      ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_id_linked']) : '';
    ($useLanguage)      ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_language']) : '';
    ($useId)            ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_id']) : '';
    ($useSticky)        ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_sticky']) : '';
    ($useTitle)         ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_title']) : '';
    ($useCreadtionDate) ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_creadtion_date']) : '';
    ($useOwner)         ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_owner']) : '';
    ($useLastModified)  ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_last_modified_person']) : '';
    ($useUrl)           ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_url']) : '';
    ($useConnections)   ? printf('<th class="list">%s</th>', $PMF_LANG['ad_stat_report_connections']) : '';
?>
    </tr>
    </thead>
    <tbody>
<?php
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
        printf('<tr>');

        if ($useCategory) {
            printf('<td class="list">%s</td>', $row->category_name);
        }
        if ($useSubcategory) {
            $current = $row->category_id;
            $cat = 1;
            while ($cat > 0) {
                $cat = $category->categoryName[$current]['parent_id'];
                if ($cat != 0) {
                    $current = $cat;
                }
            }
            if ($row->category_id == $current) {
                printf('<td class="list">%s</td>', '&nbsp;');
            } else {
                printf('<td class="list">%s</td>', $category->categoryName[$current]['name']);
            }
        }
        //if ($useIdLinked) {
        //    printf('<td class="list">%s</td>', '');
        //}
        if ($useLanguage) {
            printf('<td class="list">%s</td>', $row->lang);
        }
        if ($useId) {
            printf('<td class="list">%s</td>', $row->id);
        }
        if ($useSticky) {
            printf('<td class="list">%s</td>', $row->sticky);
        }
        if ($useTitle) {
            printf('<td class="list">%s</td>', $row->thema);
        }
        if ($useCreadtionDate) {
            printf('<td class="list">%s</td>', PMF_Date::createIsoDate($row->datum));
        }
        if ($useOwner) {
            printf('<td class="list">%s</td>', $row->author);
        }
        if ($useLastModified) {
            printf('<td class="list">%s</td>', $row->display_name);
        }
        if ($useUrl) {
            $url = sprintf('<a href="../index.php?action=artikel&amp;cat=%d&amp;id=%d&amp;artlang=%s">Link</a>',
                $row->category_id,
                $row->id,
                $row->lang);
            printf('<td class="list">%s</td>', $url);
        }
        if ($useConnections) {
            printf('<td class="list">%s</td>', $row->visits);
        }

        printf('</tr>');
    }
?>
    </tbody>
    </table>
    <br />
    <form action="stat.reportcsv.php" method="post" style="display: inline;">
    <input type="hidden" name="report_category" id="report_category" value="<?php echo $useCategory; ?>" /></td>
    <input type="hidden" name="report_sub_category" id="report_sub_category" value="<?php echo $useSubcategory; ?>" /></td>
    <!--
    <input type="hidden" name="report_id_linked" id="report_id_linked" value="<?php echo $useIdLinked; ?>" /></td>
    //-->
    <input type="hidden" name="report_language" id="report_language" value="<?php echo $useLanguage; ?>" /></td>
    <input type="hidden" name="report_id" id="report_id" value="<?php echo $useId; ?>" /></td>
    <input type="hidden" name="report_sticky" id="report_sticky" value="<?php echo $useSticky; ?>" /></td>
    <input type="hidden" name="report_title" id="report_title" value="<?php echo $useTitle; ?>" /></td>
    <input type="hidden" name="report_creadtion_date" id="report_creadtion_date" value="<?php echo $useCreadtionDate; ?>" /></td>
    <input type="hidden" name="report_owner" id="report_owner" value="<?php echo $useOwner; ?>" /></td>
    <input type="hidden" name="report_last_modified_person" id="report_last_modified_person" class="radio" value="<?php echo $useLastModified; ?>">
    <input type="hidden" name="report_url" id="report_url" value="<?php echo $useUrl; ?>" /></td>
    <input type="hidden" name="report_connections" id="report_connections" value="<?php echo $useConnections; ?>" /></td>
    <div align="center">
        <input class="submit" type="submit" value="<?php print $PMF_LANG["ad_stat_report_make_csv"]; ?>" />
    </div>
    </form>
<?php
} else {
    print $PMF_LANG['err_NotAuth'];
}
