# Hubleto CHANGELOG

## Release v0.8 (not released yet)

  * new `<HubletoChart>` React component
  * new `php hubleto app create`, `php hubleto debug router` and `php hubleto create model` commands
  * improved translation and language support
  * new community app Goals
  * cleaned-up code base

## Release v0.7

  * improved unit testing
  * deals and leads are now linked to an online shared folder (document storage)
  * started to use `value objects` (see why: https://stevegrunwell.com/blog/php-value-objects)
  * Record management extracted from the Model class
  * `columns()` renamed to `describeColumns()`
  * more flexible CSS skin (thanks to TailwindCSS v4)
  * color scales in tables
  * more consisent form design thanks to a `HubletoForm` react component
  * sidebar is now generated using information from app manifests
  * first prototyping of external apps

## Release v0.6

  * fixed CORS for assets
  * compatibility fixes for Tailwind CSS 4 and other npm packages
  * improved installer and CLI commands init and generate-demo-data
  * enhanced type safety of the core (use of methods configAsString(), urlParamAsInteger(), routeVarAsBool() and so forth)
  * static code analysis checks (thanks to the PHPStan)
  * new features in community apps
  * improved routing featurs (using named groups in routes), see [Dynamic routes with variables](https://developer.hubleto.com/tutorial/advanced/dynamic-routes)
  * new `\HubletoMain\Core\CalendarManager` class and `$this->main->calendarManager` object

New ASCII art logo :-)

```
       ###         
      ###        ##
     #####      ###
    ###  ####  ### 
   ###      #####  
   ##        ###   
            ###    
```

## Release v0.5

  * improved CLI agent
  * improved install script
  * new app manager
  * platform config made available via Settings app
  * UI improvements
  * many bugfixes
  * new class \HubletoMain\Core\ModelEloquent
  * app manifests
  * platform config available in settings app

## Release v0.4

First version in the changelog