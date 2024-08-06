<?php

namespace CeremonyCrmApp\Core;

class Router extends \ADIOS\Core\Router {
  public function __construct(\ADIOS\Core\Loader $adios) {
    parent::__construct($adios);

    $regexpProject = 'projects\\/(\d+)\\/([^\\/]+)';

    // URLs without parameters
    // $this->addRoutingGroup(
    //   '',
    //   'CeremonyCrmApp/Controllers/App',
    //   'CeremonyCrmApp/Views/App',
    //   [],
    //   [
    //     'projects' => 'Projects',
    //     'projects/create' => 'Project/CreateOrUpdate',
    //     'funds' => 'Funds',
    //     'plugins-and-extensions' => 'Plugins',
    //     'user-guide' => 'UserGuide',
    //   ]
    // );

/*    // URLs for API
    $this->addRoutingGroup(
      'api',
      'AquilaCostingApp/Controllers/Api',
      '',
      [],
      [
        '/v1/form' => 'Form',
        '/v1/table' => 'Table',
        '/v1/lookup' => 'Lookup',
        '/v1/get-inventory-info' => 'GetInventoryInfo',
        '/v1/decommissioning-plan' => 'DecommissioningPlan',
        '/v1/project-schedule' => 'ProjectSchedule',
        '/v1/site-structure' => 'SiteStructure',
      ]
    );

    // URLs for project
    $this->addRoutingGroup(
      $regexpProject,
      'AquilaCostingApp/Controllers/App/Project',
      'AquilaCostingApp/Views/App/Project',
      [
        'idProject' => '$1',
        'projectName' => '$2',
      ],
      [
        '' => 'Dashboard',
        '/delete' => 'Delete',
        '/modify' => 'CreateOrUpdate',
      ]
    );

    // URLs for costing model
    $this->addRoutingGroup(
      $regexpProject . '\\/costing-models\\/(\d+)\\/([^\\/]+)',
      'AquilaCostingApp/Controllers/App/CostingModel',
      'AquilaCostingApp/Views/App/CostingModel',
      [
        'idProject' => '$1',
        'projectName' => '$2',
        'idCostingModel' => '$3',
        'costingModelName' => '$4'
      ],
      [
        '' => 'Dashboard',
        '/inventory' => 'Inventory',
        '/inventory/waste-tree' => 'Inventory/WasteTree',
        '/inventory/import/database' => [
          'view' => 'Import/Database',
          'controller' => 'Inventory/Import/Database',
        ],
        '/inventory/clear' => 'Inventory/Import/Clear',
        '/inventory/import/log' => [
          'view' => 'Import/Log',
          'controller' => 'Inventory/Import/Log',
        ],
        '/inventory/import' => [
          'view' => 'Import',
          'controller' => 'Inventory/Import',
        ],
        '/inventory/import/excel' => [
          'view' => 'Import/Excel',
          'controller' => 'Inventory/Import/Excel',
        ],
        '/site-structure' => 'SiteStructure',
        '/systems' => 'Systems',
        '/decommissioning-plan' => 'DecommissioningPlan',
        '/waste-management' => 'WasteManagement',
        '/waste-management/nuclide-vectors' => 'WasteManagement/NuclideVectors',
        '/waste-management/nuclide-vectors/editor' => 'WasteManagement/NuclideVectors/Editor',
        '/waste-management/limits' => 'WasteManagement/Limits',
        '/waste-management/limits/editor' => 'WasteManagement/Limits/Editor',
        '/waste-management/sorters' => 'WasteManagement/Sorters',
        '/waste-management/sorters/editor' => 'WasteManagement/Sorters/Editor',
        '/waste-management/technologies' => 'WasteManagement/Technologies',
        '/waste-management/technologies/editor' => 'WasteManagement/Technologies/Editor',
        '/calculation' => 'Calculation',
        '/calculation-log' => 'Calculation/Log',
        '/calculation-parameters' => 'Calculation/Parameters',
        '/calculation-parameters/general-unit-factors' => 'Calculation/Parameters/GeneralUnitFactors',
        '/calculation-parameters/inventory-nuclide-vectors' => 'Calculation/Parameters/InventoryNuclideVectors',
        '/calculation-parameters/decommissioning-categories' => 'Calculation/Parameters/DecommissioningCategories',
        '/results' => 'Results',
        '/project-schedule' => 'ProjectSchedule',
      ],
    );

    // Misc URLs
    $this->addRouting(
      [
        '/^$/' => [
          'controller' => 'AquilaCostingApp/Controllers/App/Dashboard',
          'view' => 'AquilaCostingApp/Views/App/Dashboard',
        ],
        '/^~/' => [
          'controller' => 'ADIOS/Controllers/Login',
          'view' => 'AquilaCostingApp/Views/Login',
        ],
        '/^' . $regexpProject . '\\/costing-model\\/create\\/?$/' => [
          'controller' => 'AquilaCostingApp/Controllers/App/CostingModel/Create',
          'view' => 'AquilaCostingApp/Views/App/CostingModel/Create',
          'params' => [
            'idProject' => '$1',
            'projectName' => '$2',
          ]
        ]
      ],
    ); */
  }

  public function addRoutingGroup(
    string $urlRegexp,
    string $controllerSlug,
    string $viewSlug,
    array $commonParams,
    array $routes
  ) {
    $newRoutes = [];

    foreach ($routes as $url => $item) {
      $regexp = '/^' . $urlRegexp . str_replace('/', '\\/', $url) . '\\/?$/';

      if (is_string($item)) {
        $newRoutes[$regexp] = [
          'controller' => $controllerSlug . '/' . $item ?? '',
          'view' => $viewSlug . '/' . $item ?? '',
          'params' => $commonParams,
        ];
      } else {
        $newRoutes[$regexp] = [
          'controller' => $controllerSlug . '/' . $item['controller'] ?? '',
          'view' => $viewSlug . '/' . $item['view'] ?? '',
          'params' => array_merge($commonParams, $item['params'] ?? []),
        ];
      }
    }

    $this->addRouting($newRoutes);
  }
}
