# Help for `php hubleto` command.

`php hubleto` is a powerfull CLI script to automate initialization of Hubleto, apps management or script generation.

| Command                                  | Description                                                             |
| ---------------------------------------- | ----------------------------------------------------------------------- |
| help                                     | Prints this help                                                        |
| init [configFile]                        | Init empty Hubleto project. Config file must be in YAML.                |
| ----- COMMAND GROUP 'app' -------------- | ----------------------------------------------------------------------- |
| app disable <appClass>                   | Disable app. This will not delete app's data.                           |
| app install <appClass> [forceReinstall]  | Install specified app.                                                  |
| app reset-all                            | Re-install all apps their 'factory' state.                              |
| app show-installed                       | Show all installed apps.                                                |
| ----- COMMAND GROUP 'code' ------------- | ----------------------------------------------------------------------- |
| code generate <template>                 | Generate code by given template.                                        |
| code show-templates                      | Show all available templates for `generate` command.                    |
| ----- COMMAND GROUP 'db' --------------- | ----------------------------------------------------------------------- |
| db generate-demo-data                    | Generate demo data. RESETS ALL DATA, INCLUDING USER ACCOUNTS !          |

Examples:
  php hubleto help
  php hubleto init project-config.yaml
  php hubleto app install \HubletoApp\Community\Customers\Loader
  php hubleto code show-templates
  php hubleto db generate-demo-data
