<?php

namespace XD\Narrowcasting\Models;

use DateInterval;
use DateTime;
use Exception;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationResult;
use Spatie\Period\Boundaries;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;

/**
 * Class DisplayScheduleTimePeriod
 * @package XD\Narrowcasting\Models
 *
 * @property string From
 * @property string Till
 *
 * @method DisplayScheduleDay Parent()
 * @method Presentation Presentation()
 */
class DisplayScheduleTimePeriod extends DataObject
{
    private static $table_name = 'Narrowcasting_DisplayScheduleTimePeriod';

    private static $db = [
        'From' => 'Time',
        'Till' => 'Time',
    ];

    private static $default_sort = 'From ASC';

    private static $has_one = [
        'Parent' => DisplayScheduleDay::class,
        'Presentation' => Presentation::class
    ];

    private static $summary_fields = [
        'PresentationID' => 'Presentation',
        'From' => 'From',
        'Till' => 'Till',
    ];

    /**
     * Validates that the time period
     * - has a presentation
     * - has a correct time setting From < Till
     * - has no time conflicts
     *
     * @return ValidationResult
     * @throws Exception
     */
    public function validate()
    {
        $validation = parent::validate();
        if ($validation->isValid() && !$this->Presentation()->exists()) {
            $validation->addError(_t(__CLASS__ . '.ErrorNoPresentation', 'No presentation is set'));
        }

        $from = new DateTime($this->From);
        // Add a second so the overlaps dont trigger when finish and start are both e.g. 17:00
        $from->add(DateInterval::createFromDateString('1 seconds'));
        $till = new DateTime($this->Till);
        if ($validation->isValid() && ($from > $till)) {
            $validation->addError(_t(__CLASS__ . '.ErrorFromBeforeTill', 'The start time needs to be before the end time'));
        }

        if ($validation->isValid() && $this->ParentID) {
            $periods = $this->Parent()->TimePeriods();
            $current = Period::make($from, $till, Precision::SECOND);

            if ($this->exists()) {
                $periods = $periods->exclude('ID', $this->ID);
            }

            foreach ($periods as $period) {
                $from = new DateTime($period->From);
                $from->add(DateInterval::createFromDateString('1 seconds'));
                $check = Period::make($from, new DateTime($period->Till), Precision::SECOND);
                $overlap = $current->overlapsWith($check);
                if ($overlap) {
                    $validation->addError(_t(__CLASS__ . '.ErrorOverlappingTimePeriods', 'The schedule should not overlap'));
                    break;
                }
            }
        }

        return $validation;
    }
}