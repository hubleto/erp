# Help for `php hubleto` command.

`php hubleto` is a powerfull CLI script to automate initialization of Hubleto, apps management or script generation.

| Command                                  | Description                                                             |
| ---------------------------------------- | ----------------------------------------------------------------------- |
| help                                     | Prints this help                                                        |
| init                                     | Init empty Hubleto project with default aps and configuration           |
| ----- COMMAND GROUP 'app' -------------- | ----------------------------------------------------------------------- |
| app disable <appClass>                   | Disable app. This will not delete app's data.                           |
| app show                                 | Show all installed apps.                                                |
| app install <appClass> [forceReinstall]  | Install specified app.                                                  |
| app  reset-all                           | Re-install all apps their 'factory' state.                              |
| ----- COMMAND GROUP 'code' ------------- | ----------------------------------------------------------------------- |
| code show-templates                      | Show all available templates for `generate` command.                    |
| code generate <template>                 | Generate code by given template.                                        |
| ----- COMMAND GROUP 'db' --------------- | ----------------------------------------------------------------------- |
| db generate-demo-data                    | Generate demo data. RESETS ALL DATA, INCLUDING USER ACCOUNTS !          |

Examples:
  php hubleto help
  php hubleto app install \HubletoApp\Community\Customers\Loader
  php hubleto code show-templates
  php hubleto db generate-demo-data
