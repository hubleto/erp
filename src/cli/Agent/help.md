# Help for `php hubleto` command.

`php hubleto` is a powerfull CLI script to automate initialization of Hubleto, apps management or script generation.

| Command                                 | Description                                                              |
| --------------------------------------- | ------------------------------------------------------------------------ |
| help                                    | Prints this help                                                         |
| init [configFile]                       | Init empty Hubleto project. Config file must be in YAML.                 |
|                                         |                                                                          |
| app create <appNamespace>               | Create app in a given namespace.                                         |
| app disable <appClass>                  | Disable app. This will not delete app's data.                            |
| app install <appClass> [forceReinstall] | Install specified app.                                                   |
| app test <appClass> <testName>          | Run one test. ONLY FOR DEVELOPMENT! MAY MODIFY YOUR DATA.                |
| app test <appClass>                     | Run all tests in <appClass>. ONLY FOR DEVELOPMENT! MAY MODIFY YOUR DATA. |
| app reset-all                           | Re-install all apps their 'factory' state.                               |
| app list                                | List all installed apps.                                                 |
|                                         |                                                                          |
| project generate-demo-data              | Generate demo data. ONLY FOR DEVELOPMENT! COMPLETELY RESETS ALL DATA !   |
|                                         |                                                                          |
| release create                          | Creates a release of your project for easy deployment.                   |

Examples:
  php hubleto help
  php hubleto init project-config.yaml
  php hubleto app create \HubletoApp\Custom\MyFirstApp
  php hubleto app install \HubletoApp\Custom\MyFirstApp\Loader force
  php hubleto project generate-demo-data
