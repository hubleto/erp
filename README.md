![](https://img.shields.io/github/v/tag/hubleto/main)
![](https://img.shields.io/github/license/hubleto/main)


# Hubleto
## Business Application Hub

Hubleto is a `PHP-based opensource CRM and ERP development framework` with lots of features and free apps to develop your custom CRM or ERP.

```
+------------------------------------------+
|                                          | Hubleto
|                                          | 
|       ###         ###         ###        | Free community apps (contacts, calendar, leads, deals, orders, ...)
|       ###         ###         ###        | Download & install in just few minutes
|       ### #####   ### #####   ###        | Uses React, TailwindCSS, Adios or Symfony's Twig
|       ##########  ##########  ###        | Built-in User management, App management, Settings management
|       ###    ###  ###     ### ###        | Foundation for MVC, Routing, Translations, Authentication, Permissions
|       ###    ###  ###     ### ###        | CLI automation tools
|       ###    ###  ##### ####  ####       | Fast learning curve, comprehensive dev guide
|       ###    ###  ### #####    ###       |
|                                          |
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

# Start developing your CRM in few minutes

First, download & unzip Hubleto release: https://www.hubleto.com/en/download

Then run following commands in your terminal:

```bash
php hubleto init # init the project
php hubleto app create "HubletoApp\Custom\HelloWorldApp"
php hubleto app install "HubletoApp\Custom\HelloWorldApp"
php hubleto create model "HubletoApp\Custom\HelloWorldApp" "TodoItem"
```

You can use **models and API of free community apps** available in [apps/community](apps/community) folder, e.g.:

  * [Contacts](apps/community/Contacts) or [Customers](apps/community/Customers) as a full-featured addressbook
  * [Settings](apps/community/Settings) for management of your app's settings
  * [Reports](apps/community/Reports) as a centralized report visualizer
  * [Products](apps/community/Products) for your e-commerce project
  * and more...

## Developer's guide

Visit https://developer.hubleto.com with tutorials on how to download & install, create your own app, and more.

<img src="https://developer.hubleto.com/book/content/assets/images/create-simple-addressbook.gif" alt="Create simple addressbook CRM" />

## Contribute ![](https://img.shields.io/badge/contributions-welcome-green)

You can contribute in many areas:

  * report [bugs](https://github.com/hubleto/main/issues) or submit [issues](https://github.com/hubleto/main/issues)
  * improve or create new [community apps](apps/community)
  * review [pull requests](https://github.com/hubleto/main/pulls)
  * start [discussions](https://github.com/hubleto/main/discussions/categories/general)
  * improve [Hubleto Core](src)
  * translate [language packs](apps/community/Customers/Lang)
  * improve [developer's guide](https://developer.hubleto.com)

## Follow us

LinkedIn: https://www.linkedin.com/company/hubleto

Reddit: https://www.reddit.com/r/hubleto
