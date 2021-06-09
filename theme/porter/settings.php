<?php
// This file is part of Ranking block for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme porter block settings file
 *
 * @package    theme_porter
 * @copyright  2017 Willian Mano http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// This is used for performance, we don't need to know about these settings on every page in Moodle, only when
// we are looking at the admin settings pages.
if ($ADMIN->fulltree) {

    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
    $settings = new theme_boost_admin_settingspage_tabs('themesettingporter', get_string('configtitle', 'theme_porter'));

    /*
    * ----------------------
    * General settings tab
    * ----------------------
    */
    $page = new admin_settingpage('theme_porter_general', get_string('generalsettings', 'theme_porter'));

    // Logo file setting.
    $name = 'theme_porter/logo';
    $title = get_string('logo', 'theme_porter');
    $description = get_string('logodesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Favicon setting.
    $name = 'theme_porter/favicon';
    $title = get_string('favicon', 'theme_porter');
    $description = get_string('favicondesc', 'theme_porter');
    $opts = array('accepted_types' => array('.ico'), 'maxfiles' => 1);
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset.
    $name = 'theme_porter/preset';
    $title = get_string('preset', 'theme_porter');
    $description = get_string('preset_desc', 'theme_porter');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_porter', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_porter/presetfiles';
    $title = get_string('presetfiles', 'theme_porter');
    $description = get_string('presetfiles_desc', 'theme_porter');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Login page background image.
    $name = 'theme_porter/loginbgimg';
    $title = get_string('loginbgimg', 'theme_porter');
    $description = get_string('loginbgimg_desc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbgimg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $brand-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_porter/brandcolor';
    $title = get_string('brandcolor', 'theme_porter');
    $description = get_string('brandcolor_desc', 'theme_porter');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $navbar-header-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_porter/navbarheadercolor';
    $title = get_string('navbarheadercolor', 'theme_porter');
    $description = get_string('navbarheadercolor_desc', 'theme_porter');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $navbar-bg.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_porter/navbarbg';
    $title = get_string('navbarbg', 'theme_porter');
    $description = get_string('navbarbg_desc', 'theme_porter');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $navbar-bg-hover.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_porter/navbarbghover';
    $title = get_string('navbarbghover', 'theme_porter');
    $description = get_string('navbarbghover_desc', 'theme_porter');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Course format option.
    $name = 'theme_porter/coursepresentation';
    $title = get_string('coursepresentation', 'theme_porter');
    $description = get_string('coursepresentationdesc', 'theme_porter');
    $options = [];
    $options[1] = get_string('coursedefault', 'theme_porter');
    $options[2] = get_string('coursecover', 'theme_porter');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_porter/courselistview';
    $title = get_string('courselistview', 'theme_porter');
    $description = get_string('courselistviewdesc', 'theme_porter');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    /*
    * ----------------------
    * Advanced settings tab
    * ----------------------
    */
    $page = new admin_settingpage('theme_porter_advanced', get_string('advancedsettings', 'theme_porter'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_porter/scsspre',
        get_string('rawscsspre', 'theme_porter'), get_string('rawscsspre_desc', 'theme_porter'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_porter/scss', get_string('rawscss', 'theme_porter'),
        get_string('rawscss_desc', 'theme_porter'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Google analytics block.
    $name = 'theme_porter/googleanalytics';
    $title = get_string('googleanalytics', 'theme_porter');
    $description = get_string('googleanalyticsdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    /*
    * -----------------------
    * Frontpage settings tab
    * -----------------------
    */
    $page = new admin_settingpage('theme_porter_frontpage', get_string('frontpagesettings', 'theme_porter'));

    // Disable bottom footer.
    $name = 'theme_porter/disablefrontpageloginbox';
    $title = get_string('disablefrontpageloginbox', 'theme_porter');
    $description = get_string('disablefrontpageloginboxdesc', 'theme_porter');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Disable teachers from cards.
    $name = 'theme_porter/disableteacherspic';
    $title = get_string('disableteacherspic', 'theme_porter');
    $description = get_string('disableteacherspicdesc', 'theme_porter');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);

    // Headerimg file setting.
    $name = 'theme_porter/headerimg';
    $title = get_string('headerimg', 'theme_porter');
    $description = get_string('headerimgdesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'headerimg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Bannerheading.
    $name = 'theme_porter/bannerheading';
    $title = get_string('bannerheading', 'theme_porter');
    $description = get_string('bannerheadingdesc', 'theme_porter');
    $default = 'Perfect Learning System';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Bannercontent.
    $name = 'theme_porter/bannercontent';
    $title = get_string('bannercontent', 'theme_porter');
    $description = get_string('bannercontentdesc', 'theme_porter');
    $default = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_porter/displaymarketingbox';
    $title = get_string('displaymarketingbox', 'theme_porter');
    $description = get_string('displaymarketingboxdesc', 'theme_porter');
    $default = 1;
    $choices = array(0 => 'No', 1 => 'Yes');
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $page->add($setting);

    // Marketing1icon.
    $name = 'theme_porter/marketing1icon';
    $title = get_string('marketing1icon', 'theme_porter');
    $description = get_string('marketing1icondesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing1icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1heading.
    $name = 'theme_porter/marketing1heading';
    $title = get_string('marketing1heading', 'theme_porter');
    $description = get_string('marketing1headingdesc', 'theme_porter');
    $default = 'We host';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1subheading.
    $name = 'theme_porter/marketing1subheading';
    $title = get_string('marketing1subheading', 'theme_porter');
    $description = get_string('marketing1subheadingdesc', 'theme_porter');
    $default = 'your MOODLE';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1content.
    $name = 'theme_porter/marketing1content';
    $title = get_string('marketing1content', 'theme_porter');
    $description = get_string('marketing1contentdesc', 'theme_porter');
    $default = 'Moodle hosting in a powerful cloud infrastructure';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing1url.
    $name = 'theme_porter/marketing1url';
    $title = get_string('marketing1url', 'theme_porter');
    $description = get_string('marketing1urldesc', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2icon.
    $name = 'theme_porter/marketing2icon';
    $title = get_string('marketing2icon', 'theme_porter');
    $description = get_string('marketing2icondesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing2icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2heading.
    $name = 'theme_porter/marketing2heading';
    $title = get_string('marketing2heading', 'theme_porter');
    $description = get_string('marketing2headingdesc', 'theme_porter');
    $default = 'Consulting';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2subheading.
    $name = 'theme_porter/marketing2subheading';
    $title = get_string('marketing2subheading', 'theme_porter');
    $description = get_string('marketing2subheadingdesc', 'theme_porter');
    $default = 'for your company';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2content.
    $name = 'theme_porter/marketing2content';
    $title = get_string('marketing2content', 'theme_porter');
    $description = get_string('marketing2contentdesc', 'theme_porter');
    $default = 'Moodle consulting and training for you';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing2url.
    $name = 'theme_porter/marketing2url';
    $title = get_string('marketing2url', 'theme_porter');
    $description = get_string('marketing2urldesc', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3icon.
    $name = 'theme_porter/marketing3icon';
    $title = get_string('marketing3icon', 'theme_porter');
    $description = get_string('marketing3icondesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing3icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3heading.
    $name = 'theme_porter/marketing3heading';
    $title = get_string('marketing3heading', 'theme_porter');
    $description = get_string('marketing3headingdesc', 'theme_porter');
    $default = 'Development';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3subheading.
    $name = 'theme_porter/marketing3subheading';
    $title = get_string('marketing3subheading', 'theme_porter');
    $description = get_string('marketing3subheadingdesc', 'theme_porter');
    $default = 'themes and plugins';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3content.
    $name = 'theme_porter/marketing3content';
    $title = get_string('marketing3content', 'theme_porter');
    $description = get_string('marketing3contentdesc', 'theme_porter');
    $default = 'We develop themes and plugins as your desires';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing3url.
    $name = 'theme_porter/marketing3url';
    $title = get_string('marketing3url', 'theme_porter');
    $description = get_string('marketing3urldesc', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4icon.
    $name = 'theme_porter/marketing4icon';
    $title = get_string('marketing4icon', 'theme_porter');
    $description = get_string('marketing4icondesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing4icon', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4heading.
    $name = 'theme_porter/marketing4heading';
    $title = get_string('marketing4heading', 'theme_porter');
    $description = get_string('marketing4headingdesc', 'theme_porter');
    $default = 'Support';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4subheading.
    $name = 'theme_porter/marketing4subheading';
    $title = get_string('marketing4subheading', 'theme_porter');
    $description = get_string('marketing4subheadingdesc', 'theme_porter');
    $default = 'we give you answers';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4content.
    $name = 'theme_porter/marketing4content';
    $title = get_string('marketing4content', 'theme_porter');
    $description = get_string('marketing4contentdesc', 'theme_porter');
    $default = 'MOODLE specialized support';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Marketing4url.
    $name = 'theme_porter/marketing4url';
    $title = get_string('marketing4url', 'theme_porter');
    $description = get_string('marketing4urldesc', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Enable or disable Slideshow settings.
    $name = 'theme_porter/sliderenabled';
    $title = get_string('sliderenabled', 'theme_porter');
    $description = get_string('sliderenableddesc', 'theme_porter');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $page->add($setting);

    // Enable slideshow on frontpage guest page.
    $name = 'theme_porter/sliderfrontpage';
    $title = get_string('sliderfrontpage', 'theme_porter');
    $description = get_string('sliderfrontpagedesc', 'theme_porter');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_porter/slidercount';
    $title = get_string('slidercount', 'theme_porter');
    $description = get_string('slidercountdesc', 'theme_porter');
    $default = 1;
    $options = array();
    for ($i = 0; $i < 13; $i++) {
        $options[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // If we don't have an slide yet, default to the preset.
    $slidercount = get_config('theme_porter', 'slidercount');

    if (!$slidercount) {
        $slidercount = 1;
    }

    for ($sliderindex = 1; $sliderindex <= $slidercount; $sliderindex++) {
        $fileid = 'sliderimage' . $sliderindex;
        $name = 'theme_porter/sliderimage' . $sliderindex;
        $title = get_string('sliderimage', 'theme_porter');
        $description = get_string('sliderimagedesc', 'theme_porter');
        $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
        $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid, 0, $opts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_porter/slidertitle' . $sliderindex;
        $title = get_string('slidertitle', 'theme_porter');
        $description = get_string('slidertitledesc', 'theme_porter');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
        $page->add($setting);

        $name = 'theme_porter/slidercap' . $sliderindex;
        $title = get_string('slidercaption', 'theme_porter');
        $description = get_string('slidercaptiondesc', 'theme_porter');
        $default = '';
        $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
        $page->add($setting);
    }

    // Enable or disable Slideshow settings.
    $name = 'theme_porter/numbersfrontpage';
    $title = get_string('numbersfrontpage', 'theme_porter');
    $description = get_string('numbersfrontpagedesc', 'theme_porter');
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    // Enable sponsors on frontpage guest page.
    $name = 'theme_porter/sponsorsfrontpage';
    $title = get_string('sponsorsfrontpage', 'theme_porter');
    $description = get_string('sponsorsfrontpagedesc', 'theme_porter');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_porter/sponsorstitle';
    $title = get_string('sponsorstitle', 'theme_porter');
    $description = get_string('sponsorstitledesc', 'theme_porter');
    $default = get_string('sponsorstitledefault', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_porter/sponsorssubtitle';
    $title = get_string('sponsorssubtitle', 'theme_porter');
    $description = get_string('sponsorssubtitledesc', 'theme_porter');
    $default = get_string('sponsorssubtitledefault', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_porter/sponsorscount';
    $title = get_string('sponsorscount', 'theme_porter');
    $description = get_string('sponsorscountdesc', 'theme_porter');
    $default = 1;
    $options = array();
    for ($i = 0; $i < 5; $i++) {
        $options[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // If we don't have an slide yet, default to the preset.
    $sponsorscount = get_config('theme_porter', 'sponsorscount');

    if (!$sponsorscount) {
        $sponsorscount = 1;
    }

    for ($sponsorsindex = 1; $sponsorsindex <= $sponsorscount; $sponsorsindex++) {
        $fileid = 'sponsorsimage' . $sponsorsindex;
        $name = 'theme_porter/sponsorsimage' . $sponsorsindex;
        $title = get_string('sponsorsimage', 'theme_porter');
        $description = get_string('sponsorsimagedesc', 'theme_porter');
        $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
        $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid, 0, $opts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_porter/sponsorsurl' . $sponsorsindex;
        $title = get_string('sponsorsurl', 'theme_porter');
        $description = get_string('sponsorsurldesc', 'theme_porter');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
        $page->add($setting);
    }

    // Enable clients on frontpage guest page.
    $name = 'theme_porter/clientsfrontpage';
    $title = get_string('clientsfrontpage', 'theme_porter');
    $description = get_string('clientsfrontpagedesc', 'theme_porter');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $page->add($setting);

    $name = 'theme_porter/clientstitle';
    $title = get_string('clientstitle', 'theme_porter');
    $description = get_string('clientstitledesc', 'theme_porter');
    $default = get_string('clientstitledefault', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_porter/clientssubtitle';
    $title = get_string('clientssubtitle', 'theme_porter');
    $description = get_string('clientssubtitledesc', 'theme_porter');
    $default = get_string('clientssubtitledefault', 'theme_porter');
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $page->add($setting);

    $name = 'theme_porter/clientscount';
    $title = get_string('clientscount', 'theme_porter');
    $description = get_string('clientscountdesc', 'theme_porter');
    $default = 1;
    $options = array();
    for ($i = 0; $i < 5; $i++) {
        $options[$i] = $i;
    }
    $setting = new admin_setting_configselect($name, $title, $description, $default, $options);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // If we don't have an slide yet, default to the preset.
    $clientscount = get_config('theme_porter', 'clientscount');

    if (!$clientscount) {
        $clientscount = 1;
    }

    for ($clientsindex = 1; $clientsindex <= $clientscount; $clientsindex++) {
        $fileid = 'clientsimage' . $clientsindex;
        $name = 'theme_porter/clientsimage' . $clientsindex;
        $title = get_string('clientsimage', 'theme_porter');
        $description = get_string('clientsimagedesc', 'theme_porter');
        $opts = array('accepted_types' => array('.png', '.jpg', '.gif', '.webp', '.tiff', '.svg'), 'maxfiles' => 1);
        $setting = new admin_setting_configstoredfile($name, $title, $description, $fileid, 0, $opts);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $name = 'theme_porter/clientsurl' . $clientsindex;
        $title = get_string('clientsurl', 'theme_porter');
        $description = get_string('clientsurldesc', 'theme_porter');
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
        $page->add($setting);
    }

    $settings->add($page);

    /*
    * --------------------
    * Footer settings tab
    * --------------------
    */
    $page = new admin_settingpage('theme_porter_footer', get_string('footersettings', 'theme_porter'));

    $name = 'theme_porter/getintouchcontent';
    $title = get_string('getintouchcontent', 'theme_porter');
    $description = get_string('getintouchcontentdesc', 'theme_porter');
    $default = 'Conecti.me';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Website.
    $name = 'theme_porter/website';
    $title = get_string('website', 'theme_porter');
    $description = get_string('websitedesc', 'theme_porter');
    $default = 'http://conecti.me';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Mobile.
    $name = 'theme_porter/mobile';
    $title = get_string('mobile', 'theme_porter');
    $description = get_string('mobiledesc', 'theme_porter');
    $default = 'Mobile : +55 (98) 00123-45678';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Mail.
    $name = 'theme_porter/mail';
    $title = get_string('mail', 'theme_porter');
    $description = get_string('maildesc', 'theme_porter');
    $default = 'willianmano@conecti.me';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Facebook url setting.
    $name = 'theme_porter/facebook';
    $title = get_string('facebook', 'theme_porter');
    $description = get_string('facebookdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Twitter url setting.
    $name = 'theme_porter/twitter';
    $title = get_string('twitter', 'theme_porter');
    $description = get_string('twitterdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Linkdin url setting.
    $name = 'theme_porter/linkedin';
    $title = get_string('linkedin', 'theme_porter');
    $description = get_string('linkedindesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Youtube url setting.
    $name = 'theme_porter/youtube';
    $title = get_string('youtube', 'theme_porter');
    $description = get_string('youtubedesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Instagram url setting.
    $name = 'theme_porter/instagram';
    $title = get_string('instagram', 'theme_porter');
    $description = get_string('instagramdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Whatsapp url setting.
    $name = 'theme_porter/whatsapp';
    $title = get_string('whatsapp', 'theme_porter');
    $description = get_string('whatsappdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Top footer background image.
    $name = 'theme_porter/topfooterimg';
    $title = get_string('topfooterimg', 'theme_porter');
    $description = get_string('topfooterimgdesc', 'theme_porter');
    $opts = array('accepted_types' => array('.png', '.jpg', '.svg'));
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'topfooterimg', 0, $opts);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Forum page.
    $settingpage = new admin_settingpage('theme_porter_forum', get_string('forumsettings', 'theme_porter'));

    $settingpage->add(new admin_setting_heading('theme_porter_forumheading', null,
            format_text(get_string('forumsettingsdesc', 'theme_porter'), FORMAT_MARKDOWN)));

    // Enable custom template.
    $name = 'theme_porter/forumcustomtemplate';
    $title = get_string('forumcustomtemplate', 'theme_porter');
    $description = get_string('forumcustomtemplatedesc', 'theme_porter');
    $default = 0;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $settingpage->add($setting);

    // Header setting.
    $name = 'theme_porter/forumhtmlemailheader';
    $title = get_string('forumhtmlemailheader', 'theme_porter');
    $description = get_string('forumhtmlemailheaderdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settingpage->add($setting);

    // Footer setting.
    $name = 'theme_porter/forumhtmlemailfooter';
    $title = get_string('forumhtmlemailfooter', 'theme_porter');
    $description = get_string('forumhtmlemailfooterdesc', 'theme_porter');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settingpage->add($setting);

    $settings->add($settingpage);
}
