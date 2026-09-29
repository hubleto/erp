import App from '@hubleto/react-ui/core/App'
import TableHrRecruitment from './Components/FC/TableHrRecruitment'

class HrRecruitmentApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrRecruitmentTable', TableHrRecruitment);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrRecruitment', new HrRecruitmentApp());