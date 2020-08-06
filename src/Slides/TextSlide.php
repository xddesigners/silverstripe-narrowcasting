<?php

namespace XD\Narrowcasting\Slides;

use DNADesign\Elemental\Models\ElementContent;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\OptionsetField;

/**
 * Class TextSlide
 * @package XD\Narrowcasting\Models
 *
 * @property string Content
 */
class TextSlide extends Slide
{
    private static $table_name = 'Narrowcasting_TextSlide';

    private static $db = [
        'Content' => 'HTMLText',
        'ImagePosition' => 'Enum("Left,Right","Right")',
        'ImageOrientation' => 'Enum("Portrait,Landscape","Portrait")'
    ];

    private static $has_one = [
        'Image' => Image::class
    ];

    private static $owns = [
        'Image'
    ];

    private static $defaults = [
        'ShowTitle' => 1,
        'ImagePosition' => 'Right'
    ];

    private static $icon = 'font-icon-block-content';

    public function getType()
    {
        return _t(__CLASS__ . '.Type', 'Text slide');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab('Root.Main', [
            HTMLEditorField::create('Content', _t(__CLASS__ . '.Content', 'Content')),
        ]);


        $fields->insertAfter('Image', $fields->fieldByName('Root.Main.ImagePosition'));

        $field = new OptionsetField('ImagePosition','Image position',$this->dbObject('ImagePosition')->enumValues());
        $field->addExtraClass('compact');
        $fields->insertAfter('Image',$field);

        $field = new OptionsetField('ImageOrientation','Image orientation',$this->dbObject('ImageOrientation')->enumValues());
        $field->addExtraClass('compact');
        $fields->insertAfter('ImagePosition',$field);

        return $fields;
    }
}
