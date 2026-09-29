import App from '@hubleto/react-ui/core/App'
import TableHrLeave from './Components/FC/TableHrLeave'

class HrLeaveApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrLeaveTable', TableHrLeave);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrLeave', new HrLeaveApp());