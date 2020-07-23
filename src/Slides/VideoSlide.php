<?php

namespace XD\Narrowcasting\Slides;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;

/**
 * Class VideoSlide
 * @package XD\Narrowcasting\Models
 *
 * @property string BackgroundVideo
 * @property boolean BackgroundVideoLoop
 * @property boolean BackgroundVideoMuted
 * @property string BackgroundSize
 * @property int BackgroundOpacity
 */
class VideoSlide extends Slide
{
    private static $table_name = 'Narrowcasting_VideoSlide';

    private static $db = [
        'BackgroundVideo' => 'Varchar',
        'BackgroundVideoLoop' => 'Boolean',
        'BackgroundVideoMuted' => 'Boolean',
        'BackgroundSize' => 'Enum("cover,contain","cover")',
        'BackgroundOpacity' => 'Int',
    ];

    private static $defaults = [
        'BackgroundVideoLoop' => true,
        'BackgroundVideoMuted' => true,
        'BackgroundOpacity' => 100
    ];

    private static $icon = 'font-icon-block-media';

    public function getType()
    {
        return _t(__CLASS__ . '.Type', 'Video slide');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('BackgroundVideo', _t(__CLASS__ . '.BackgroundVideo', 'BackgroundVideo')),
            CheckboxField::create('BackgroundVideoLoop', _t(__CLASS__ . '.BackgroundVideoLoop', 'Loop background video')),
            CheckboxField::create('BackgroundVideoMuted', _t(__CLASS__ . '.BackgroundVideoMuted', 'Mute background video')),
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
        $hasVideo = !empty($this->BackgroundVideo);
        return array_merge($config, array_filter([
            'data-background-video' => $this->BackgroundVideo ?: null,
            'data-background-video-loop' => $hasVideo ? $this->BackgroundVideoLoop : null,
            'data-background-video-muted' => $hasVideo ? $this->BackgroundVideoMuted : null,
            'data-background-size' => $hasVideo ? $this->BackgroundSize : null,
            'data-background-opacity' => $hasVideo ? ((int)$this->BackgroundOpacity / 100) : null,
        ]));
    }
}
