# Basic commands (php hubleto <command>)

| Command | Description                                                        |
| ------- | ------------------------------------------------------------------ |
| help    | Prints this help                                                   |
| init    | init empty Hubleto project with default packages and configuration |
| install | install tables for existing Hubleto project                        |
| app     | See command group 'app'.                                           |

Examples:
  php hubleto help

# Command groups

## app (php hubleto app <command>)

| Command                             | Description                                             |
| ----------------------------------- | ------------------------------------------------------- |
| list                                | List all installed apps                                 |
| install <appClass> [forceReinstall] | Install app                                             |
|                                     | <appClass> must be full path to the app's loader class. |
|                                     | [forceReinstall] If = "1", app will be reinstalled.     |

Examples:
  php hubleto app install \HubletoApp\Community\Customers\Loader
