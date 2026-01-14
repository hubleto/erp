import React, { Component } from "react";
import Chart, { ChartType } from "@hubleto/src/core/Components/Chart";

export interface ReportGoalProps {
}

export interface ReportGoalState {
  data: any,
  selectedGraph: ChartType,
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
        <Chart type={this.state.selectedGraph} data={this.state.data} />
      </>
    );
  }
}
