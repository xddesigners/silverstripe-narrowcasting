<?php

namespace XD\Narrowcasting\Models;

use SilverStripe\Control\Director;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\ORM\HasManyList;
use SilverStripe\ORM\ValidationException;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use XD\Basic\Extensions\SiteConfigExtension;
use XD\Basic\Util;
use XD\Narrowcasting\GridField\GridFieldConfig_DisplayScheduleDay;

/**
 * Class Display
 * @package XD\Narrowcasting\Models
 *
 * @property string Title
 * @property string MacAddress
 * @property string KioskConnection
 * @property string KioskSSIDName
 * @property string KioskWPAPassword
 *
 * @property int DefaultPresentationID
 * @method Presentation DefaultPresentation
 *
 * @method HasManyList Schedule
 */
class Display extends DataObject
{
    private static $table_name = 'Narrowcasting_Display';

    private static $db = [
        'Title' => 'Varchar',
        'MacAddress' => 'Varchar', // obsolete
        'KioskConnection' => 'Enum("wired,wifi", "wired")',
        'KioskSSIDName' => 'Varchar',
        'KioskWPAPassword' => 'Varchar'
    ];

    private static $has_one = [
        'DefaultPresentation' => Presentation::class
    ];

    private static $has_many = [
        'Schedule' => DisplayScheduleDay::class
    ];

    private static $indexes = [
        'MacAddress' => true
    ];

    private static $summary_fields = [
        'Title',
        'ActivePresentationSummary' => 'Active presentation'
    ];

    private static $kiosk_config = [
        'browser' => 'chrome',
        'homepage_check' => 'De narrowcasting is op het moment niet beschikbaar',
        'disable_navigation_bar' => 'yes',
        'disable_input_devices' => 'yes',
        'refresh_webpage' => 1800, // site config ?
        'dhcp' => 'yes',
        'hide_mouse' => '5',
        //'scheduled_action' => 'Monday-17:00 Tuesday-17:00 Wednesday-17:00 Thursday-17:00 Friday-17:00 action:shutdown',
        //'rtc_wake' => 'Monday-08:00 Tuesday-08:00 Wednesday-08:00 Thursday-08:00 Friday-08:00'
        // 'homepage_append' => 'mac', // if using static homepage
        // 'additional_components' => '08-ssh.xzm', // enable ssh support
        // 'root_password' => '', // enable ssh support
        // 'session_idle_forced' => 60 // minutes
        // 'screen_settings' => '' // force a output e.g. hdmi  // site config or device config ?
        // 'rtc_wake' => '' // set a wake up schedule if supported by the device
        // 'scheduled_action' => '' // could be used for shutting down
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['Schedule']);
        if( $this->exists() ) {
            $fields->addFieldsToTab('Root.Main', [
                ReadonlyField::create('MacAddress', _t(__CLASS__ . '.MacAddress', 'Mac address')),
                GridField::create(
                    'Schedule',
                    _t(__CLASS__ . '.Schedule', 'Schedule'),
                    $this->Schedule(),
                    GridFieldConfig_DisplayScheduleDay::create($this->Schedule())
                )
            ]);
        }

        $fields->addFieldsToTab('Root.KioskConfig', [
            OptionsetField::create(
                'KioskConnection',
                _t(__CLASS__ . '.KioskConnection', 'Connection'),
                Util::friendlyEnum($this, 'KioskConnection')
            ),
            $ssid = TextField::create('KioskSSIDName', _t(__CLASS__ . '.KioskSSIDName', 'Wifi network name')),
            $wpa = TextField::create('KioskWPAPassword', _t(__CLASS__ . '.KioskWPAPassword', 'Wifi password')),
        ]);

        $ssid->displayIf('KioskConnection')->isEqualTo("wifi");
        $wpa->displayIf('KioskConnection')->isEqualTo("wifi");

