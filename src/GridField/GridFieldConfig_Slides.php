<?php

namespace XD\Narrowcasting\GridField;

use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\ORM\HasManyList;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use XD\Narrowcasting\Slides\Slide;

/**
 * Class GridFieldConfig_Slides
 * @package XD\Narrowcasting\GridField
 */
class GridFieldConfig_Slides extends GridFieldConfig
{
    public function __construct($classes = [])
    {
        parent::__construct();
        $this->addComponent($multiClass = new GridFieldAddNewMultiClass());
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new GridFieldDetailForm());
        $this->addComponent(new GridFieldOrderableRows());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldDeleteAction());
        $this->addComponent(new GridField_ActionMenu());

        if (empty($classes)) {
            $classes = Slide::getAvailableSlides();
        }

        $multiClass->setClasses($classes);
    }
}
