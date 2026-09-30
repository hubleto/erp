import App from '@hubleto/react-ui/core/App'
import TableCourses from './Components/FC/TableCourses'
import TableGoals from './Components/FC/TableGoals'
import TableLearnings from './Components/FC/TableLearnings'
import TableReviews from './Components/FC/TableReviews'

class HrPerformanceApp extends App {
  init() {
    super.init();
    globalThis.hubleto.registerReactComponent('HrPerformanceTableCourses', TableCourses);
    globalThis.hubleto.registerReactComponent('HrPerformanceTableGoals', TableGoals);
    globalThis.hubleto.registerReactComponent('HrPerformanceTableLearnings', TableLearnings);
    globalThis.hubleto.registerReactComponent('HrPerformanceTableReviews', TableReviews);
  }
}

globalThis.hubleto.registerApp('Hubleto/App/Community/HrPerformance', new HrPerformanceApp());