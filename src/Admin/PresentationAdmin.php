<?php

namespace XD\Narrowcasting\Admin;

use SilverStripe\Admin\ModelAdmin;
use XD\Narrowcasting\Models\Presentation;

class PresentationAdmin extends ModelAdmin
{
    private static $menu_title = 'Presentaties';

    private static $url_segment = 'presentations';

    private static $menu_icon_class = 'font-icon-fast-forward';

    private static $managed_models = [
        Presentation::class
    ];
}