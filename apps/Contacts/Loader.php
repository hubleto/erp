<?php

namespace HubletoApp\Community\Contacts;

class Loader extends \Hubleto\Framework\App
{
  // const DEFAULT_INSTALLATION_CONFIG = [
  //   'sidebarOrder' => 0,
  // ];

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^contacts\/?$/' => Controllers\Contacts::class,
      '/^contacts\/add\/?$/' => ['controller' => Controllers\Contacts::class, 'vars' => ['recordId' => -1]],
      '/^contacts(\/(?<recordId>\d+))?\/?$/' => Controllers\Contacts::class,
      '/^contacts\/get-customer-contacts\/?$/' => Controllers\Api\GetCustomerContacts::class,
      '/^contacts\/check-primary-contact\/?$/' => Controllers\Api\CheckPrimaryContact::class,
      '/^settings\/contact-tags\/?$/' => Controllers\Tags::class,
      '/^contacts\/categories\/?$/' => Controllers\Categories::class,
      '/^contacts\/import\/?$/' => Controllers\Import::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, ['title' => $this->translate('Contact Categories'), 'icon' => 'fas fa-phone', 'url' => 'settings/categories']);
    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Contact Tags'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/contact-tags',
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'contacts', $this->translate('Contacts'), 'fas fa-user');
    $appMenu->addItem($this, 'contacts/import', $this->translate('Import contacts'), 'fas fa-file-import');
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mCategory = $this->main->di->create(Models\Category::class);
      $mContact = $this->main->di->create(Models\Contact::class);
      $mValue = $this->main->di->create(Models\Value::class);
      $mTag = $this->main->di->create(Models\Tag::class);
      $mContactTag = $this->main->di->create(Models\ContactTag::class);

      $mCategory->dropTableIfExists()->install();
      $mContact->dropTableIfExists()->install();
      $mValue->dropTableIfExists()->install();
      $mTag->dropTableIfExists()->install();
      $mContactTag->dropTableIfExists()->install();

      $mCategory->record->recordCreate([ 'name' => 'Work' ]);
      $mCategory->record->recordCreate([ 'name' => 'Home' ]);
      $mCategory->record->recordCreate([ 'name' => 'Other' ]);

      $mTag->record->recordCreate([ 'name' => "IT manager", 'color' => '#D33115' ]);
      $mTag->record->recordCreate([ 'name' => "CEO", 'color' => '#4caf50' ]);
      $mTag->record->recordCreate([ 'name' => "Desicion Maker", 'color' => '#fcc203' ]);
      $mTag->record->recordCreate([ 'name' => "Sales", 'color' => '#2196f3' ]);
      $mTag->record->recordCreate([ 'name' => "Support", 'color' => '#03fc8c' ]);
      $mTag->record->recordCreate([ 'name' => "Other", 'color' => '#b3b3b3' ]);
    }
  }

}
