<?php

namespace XD\Narrowcasting\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class PresentationConfigExtension
 * @package XD\Narrowcasting\Extensions
 * @property PresentationConfigExtension|DataObject $owner
 *
 * @property boolean Controls
 * @property boolean Progress
 * @property boolean SlideNumber
 * @property boolean Loop
 * @property boolean Shuffle
 * @property boolean AutoPlayMedia
 * @property boolean AutoSlide
 * @property boolean Transition
 * @property boolean TransitionSpeed
 * @property boolean BackgroundTransition
 */
class PresentationConfigExtension extends DataExtension
{
    private static $db = [
        'Controls' => 'Boolean',
        'Progress' => 'Boolean',
        'SlideNumber' => 'Boolean', // can also be formatted string
        'Loop' => 'Boolean',
        'Shuffle' => 'Boolean',
        'AutoPlayMedia' => 'Boolean', // should be nullable
        'AutoSlide' => 'Int',
        'Transition' => 'Enum("none,fade,slide,convex,concave,zoom","slide")',
        'TransitionSpeed' => 'Enum("default,fast,slow", "default")',
        'BackgroundTransition' => 'Enum("none,fade,slide,convex,concave,zoom","slide")',
    ];

    private static $presentation_default_config = [
        'controls' => false, // settable || if dev || if admin
        'controlsTutorial' => false,
        'controlsLayout' => 'bottom-right',
        'controlsBackArrows' => 'faded',
        'progress' => false, // settable
        'slideNumber' => false, // settable
        'hash' => false,
        'history' => false,
        'keyboard' => true,
        'overview' => false,
        'center' => false,
        'touch' => false,
        'loop' => true, // settable
        'rtl' => false,
        'navigationMode' => 'default',
        'shuffle' => false, // settable
        'fragments' => true,
        'fragmentInURL' => false,
        'embedded' => false,
        'help' => false,
        'showNotes' => false,
        'autoPlayMedia' => true,  // settable
        'preloadIframes' => true,
        'autoSlide' => 6000, // settable
        'autoSlideStoppable' => true,
        // 'autoSlideMethod' => 'Reveal.navigateNext',
        'defaultTiming' => 0,
        'totalTime' => 0,
        'minimumTimePerSlide' => 0,
        'mouseWheel' => false,
        'hideInactiveCursor' => true,
        'hideCursorTime' => 1000,
        'hideAddressBar' => true,
        'previewLinks' => false,
        'transition' => 'slide', // settable: none/fade/slide/convex/concave/zoom
        'transitionSpeed' => 'default', // settable:  default/fast/slow
        'backgroundTransition' => 'fade', // settable:  none/fade/slide/convex/concave/zoom
        'viewDistance' => 3,
        'mobileViewDistance' => 2,
        'parallaxBackgroundImage' => '', // settable
        'parallaxBackgroundSize' => '', // settable
        'parallaxBackgroundHorizontal' => null,
        'parallaxBackgroundVertical' => null,
        'display' => 'block',
        'width' => 1920,
        'height' => 1080,
	    'margin' => 0
    ];

    /**
     * Populate the defaults based on the yml config:
     *   presentation_default_config
     */
    public function populateDefaults()
    {
        $defaults = [];
        if ($this->owner instanceof SiteConfig) {
            $defaults = $this->owner->config()->get('presentation_default_config');
        } elseif (($siteConfig = SiteConfig::current_site_config()) && $siteConfig->hasExtension(self::class)) {
            $siteConfig = SiteConfig::current_site_config();
            $defaults = $siteConfig->getPresentationConfig();
        }

        foreach ($defaults as $key => $value) {
            if ($this->owner->hasField(ucfirst($key))) {
                $this->owner->setField(ucfirst($key), $value);
            }
        }
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.PresentationConfig', [
            CheckboxField::create('Controls', _t(__CLASS__ . '.Controls', 'Controls')),
            CheckboxField::create('Progress', _t(__CLASS__ . '.Progress', 'Progress')),
            CheckboxField::create('SlideNumber', _t(__CLASS__ . '.SlideNumber', 'Slide number')),
            CheckboxField::create('Loop', _t(__CLASS__ . '.Loop', 'Loop')),
            CheckboxField::create('Shuffle', _t(__CLASS__ . '.Shuffle', 'Shuffle')),
            CheckboxField::create('AutoPlayMedia', _t(__CLASS__ . '.AutoPlayMedia', 'Auto play media')),
            NumericField::create('AutoSlide', _t(__CLASS__ . '.AutoSlide', 'Auto slide')),
            DropdownField::create(
                'Transition',
                _t(__CLASS__ . '.Transition', 'Transition'),
                $this->owner->dbObject('Transition')->enumValues()
            ),
            DropdownField::create(
                'TransitionSpeed',
                _t(__CLASS__ . '.TransitionSpeed', 'Transition speed'),
                $this->owner->dbObject('TransitionSpeed')->enumValues()
            ),
            DropdownField::create(
                'BackgroundTransition',
                _t(__CLASS__ . '.BackgroundTransition', 'Background transition'),
                $this->owner->dbObject('BackgroundTransition')->enumValues()
            ),
        ]);
    }

    public function getPresentationConfig()
    {
        $defaultConfig = $this->owner->config()->get('presentation_default_config');
        $settableValues = [
            'controls' => (bool) $this->owner->Controls,
            'autoSlideStoppable' => (bool) $this->owner->Controls,
            'progress' => (bool) $this->owner->Progress,
            'slideNumber' => (bool) $this->owner->SlideNumber,
            'loop' => (bool) $this->owner->Loop,
            'shuffle' => (bool) $this->owner->Shuffle,
            'autoPlayMedia' => (bool) $this->owner->AutoPlayMedia,
            'autoSlide' => $this->owner->AutoSlide,
            'transition' => $this->owner->Transition,
            'transitionSpeed' => $this->owner->TransitionSpeed,
            'backgroundTransition' => $this->owner->BackgroundTransition,
        ];

        return array_merge($defaultConfig, $settableValues);
    }
}