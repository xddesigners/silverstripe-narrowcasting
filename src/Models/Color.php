<?php

namespace XD\Narrowcasting\Models;

use Heyday\ColorPalette\Fields\ColorPaletteField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Class Color
 * @package XD\Narrowcasting\Models
 *
 * @property string Title
 * @property string Hex
 * @property string Code
 * @property boolean IsDefault
 */
class Color extends DataObject
{

    private static $table_name = 'Narrowcasting_Color';

    private static $db = array(
        'Title' => 'Varchar',
        'Hex' => 'Varchar',
        'Code' => 'Varchar',
        'IsDefault' => 'Boolean'
    );

    private static $has_one = array(
        'SiteConfig' => SiteConfig::class
    );

    private static $has_many = array();

    private static $belongs_many_many = array();

    private static $searchable_fields = array();

    private static $summary_fields = array(
        'Title' => 'Title',
        'Code' => 'Code',
        'getColorPreviewHTML' => 'Color'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SiteConfigID', 'Code');
        $fields->addFieldToTab("Root.Main", new TextField('Title', 'Title'));
        $fields->addFieldToTab("Root.Main", new TextField('Hex', 'Hex'));
        $fields->addFieldToTab("Root.Main", TextField::create('Code', 'Code')->setReadOnly(true));
        return $fields;
    }

    public function getColorPreviewHTML()
    {
        $hex = '#dedede';
        $label = '-';
        if (!empty($this->Hex)) {
            $label = $hex = $this->getHexCode();
        }

        return DBField::create_field(
            'HTMLVarchar',
            "<span style='background-color:{$hex}; color:#fff; padding: 5px; border: 1px solid #000; font-family: courier;'>{$label}</span>");
    }

    public function getHexCode()
    {
        return '#' . substr($this->Hex, -6, 6);
    }

    public function rgb()
    {
        $hex = str_replace('#', '', substr($this->Hex, -6, 6));
        $length = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));

        return new ArrayData($rgb);
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $filter = new URLSegmentFilter();
        $this->Code = $filter->filter($this->Title);
        // update new css file in assets
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        // update new css file in assets
    }

    public static function getFormField($field, $label = null)
    {
        return ColorPaletteField::create($field, $label, Color::get()->map('ID','Hex'))->setEmptyString(0);
    }

    public static function getDefault()
    {
        return self::get_one(self::class, ['IsDefault' => 1]);
    }
}