        return $fields;
    }

    public function populateDefaults()
    {
        $display = parent::populateDefaults();
        if (empty($this->Title)) {
            $this->Title = _t(__CLASS__ . '.NewDisplay', 'New display');
        }

        return $display;
    }

    /**
     * @return Presentation|null
     */
    public function getActivePresentation()
    {
        /** @var DisplayScheduleDay $schedule */
        $schedule = $this->Schedule()->find('Day', date('l'));

        // check for a planned presentation
        if ($schedule) {
            $now = DBDatetime::now()->Format('HH:mm:ss');
            /** @var DisplayScheduleTimePeriod $plannedPresentation */
            $plannedPresentation = $schedule->TimePeriods()->filter([
                'From:LessThanOrEqual' => $now,
                'Till:GreaterThanOrEqual' => $now
            ])->first();

            if ($plannedPresentation && ($presentation = $plannedPresentation->Presentation()) && $plannedPresentation->exists()) {
                return $presentation;
            }
        }

        // return the presentation set for today
        if ($schedule && ($presentation = $schedule->Presentation()) && $presentation->exists()) {
            return $presentation;
        }

        // fallback to default presentation
        if (($presentation = $this->DefaultPresentation()) && $presentation->exists()) {
            return $presentation;
        }

        /** @var SiteConfigExtension $siteConfig */
        $siteConfig = SiteConfig::current_site_config();
        if (($presentation = $siteConfig->DefaultPresentation()) && $presentation->exists()) {
            return $presentation;
        }

        return null;
    }

    /**
     * Returns a summary for a active presentation
     *
     * @return string
     */
    public function getActivePresentationSummary()
    {
        if ($presentation = $this->getActivePresentation()) {
            return $presentation->getTitle();
        }

        return _t(__CLASS__ . '.NoActivePresentation', 'No presentation active');
    }

    /**
     * Get the config for this display
     * For config options
     * @see https://porteus-kiosk.org/parameters.html
     *
     * @return ArrayList
     */
    public function getKioskConfig()
    {
        // todo merge with global settings, e.g. wallpaper
        $config = array_merge(self::config()->get('kiosk_config'), [
            'kiosk_config' => $this->getKioskConfigLink(),
            'homepage' => $this->AbsoluteLink(),
            'wallpaper' => Director::absoluteBaseURL() . 'assets/wallpaper/wallpaper.jpg',
            'whitelist' => Director::absoluteBaseURL(),
            'timezone' => date_default_timezone_get(),
            'connection' => $this->KioskConnection
        ]);

        if ($this->KioskConnection === 'wifi') {
            $config = array_merge($config, [
                //'network_interface' => 'wlan0',
                'wifi_encryption' => 'wpa',
                'ssid_name' => $this->KioskSSIDName,
                'wpa_password' => $this->KioskWPAPassword
            ]);
        }

        $settings = new ArrayList();
        foreach ($config as $param => $value) {
            $settings->add(new ArrayData([
                'Param' => $param,
                'Value' => $value
            ]));
        }

        return $settings;
    }

    public function Link()
    {
        return '/display/view/' . $this->ID;
    }

    public function AbsoluteLink()
    {
        return Director::absoluteURL($this->Link());
    }

    public function getKioskConfigLink()
    {
        return Director::absoluteURL("/display/kioskconfig/$this->ID");
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if ($this->exists() && !$this->Schedule()->exists()) {
            $this->createSchedule();
        }
    }

    /**
     * Set up the displays presentation schedule
     */
    private function createSchedule()
    {
        $days = DisplayScheduleDay::singleton()->dbObject('Day')->enumValues();
        foreach ($days as $day) {
            $scheduleDay = DisplayScheduleDay::create();
            $scheduleDay->Day = $day;
            $this->Schedule()->add($scheduleDay);
        }
    }

    /**
     * Register a device with the given mac address
     *
     * @param $macAddress
     * @return Display
     * @throws ValidationException
     */
    public static function register($macAddress)
    {
        $display = self::create([
            'MacAddress' => $macAddress
        ]);

        $display->write();
        return $display;
    }

    /**
     * Displays can only be created by the system
     *
     * @param null $member
     * @param array $context
     * @return bool
     */
    public function canCreate($member = null, $context = array())
    {
        return true;
    }
}
