import App from '@hubleto/react-ui/core/App'
import TableHrPerformance from './Components/FC/TableHrPerformance'

class HrPerformanceApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrPerformanceTable', TableHrPerformance);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrPerformance', new HrPerformanceApp());