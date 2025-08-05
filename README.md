![](https://img.shields.io/github/v/tag/hubleto/main)
![](https://img.shields.io/github/license/hubleto/main)


# Hubleto
## Business Application Hub

Hubleto is a `PHP-based opensource CRM and ERP development framework` with lots of features and free apps to develop your custom CRM or ERP.

The whole platform consists of several repositories, check them all to get the full understanding:

  * [hubleto/main](https://github.com/hubleto/main) - package combining everything together into a Hubleto ERP solution.
  * [hubleto/apps](https://github.com/hubleto/apps) - set of freely available apps covering features like CRM, supply-chain or project management.
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

# Start using Hubleto in few minutes

You have two options to install Hubleto: from a `.zip` package or using `composer`.

## Zip package

Download & unzip Hubleto release: https://www.hubleto.com/en/download

## Composer

Run `composer create-project hubleto/project` in any folder. More information here: https://github.com/hubleto/project

# Develop your apps

To develop your apps run following commands in your terminal:

```bash
php hubleto init # init the project
php hubleto app create "HubletoApp\Custom\HelloWorldApp"
php hubleto app install "HubletoApp\Custom\HelloWorldApp"
php hubleto create model "HubletoApp\Custom\HelloWorldApp" "TodoItem"
```

More details are in developer's guide at https://developer.hubleto.com.

<img src="https://developer.hubleto.com/book/content/assets/images/create-simple-addressbook.gif" alt="Create simple addressbook CRM" />

## Contribute ![](https://img.shields.io/badge/contributions-welcome-green)

You can contribute in many areas:

  * report [bugs](https://github.com/hubleto/main/issues) or submit [issues](https://github.com/hubleto/main/issues)
  * improve or create new [community apps](apps)
  * review [pull requests](https://github.com/hubleto/main/pulls)
  * start [discussions](https://github.com/hubleto/main/discussions/categories/general)
  * improve [Hubleto Core](src)
  * translate [language packs](apps/Customers/Lang)
  * improve [developer's guide](https://developer.hubleto.com)

## Follow us

LinkedIn: https://www.linkedin.com/company/hubleto

Reddit: https://www.reddit.com/r/hubleto
