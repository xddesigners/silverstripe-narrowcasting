<?php

namespace XD\Narrowcasting\GridField;

use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\ORM\HasManyList;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * Class GridFieldConfig_DisplayScheduleTimePeriods
 * @package XD\Narrowcasting\GridField
 */
class GridFieldConfig_DisplayScheduleTimePeriods extends GridFieldConfig
{
    public function __construct($fields = null)
    {
        parent::__construct();
        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent(new GridFieldTitleHeader());
        $this->addComponent($columns = new GridFieldEditableColumns());
        $this->addComponent(new GridFieldDeleteAction());
        $this->addComponent(new GridField_ActionMenu());
        $this->addComponent(new GridFieldAddNewInlineButton("toolbar-header-right"));

        if ($fields) {
            $columns->setDisplayFields($fields);
        }
    }
}