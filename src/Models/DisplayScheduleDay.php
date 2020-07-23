<?php

namespace XD\Narrowcasting\Models;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TimeField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBDate;
use SilverStripe\ORM\HasManyList;
use XD\Narrowcasting\GridField\GridFieldConfig_DisplayScheduleDay;
use XD\Narrowcasting\GridField\GridFieldConfig_DisplayScheduleTimePeriods;

/**
 * Class DisplayScheduleDay
 * @package XD\Narrowcasting\Models
 *
 * @property string Title
 * @property string Day
 * @property int Sort
 *
 * @method Presentation Presentation()
 * @method HasManyList TimePeriods()
 */
class DisplayScheduleDay extends DataObject
{
    private static $table_name = 'Narrowcasting_DisplayScheduleDay';

    private static $db = [
        'Day' => 'Enum("Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday", "Monday")',
        'Sort' => 'Int'
    ];

    private static $has_one = [
        'Parent' => Display::class,
        'Presentation' => Presentation::class
    ];

    private static $has_many = [
        'TimePeriods' => DisplayScheduleTimePeriod::class
    ];

    private static $default_sort = 'Sort ASC';

    private static $summary_fields = [
        'Title' => 'Day',
        'ScheduledSummary' => 'Schedule',
        'PresentationID' => 'Presentation'
    ];

    public function getCMSFields()
    {
        $self =& $this;
        $fields = parent::getCMSFields();
        $fields->removeByName(['Sort', 'ParentID', 'TimePeriods']);
        $fields->addFieldsToTab('Root.Main', [
            ReadonlyField::create('Day', _t('OpeningHours.DAY', 'Day'), $this->Day),
            DropdownField::create(
                'PresentationID',
                _t(__CLASS__ . '.Presentation', 'Presentation'),
                Presentation::get()->map()
            )->setEmptyString(_t(__CLASS__ . '.UseDefaultPresentation', 'Use default presentation')),
            GridField::create(
                'TimePeriods',
                _t(__CLASS__ . '.TimePeriods', 'Planned presentations'),
                $this->TimePeriods(),
                GridFieldConfig_DisplayScheduleTimePeriods::create([
                    'From' => function($record, $column, $grid) {
                        return TimeField::create('From', _t(__CLASS__ . '.From', 'From'));
                    },
                    'Till' => function($record, $column, $grid) {
                        return TimeField::create('Till', _t(__CLASS__ . '.Till', 'Till'));
                    },
                    'PresentationID' => function($record, $column, $grid) {
                        return DropdownField::create('PresentationID', _t(__CLASS__ . '.Presentation', 'Presentation'), Presentation::get()->map());
                    },
                    'ParentID' => function($record, $column, $grid) use ($self) {
                        return HiddenField::create('ParentID', _t(__CLASS__ . '.From', 'From'), $self->ID);
                    },
                ])
            )
        ]);

        return $fields;
    }

    /**
     * Set the title and sorting value to the day of the week
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $this->Title = $this->Day;
        $this->Sort = $this->sortVal();
    }

    public function getScheduledSummary()
    {
        if (($periods = $this->TimePeriods()) && $periods->exists()) {
            return _t(__CLASS__ . '.NumberOfScheduledPresentations', '{presentations} scheduled presentations', null, [
                'presentations' => $periods->count()
            ]);
        }

        if (($presentation = $this->Presentation()) && $presentation->exists()) {
            return _t(__CLASS__ . '.PresentationScheduled', '"{presentation}" is scheduled', null, [
                'presentation' => $presentation->getTitle()
            ]);
        }

        return _t(__CLASS__ . '.NoScheduledPresentation', 'Running the default presentation');
    }

    /**
     * Return the full localized day
     *
     * @return string
     */
    public function getTitle()
    {
        $dayTime = strtotime($this->Day);
        return DBDate::create()->setValue($dayTime)->Format('cccc');
    }

    /**
     * Return the sorting value by the chosen day pf the week
     *
     * @return false|string
     */
    private function sortVal()
    {
        $day = $this->Day;
        return date('N', strtotime($day));
    }
}