# Help for `php hubleto` command.

`php hubleto` is a powerfull CLI script to automate initialization of Hubleto, apps management or script generation.

| Command                                 | Description                                                            |
| --------------------------------------- | ---------------------------------------------------------------------- |
| help                                    | Prints this help                                                       |
| init [configFile]                       | Init empty Hubleto project. Config file must be in YAML.               |
|                                         |                                                                        |
| app disable <appClass>                  | Disable app. This will not delete app's data.                          |
| app install <appClass> [forceReinstall] | Install specified app.                                                 |
| app test <appClass> <testName>          | Test specified app. ONLY FOR DEVELOPMENT! MAY MODIFY YOUR DATA.        |
| app reset-all                           | Re-install all apps their 'factory' state.                             |
| app list-installed                      | List all installed apps.                                               |
|                                         |                                                                        |
| code generate <template>                | Generate code by given template.                                       |
| code list-templates                     | List all available templates for `generate` command.                   |
|                                         |                                                                        |
| db generate-demo-data                   | Generate demo data. ONLY FOR DEVELOPMENT! COMPLETELY RESETS ALL DATA ! |

Examples:
  php hubleto help
  php hubleto init project-config.yaml
  php hubleto app install \HubletoApp\Community\Customers\Loader
  php hubleto code show-templates
  php hubleto db generate-demo-data
