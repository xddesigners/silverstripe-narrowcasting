<?php

namespace XD\Narrowcasting\Controller;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse_Exception;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\ArrayData;
use SilverStripe\View\Requirements;
use XD\Narrowcasting\Models\Display;
use XD\Narrowcasting\Models\Presentation;

class DisplayController extends ContentController
{
    private static $allowed_actions = [
        'view',
        'presentation',
        'kioskconfig'
    ];

    private static $include_requirements = true;

    public function init()
    {
        parent::init();
        if (self::config()->get('include_requirements')) {
            Requirements::javascript('xddesigners/silverstripe-narrowcasting:client/dist/js/narrowcasting.js');
            Requirements::css('xddesigners/silverstripe-narrowcasting:client/dist/styles/narrowcasting.css');
        }
    }

    /**
     * Render the display's active presentation
     *
     * @param HTTPRequest $request
     * @return DBHTMLText|void
     * @throws HTTPResponse_Exception
     */
    public function view(HTTPRequest $request)
    {
        $id = $request->param('ID');
        if ($display = Display::get_by_id($id)) {
            return $this->renderPresentation($display);
        }

        return $this->httpError(404);
    }

    /**
     * Render the direct presentation
     *
     * @param HTTPRequest $request
     * @return false|DBHTMLText|string|void
     * @throws HTTPResponse_Exception
     */
    public function presentation(HTTPRequest $request)
    {
        $id = $request->param('ID');
        $slideId = $request->param('SlideID');
        if ($presentation = Presentation::get_by_id($id)) {
            return $this->renderPresentation($presentation, $slideId);
        }

        return $this->httpError(404);
    }

    /**
     * Render the display's presentation or presentation itself
     *
     * @param Display|Presentation|null $displayOrPresentation
     * @param int|null $slideId
     * @return false|DBHTMLText|string|void
     * @throws HTTPResponse_Exception
     */
    public function renderPresentation($displayOrPresentation, $slideId = null)
    {
        $presentation = null;

        // Continue with the presentation inst
        if ($displayOrPresentation instanceof Presentation) {
            $presentation = $displayOrPresentation;
        }

        // Retrieve the active presentation
        if ($displayOrPresentation instanceof Display) {
            $presentation = $displayOrPresentation->getActivePresentation();
        }

        if ($presentation && $presentation->exists()) {
            // determine if reload is needed
            if (Director::is_ajax()) {
                return json_encode([
                    'lastEdited' => $presentation->LastEdited,
                    'presentationID' => $presentation->ID
                ]);
            }

            $config = $presentation->getPresentationConfig();
            Requirements::insertHeadTags(sprintf(
                '<script>window.revealConfig = %s,window.presentationID=%s,window.lastEdited="%s"</script>',
                json_encode($config),
                $presentation->ID,
                $presentation->LastEdited
            ));

            if ($slideId && ($slide = $presentation->Slides()->find('ID', $slideId)) && $slide->exists()) {
                return $this->renderWith(['XD\\Narrowcasting\\Presentation_slide', 'XD\\Narrowcasting\\Presentation', 'Page'], $slide);
            } else {
                return $this->renderWith(['XD\\Narrowcasting\\Presentation', 'Page'], $presentation);
            }
        }

        return $this->httpError(404);
    }

    /**
     * Serve the display kiosk config as plain txt file
     * For config options
     * @see https://porteus-kiosk.org/parameters.html
     *
     * @param HTTPRequest $request
     * @return DBHTMLText|void
     * @throws HTTPResponse_Exception
     */
    public function kioskconfig(HTTPRequest $request)
    {
        $id = $request->param('ID');
        if ($display = Display::get_by_id($id)) {
            $this->getResponse()->addHeader('Content-Type', 'text/plain; charset="utf-8"');
            $this->getResponse()->addHeader('X-Robots-Tag', 'noindex');

            return $this->customise(new ArrayData([
                'Settings' => $display->getKioskConfig()
            ]))->renderWith('XD\\Narrowcasting\\DisplayKioskConfig');
        }

        return $this->httpError(404);
    }
}
