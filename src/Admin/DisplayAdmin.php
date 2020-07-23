<?php

namespace XD\Narrowcasting\Admin;

use SilverStripe\Admin\ModelAdmin;
use XD\Narrowcasting\Models\Display;

class DisplayAdmin extends ModelAdmin
{
    private static $menu_title = 'Displays';

    private static $url_segment = 'displays';

    private static $menu_icon_class = 'font-icon-monitor';

    private static $menu_priority = -1;

    private static $managed_models = [
        Display::class
    ];
}