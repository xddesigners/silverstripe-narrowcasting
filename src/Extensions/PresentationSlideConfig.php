<?php

namespace XD\Narrowcasting\Extensions;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;

/**
 * Class PresentationSlideConfig
 * @mixin DataObject
 * @property PresentationSlideConfig $owner
 * @property int AutoSlide
 * @property string Transition
 * @property string TransitionSpeed
 * @property string BackgroundColor
 * @property string BackgroundVideo
 * @property boolean BackgroundVideoLoop
 * @property boolean BackgroundVideoMuted
 * @property string BackgroundSize
 * @property int BackgroundOpacity
 * @property string BackgroundIframe
 * @property boolean BackgroundInteractive
 */
class PresentationSlideConfig extends DataExtension
{

}
