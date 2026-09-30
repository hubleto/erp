import App from '@hubleto/react-ui/core/App'
import TableEmployees from './Components/FC/TableEmployees'
import TableEmploymentTypes from './Components/FC/TableEmploymentTypes'
import TableEmploymentStatuses from './Components/FC/TableEmploymentStatuses'
import TableWorkLocations from './Components/FC/TableWorkLocations'

class HrEmployeesApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrEmployeesTable', TableEmployees);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableEmploymentTypes', TableEmploymentTypes);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableEmploymentStatuses', TableEmploymentStatuses);
    globalThis.hubleto.registerReactComponent('HrEmployeesTableWorkLocations', TableWorkLocations);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrEmployees', new HrEmployeesApp());