import React, { Component } from "react";
import HubletoChart, { HubletoChartType } from "@hubleto/src/core/Components/HubletoChart";

export interface ReportGoalProps {
}

export interface ReportGoalState {
  data: any,
  selectedGraph: HubletoChartType,
}

export default class ReportGoal extends Component<ReportGoalProps,ReportGoalState> {
  constructor(props) {
    super(props);

    this.state = {
      selectedGraph: "goals",
      data: null,
    };
  }

render(): JSX.Element {
    return (
      <>
        <HubletoChart type={this.state.selectedGraph} data={this.state.data} />
      </>
    );
  }
}
