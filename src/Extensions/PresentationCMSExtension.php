<?php

namespace XD\Narrowcasting\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use XD\Narrowcasting\Models\Presentation;
use XD\Narrowcasting\Slides\Slide;

/**
 * Class PresentationCMSExtension
 * @package XD\Narrowcasting\Extensions
 * @property GridFieldDetailForm_ItemRequest $owner
 */
class PresentationCMSExtension extends DataExtension
{
    public function updateFormActions(FieldList $actions)
    {
        $record = $this->owner->getRecord();

        if(!$record->exists()){
            return;
        }

        if ($record instanceof Presentation || $record instanceof Slide) {
            $label = _t(__CLASS__ . '.Preview', 'Preview');
            $actions->push(new LiteralField(
                'previewLink',
                "<a class='font-icon-eye btn secondary' href='{$record->AbsoluteLink()}' target='_blank'>{$label}</a>"
            ));
        }
    }
}
