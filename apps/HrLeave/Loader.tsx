import App from '@hubleto/react-ui/core/App'
import TableLeaves from './Components/FC/TableLeaves'
import TableLeaveRequests from './Components/FC/TableLeaveRequests'
import TableLeaveTypes from './Components/FC/TableLeaveTypes'

class HrLeaveApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrLeaveTableLeaves', TableLeaves);
    globalThis.hubleto.registerReactComponent('HrLeaveTableLeaveRequests', TableLeaveRequests);
    globalThis.hubleto.registerReactComponent('HrLeaveTableLeaveTypes', TableLeaveTypes);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrLeave', new HrLeaveApp());