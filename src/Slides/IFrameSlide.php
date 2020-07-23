<?php

namespace XD\Narrowcasting\Slides;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextareaField;

/**
 * Class IFrameSlide
 * @package XD\Narrowcasting\Models
 *
 * @property string BackgroundIframe
 * @property boolean BackgroundInteractive
 */
class IFrameSlide extends Slide
{
    private static $table_name = 'Narrowcasting_IFrameSlide';

    private static $db = [
        'BackgroundIframe' => 'Text',
        'BackgroundInteractive' => 'Boolean',
    ];

    private static $icon = 'font-icon-block-embed';

    public function getType()
    {
        return _t(__CLASS__ . '.Type', 'iFrame slide');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            TextareaField::create('BackgroundIframe', _t(__CLASS__ . '.BackgroundIframe', 'Background iframe')),
        ]);

        // Slide settings
        $fields->addFieldsToTab('Root.Settings', [
            CheckboxField::create('BackgroundInteractive', _t(__CLASS__ . '.BackgroundInteractive', 'Make iframe interactive')),
        ]);

        return $fields;
    }

    public function getSlideConfig()
    {
        $config = parent::getSlideConfig();
        return array_merge($config, array_filter([
            'data-background-iframe' => $this->BackgroundIframe ?: null,
            'data-background-interactive' => $this->BackgroundIframe ? $this->BackgroundInteractive : null,
        ]));
    }
}
