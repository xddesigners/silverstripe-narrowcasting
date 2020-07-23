# SilverStripe Narrowcasting

Backend for narrowcasting applications running on Reveal JS.

## how to install
```bash
composer require xddesigners/silverstripe-narrowcasting
```

## Customization
Most of the Reveal JS settings are exposed trough the CMS and can be configured in the SiteConfig. These global settings are inherited by each presentation where you can overwrite these settings as desired. 

This module adds the Reveal JS dependency. 
The added script also checks if the presentation was altered each cycle by fetching the last edit datetime and presentation id. If the presentation was changed it forces a reload. 

If you want to hook onto the Reveal JS instance you can access this trough `window.Reveal`.
```js
const Reveal = window.Reveal;
Reveal.addEventListener('slidechanged', function(event) {
  // do things on slidechanged
});
```

If you want to include your own instance of Reveal JS you can block the requirements trough the config:
```yaml
XD\Narrowcasting\Controller\DisplayController:
  include_requirements: false
```

You can inject your own js/css by extending the `XD\Narrowcasting\Controller\DisplayController` class:
```php
class DisplayControllerExtension extends Extension
{
    public function onAfterInit()
    {
        Requirements::javascript(project() . '/client/dist/js/app.js');
        Requirements::css(project() . '/client/dist/styles/app.css');
    }
}
```
