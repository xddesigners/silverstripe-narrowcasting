<?php

namespace XD\Narrowcasting\Models;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Versioned\Versioned;
use XD\Narrowcasting\Extensions\PresentationConfigExtension;
use XD\Narrowcasting\Slides\Slide;

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

    private static $extensions = [
        Versioned::class,
        PresentationConfigExtension::class
    ];

    public function getCMSFields(){
        $fields = parent::getCMSFields();
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
