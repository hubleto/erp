<?php

namespace HubletoApp\Community\Documents;

class Loader extends \Hubleto\Framework\App
{
  
  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->getRouter()->httpGet([
      '/^documents\/?$/' => Controllers\Browse::class,
      // '/^documents\/api\/save-junction\/?$/' => Controllers\Api\SaveJunction::class,
      '/^documents\/browse\/?$/' => Controllers\Browse::class,
      '/^documents\/list\/?$/' => Controllers\Documents::class,
      '/^documents\/(?<recordId>\d+)\/?$/' => Controllers\Documents::class,
      '/^documents\/templates\/?$/' => Controllers\Templates::class,
      '/^documents\/api\/get-folder-content\/?$/' => Controllers\Api\GetFolderContent::class,
    ]);

  }

  public function getRootFolderId(): int|null
  {
    $mFolder = $this->getModel(Models\Folder::class);
    $rootFolder = $mFolder->record->where('uid', '_ROOT_')->first()->toArray();
    if (!isset($rootFolder['id'])) {
      return null;
    } else {
      return (int) $rootFolder['id'];
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mFolder = $this->getModel(Models\Folder::class);
      $mFolder->dropTableIfExists()->install();

      $mFolder->record->recordCreate([
        'id_parent_folder' => null,
        'uid' => '_ROOT_',
        'name' => '_ROOT_',
      ]);

      $this->getModel(Models\Document::class)->dropTableIfExists()->install();
      $this->getModel(Models\Template::class)->dropTableIfExists()->install();
    }

  }

  public function generateDemoData(): void
  {
    $mFolder = $this->getModel(Models\Folder::class);
    $mDocument = $this->getModel(Models\Document::class);
    $mTemplate = $this->getModel(Models\Template::class);

    $mDocument->record->recordCreate([
      'id_folder' => $this->getRootFolderId(),
      'name' => 'bid_template.docx',
      'hyperlink' => 'https://www.google.com',
    ]);

    $idFolderMM = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => 'Marketing materials' ])['id'];
    $idFolderMM1 = $mFolder->record->recordCreate(['id_parent_folder' => $idFolderMM, 'name' => 'LinkedIn'])['id'];
    $idFolderMM2 = $mFolder->record->recordCreate(['id_parent_folder' => $idFolderMM, 'name' => 'GoogleAds'])['id'];

    $idFolderCU = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => 'Customer profiles' ])['id'];

    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM, 'name' => 'logo.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_1.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_2.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $mDocument->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $idTemplate = $mTemplate->record->recordCreate([
      'name' => 'PDF template for quotation',
      'content' => '
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
  * { font-family: "Helvetica"; font-size: 12px; }
</style>

<div>
  <div class="dtop">
    <div style="font-size:24pt"><b>Quotation</b></div>
    Deal: {{ identifier }} {{ title }}<br/>
    {% if version %} Version {{ version }}<br/> {% endif %}
    Generated on: {{ now }}<br/>
    Customer: {{ CUSTOMER.name }}<br/>
    Contact person: {{ CONTACT.first_name }} {{ CONTACT.last_name }}<br/>
  </div>
  <br/>
  <br/>

  <table style="width:100%">
    <tr>
      <td style="width:60%"><b>Product</b></td>
      <td style="width:10%"><b>Unit price</b></td>
      <td style="width:10%"><b>Amount</b></td>
      <td style="width:10%"><b>Discount</b></td>
      <td style="width:10%"><b>Subtotal</b></td>
    </tr>
    {% for product in PRODUCTS %}
      <tr>
        <td style="width:60%">
          {{ product.PRODUCT.name }}
          {% if product.description %}
            <div style="color:#666666">
              {{ product.description }}
            </div>
          {% endif %}
        </td>
        <td style="width:10%">{{ product.unit_price|number_format(2, ",", " ") }} €</td>
        <td style="width:10%">{{ product.amount|number_format(2, ",", " ") }} </td>
        <td style="width:10%">{{ product.discount|number_format(0, ",", " ") }} %</td>
        <td style="width:10%"><b>{{ product.price_excl_vat }}|number_format(2, ",", " ") €</b></td>
      </tr>
    {% endfor %}
  </table>

  <div style="font-size:24px"><b>Total: {{ price_excl_vat }}|number_format(2, ",", " ") €</b></div>

  Note: All prices are excluding value added tax.
</div>

<br/><br/><br/>
<div>
  <b><span style="color:#05b9e9">wai</span><span style="color:#58585a">blue</span></b><br/>
  <span style="color:#58585a">software_engineering_experts</span><br/>
</div>
      '
    ])['id'];


  }

}
