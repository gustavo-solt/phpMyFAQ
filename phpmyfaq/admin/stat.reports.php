<?php
/**
 * The report statistics page
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
?>
    <h2><?php print $PMF_LANG["ad_stat_report"]; ?></h2>

    <form action="?action=reportshow" method="post" style="display: inline;">
    <fieldset>
    <legend><?php print $PMF_LANG["ad_stat_report_fields"]; ?></legend>
    <table>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_category"]; ?>:</label></td>
            <td><input type="checkbox" name="report_category" id="report_category" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_sub_category"]; ?>:</label></td>
            <td><input type="checkbox" name="report_sub_category" id="report_sub_category" class="radio" checked="checked" value="1" /></td>
        </tr>
<!--
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_id_linked"]; ?>:</label></td>
            <td><input type="checkbox" name="report_id_linked" id="report_id_linked" class="radio" checked="checked" value="1" /></td>
        </tr>
//-->
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_language"]; ?>:</label></td>
            <td><input type="checkbox" name="report_language" id="report_language" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_id"]; ?>:</label></td>
            <td><input type="checkbox" name="report_id" id="report_id" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_sticky"]; ?>:</label></td>
            <td><input type="checkbox" name="report_sticky" id="report_sticky" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_title"]; ?>:</label></td>
            <td><input type="checkbox" name="report_title" id="report_title" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_creadtion_date"]; ?>:</label></td>
            <td><input type="checkbox" name="report_creadtion_date" id="report_creadtion_date" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_owner"]; ?>:</label></td>
            <td><input type="checkbox" name="report_owner" id="report_owner" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_last_modified_person"]; ?>:</label></td>
            <td><input type="checkbox" name="report_last_modified_person" id="report_last_modified_person" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_url"]; ?>:</label></td>
            <td><input type="checkbox" name="report_url" id="report_url" class="radio" checked="checked" value="1" /></td>
        </tr>
        <tr>
            <td><label class="left"><?php print $PMF_LANG["ad_stat_report_connections"]; ?>:</label></td>
            <td><input type="checkbox" name="report_connections" id="report_connections" class="radio" checked="checked" value="1" /></td>
        </tr>
    </table>
    <br />
    <div align="center">
        <input class="submit" type="submit" value="<?php print $PMF_LANG["ad_stat_report_make_report"]; ?>" />
    </div>

    </fieldset>
    </form>
<?php
} else {
    print $PMF_LANG["err_NotAuth"];
}
