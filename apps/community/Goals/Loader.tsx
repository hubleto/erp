import HubletoMain from "../../../App";
import ReportGoal from "./Components/ReportGoal";
import TableGoals from "./Components/TableGoals";

export default class Loader {
  uid: string = 'goals';
  constructor(app: HubletoMain) {
    app.registerReactComponent('ReportGoal', ReportGoal);
    app.registerReactComponent('TableGoals', TableGoals);
  }
}
