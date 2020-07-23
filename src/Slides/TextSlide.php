<?php

namespace XD\Narrowcasting\Slides;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

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

        return $fields;
    }
}
