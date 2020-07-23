<?php

namespace XD\Narrowcasting\Extensions;

use SilverStripe\Admin\CMSMenu;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Admin\LeftAndMainExtension;
use SilverStripe\View\Requirements;

/**
 * Workaround to remove CMS Help Button
 *
 * @property LeftAndMain owner
 */
class NarrowcastingLeftAndMainExtension extends LeftAndMainExtension
{
    public function init()
    {
        parent::init();
        Requirements::css('xddesigners/silverstripe-narrowcasting:client/dist/styles/narrowcastingcms.css');
    }
}
