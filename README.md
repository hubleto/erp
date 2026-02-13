![](https://img.shields.io/github/v/tag/hubleto/erp)
![](https://img.shields.io/github/license/hubleto/erp)

# Hubleto
## Business Application Hub

Hubleto is a `PHP-based opensource CRM and ERP development framework` with lots of features and free apps to develop your custom CRM or ERP.

🌟 Star if you like it!

The whole platform consists of several repositories, check them all to get the full understanding:

  * [hubleto/erp](https://github.com/hubleto/erp) - package combining everything together into a Hubleto ERP solution.
  * [hubleto/apps](https://github.com/hubleto/apps) - set of freely available apps covering features like CRM, retail or project management.
  * [hubleto/erp](https://github.com/hubleto/erp) - repo for installation of production-ready Hubleto ERP with `composer create-project`.
  * [hubleto/framework](https://github.com/hubleto/framework) - PHP-based low level MVC framework.
  * [hubleto/react-ui](https://github.com/hubleto/react-ui) - React-based UI using other libraries like primereact but modified and tailored for Hubleto ERP.
  * [hubleto/terminal](https://github.com/hubleto/terminal) - a simple command-line tool for PHP.

```
+------------------------------------------+
|       ###         ###         ###        | Free community apps (contacts, calendar, leads, deals, orders, ...)
|       ###         ###         ###        | Download & install in just few minutes
|       ### #####   ### #####   ###        | Uses React, TailwindCSS or Symfony's Twig
|       ##########  ##########  ###        | Built-in User management, App management, Settings management
|       ###    ###  ###     ### ###        | Foundation for MVC, Routing, Translations, Authentication, Permissions
|       ###    ###  ###     ### ###        | CLI automation tools
|       ###    ###  ##### ####  ####       | Fast learning curve, comprehensive dev guide
|       ###    ###  ### #####    ###       |
|                                          |
|                    ##################### |
|                  ####################### |
|               ########################## |
|            #########++++++++++++++++++++ |
|          #######++++++++++++++++++++++++ |
|       #######+++++++++++++++++++++++++++ |
|    ######+++++++++++++++++++++++++++++++ |
|  ##+++++++++++++++++++++++++++++++++++++ |
+------------------------------------------+
```

# Getting started

Follow https://github.com/hubleto/erp-project/blob/main/README.md to install your Hubleto.

## Contribute to Hubleto core (setup dev environment)

Contributing to the Hubleto core is the best way how to support us. You can contribute in many areas:

  * report [bugs](https://github.com/hubleto/erp/issues) or submit [issues](https://github.com/hubleto/erp/issues)
  * improve or create new [community apps](apps)
  * review [pull requests](https://github.com/hubleto/erp/pulls)
  * start [discussions](https://github.com/hubleto/erp/discussions/categories/general)
  * improve [Hubleto Core](src)
  * translate [language packs](apps/Customers/Lang)
  * improve [developer's guide](https://developer.hubleto.com)

To start contribution, follow the steps described below.

  1. **Fork Hubleto repositories**

      Fork following repositories into one folder, e.g. /var/www/hubleto.

        * https://github.com/hubleto/framework
        * https://github.com/hubleto/erp
        * https://github.com/hubleto/react-ui
        * https://github.com/hubleto/assets

  2. **Re-create your Hubleto folder**

      ```
      cd YOUR_PROJECT_FOLDER
      composer create-project hubleto/erp-project . dev-main
      ./bin/use-local-repositories /var/www/hubleto
      npm run build
      php hubleto init
      ```

## Follow us

LinkedIn: https://www.linkedin.com/company/hubleto

Reddit: https://www.reddit.com/r/hubleto
