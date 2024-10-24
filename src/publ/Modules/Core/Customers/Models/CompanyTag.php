<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\Tag;

class CompanyTag extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'company_tags';
  public string $eloquentClass = Eloquent\CompanyTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_tag' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Tag',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Company Categories';
    $description['ui']['addButtonText'] = 'Add Company';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}
