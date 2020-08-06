<?php

namespace XD\Narrowcasting\Models;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Versioned\Versioned;
use XD\Narrowcasting\Extensions\PresentationConfigExtension;
use XD\Narrowcasting\Slides\Slide;
use XD\Narrowcasting\GridField\GridFieldConfig_Slides;

/**
 * Class Presentation
 * @package XD\Narrowcasting\Models
 * @mixin PresentationConfigExtension
 *
 * @property string Title
 *
 * @method HasManyList Slides()
 */
class Presentation extends DataObject
{
    private static $table_name = 'Narrowcasting_Presentation';

    private static $db = [
        'Title' => 'Varchar'
    ];

    private static $has_many = [
        'Slides' => Slide::class,
    ];

    private static $cascade_duplicates = [
        'Slides'
    ];

    private static $owns = [
        'Slides'
    ];

    private static $extensions = [
        Versioned::class,
        PresentationConfigExtension::class
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('Slides');
        $fields->addFieldsToTab('Root.Main', [
            GridField::create('Slides', _t(__CLASS__ . '.Slides', 'Slides'), $this->Slides(), GridFieldConfig_Slides::create())
        ]);

        return $fields;
    }

    public function Link($action = null)
    {
        return Controller::join_links(array_filter([
            'display/presentation',
            $this->ID,
            $action
        ]));
    }

    public function AbsoluteLink($action = null){
        return Director::absoluteURL($this->Link($action));
    }
}
