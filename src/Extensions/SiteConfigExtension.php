<?php

namespace XD\Narrowcasting\Extensions;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use XD\Narrowcasting\Models\Presentation;

/**
 * class SiteConfigExtension
 *
 * @method Presentation DefaultPresentation()
 */
class SiteConfigExtension extends DataExtension
{
    private static $has_one = [
        'DefaultPresentation' => Presentation::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.PresentationConfig', [
            DropdownField::create('DefaultPresentationID', _t(__CLASS__ . '.DefaultPresentation', 'Default presentation'), Presentation::get()->map())
        ]);
    }
}
