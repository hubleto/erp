# Basic commands (php hubleto <command>)

| Command       | Description                                                        |
| ------------- | ------------------------------------------------------------------ |
| app           | See command group 'app'.                                           |
| factory-reset | Re-install all apps their 'factory reset' state.                   |
| help          | Prints this help                                                   |
| init          | Init empty Hubleto project with default packages and configuration |

Examples:
  php hubleto help

# Command groups

## app (php hubleto app <command>)

| Command                             | Description                                             |
| ----------------------------------- | ------------------------------------------------------- |
| disable <appClass>                  | Disable app. This will not delete app's data.           |
| list                                | List all installed apps                                 |
| install <appClass> [forceReinstall] | Install app                                             |
|                                     | <appClass> must be full path to the app's loader class. |
|                                     | [forceReinstall] If = "1", app will be reinstalled.     |

Examples:
  php hubleto app install \HubletoApp\Community\Customers\Loader
