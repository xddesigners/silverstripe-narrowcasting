<?php

namespace XD\Narrowcasting\Slides;

use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use XD\Narrowcasting\Models\Presentation;

/**
 * Class Slide
 * @package XD\Narrowcasting\Models
 *
 * @property string Title
 * @property int AutoSlide
 * @property string Transition
 * @property string TransitionSpeed
 * @property string BackgroundColor
 *
 * @method Presentation Parent()
 */
class Slide extends DataObject
{
    private static $table_name = 'Narrowcasting_Slide';

    private static $db = [
        'Title' => 'Varchar',
        'AutoSlide' => 'Int',
        'Transition' => 'Enum("none,fade,slide,convex,concave,zoom","none")',
        'TransitionSpeed' => 'Enum("default,fast,slow","default")',
        'BackgroundColor' => 'Varchar',
    ];

    private static $casting = [
        'SlideConfigAttributes' => 'HTMLFragment',
        'getSlideConfigAttributes' => 'HTMLFragment'
    ];

    private static $has_one = [
        'Parent' => Presentation::class
    ];

    public function getType()
    {
        $shortClassName = ClassInfo::shortName($this->ClassName);
        return _t(__CLASS__ . '.Type', $shortClassName);
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['ParentID']);

        if ($this->ClassName === Slide::class) {
            $fields = new FieldList();
            $fields->push(DropdownField::create(
                'ClassName',
                _t(__CLASS__ . '.CreateSlide', 'Create slide type'),
                $this->getAvailableSlides())
            );
            return $fields;
        }

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', _t(__CLASS__ . '.Title', 'Title')),
        ]);

        // Slide settings
        $fields->addFieldsToTab('Root.Settings', [
            NumericField::create('AutoSlide', _t(__CLASS__ . '.AutoSlide', 'Slide duration in seconds'))
                ->setDescription(_t(__CLASS__ . '.AutoSlideDescription', 'If you leave this value "0" the default duration will be used')),
            DropdownField::create(
                'Transition',
                _t(__CLASS__ . '.Transition', 'Transition'),
                $this->dbObject('Transition')->enumValues()
            ),
            DropdownField::create(
                'TransitionSpeed',
                _t(__CLASS__ . '.TransitionSpeed', 'Transition speed'),
                $this->dbObject('TransitionSpeed')->enumValues()
            ),
            TextField::create('BackgroundColor', _t(__CLASS__ . '.BackgroundColor', 'Background color'))
        ]);

        return $fields;
    }

    public function getSlideConfig()
    {
        $config = array_filter([
            'data-autoslide' => ($autoSlide = $this->AutoSlide) > 0 ? ($autoSlide * 1000) : null,
            'data-transition' => ($transition = $this->Transition) !== 'none' ? $transition : 'none',
            'data-Transition-speed' => ($speed = $this->TransitionSpeed) !== 'default' ? $speed : null,
            'data-background-color' => $this->BackgroundColor ?: null
        ]);

        $this->extend('updateSlideConfig', $config);
        return $config;
    }

    public function getSlideConfigAttributes()
    {
        $parts = array();
        foreach ($this->getSlideConfig() as $name => $value) {
            if ($value === true) {
                $value = $name;
            } else {
                if (is_scalar($value)) {
                    $value = (string) $value;
                } else {
                    $value = json_encode($value);
                }
            }

            $parts[] = sprintf('%s="%s"', Convert::raw2att($name), Convert::raw2att($value));
        }

        return implode(' ', $parts);
    }

    public function Link()
    {
        if (($presentation = $this->Parent()) && $presentation->exists()) {
            return $presentation->Link($this->ID);
        }

        return null;
    }

    public function AbsoluteLink()
    {
        return Director::absoluteURL($this->Link());
    }

    public function getBemClassName()
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', ClassInfo::shortName($this)));
    }

    public static function getAvailableSlides()
    {
        $availableClasses = ClassInfo::subclassesFor(Slide::class);
        array_shift($availableClasses);

        return array_map(function ($class) {
            /** @var Slide $class */
            return $class::singleton()->getType();
        }, array_combine($availableClasses, $availableClasses));
    }

    public function getAnchor()
    {
        return 'slide-' . $this->ID;
    }
    
    public function forTemplate()
    {
        $ancestry = $this->getClassAncestry();
        return $this->renderWith(array_reverse($ancestry));
    }
}
