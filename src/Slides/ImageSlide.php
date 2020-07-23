<?php

namespace XD\Narrowcasting\Slides;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;

/**
 * Class ImageSlide
 * @package XD\Narrowcasting\Models
 *
 * @property string BackgroundPosition
 * @property boolean BackgroundRepeat
 * @property boolean BackgroundOpacity
 * @property string BackgroundSize
 *
 * @method Image BackgroundImage()
 */
class ImageSlide extends Slide
{
    private static $table_name = 'Narrowcasting_ImageSlide';

    private static $db = [
        'BackgroundSize' => 'Enum("Cover,Contain","Cover")',
        'BackgroundOpacity' => 'Int',
    ];

    private static $has_one = [
        'BackgroundImage' => Image::class
    ];

    private static $defaults = [
        'BackgroundOpacity' => 100
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            DropdownField::create(
                'BackgroundSize',
                _t(__CLASS__ . '.BackgroundSize', 'Background size'),
                $this->dbObject('BackgroundSize')->enumValues()
            ),
        ]);

        // Slide settings
        $fields->addFieldsToTab('Root.Settings', [
            NumericField::create('BackgroundOpacity', _t(__CLASS__ . '.BackgroundOpacity', 'Background opacity (%)'))
                ->setAttribute('min', 0)
                ->setAttribute('max', 100),
        ]);

        return $fields;
    }

    public function getSlideConfig()
    {
        $config = parent::getSlideConfig();
        $hasBackground = $this->BackgroundImage() && $this->BackgroundImage()->exists();
        return array_merge($config, array_filter([
            'data-background-image' => $hasBackground ? $this->BackgroundImage()->Fill(1600, 900)->Link() : null,
            'data-background-size' => $hasBackground ? $this->BackgroundSize : null,
            'data-background-opacity' => $hasBackground ? ((int)$this->BackgroundOpacity / 100) : null,
            'data-background-position' => $hasBackground ? 'center' : null,
            'data-background-repeat' => $hasBackground ? 'no-repeat' : null,
        ]));
    }
}
