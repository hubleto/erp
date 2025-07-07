# Help for `php hubleto` command.

`php hubleto` is a powerfull CLI script to automate initialization of Hubleto, apps management or script generation.

| Command                                  | Description                                                                  |
| ---------------------------------------- | ---------------------------------------------------------------------------- |
| help                                     | Prints this help                                                             |
| init                                     | Shortcut to `project init` command.                                          |
|                                          |                                                                              |
| project init                             | Init empty Hubleto project. Config file must be in YAML.                     |
|                                          |                                                                              |
| app create <appNamespace>                | Create app in a given namespace.                                             |
| app disable <appNamespace>               | Disable app. This will not delete app's data.                                |
| app install <appNamespace> [force]       | Install specified app. If force set to true, app will be reinstalled.        |
| app test <appNamespace> <testName>       | Run one test. ONLY FOR DEVELOPMENT! MAY MODIFY YOUR DATA.                    |
| app test <appNamespace>                  | Run all tests in <appNamespace>. ONLY FOR DEVELOPMENT! MAY MODIFY YOUR DATA. |
| app reset-all                            | Re-install all apps their 'factory' state.                                   |
| app list                                 | List all installed apps.                                                     |
|                                          |                                                                              |
| create app <appNamespace>                | Synonym for `app create` command.                                            |
| create model <appNamespace> <model>      | Creates an empty model into a specified app.                                 |
| create controller <appNamespace> <model> | Creates an empty controller into a specified app.                            |
| create view <appNamespace> <view>        | Creates an empty view into a specified app.                                  |
| create mvc <appNamespace> <model>        | Creates a default MVC for a specified model.                                 |
| create api <appNamespace> <endpoint>     | Creates a sample REST API controller.                                        |
|                                          |                                                                              |
| debug router [routeToDebug]              | List all routes or print route information.                                  |

Examples:
  php hubleto help
  php hubleto project init
  php hubleto app create \HubletoApp\Custom\MyFirstApp
  php hubleto app install \HubletoApp\Custom\MyFirstApp\Loader force
  php hubleto project generate-demo-data
