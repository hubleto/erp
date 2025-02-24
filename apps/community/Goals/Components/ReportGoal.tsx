import React, { Component } from "react";
import HubletoChart, {
  HubletoChartType,
} from "@hubleto/src/core/Components/HubletoChart";
import request from "adios/Request";

export interface ReportGoalProps {
  interval: Array<String>;
  user: number;
  frequency: number;
  metric: number;
  value: number;
  values: any;
  idGoal: number;
  idPipeline: number;
}

export interface ReportGoalState {
  data: any;
}

export default class ReportGoal extends Component< ReportGoalProps, ReportGoalState > {
  constructor(props) {
    super(props);

    this.state = {
      data: null
    }
  }

  componentDidMount(): void {
    let transformedValues = [];

    if (this.props.values && this.props.values.length > 0) {
      this.props.values.map((item, index) => {
        transformedValues.push(item.value);
      })
    }

    request.get(
      'goals/report/get-goal-data',
      {
        interval: this.props.interval,
        user: this.props.user,
        frequency: this.props.frequency,
        metric: this.props.metric,
        value: this.props.value,
        values: transformedValues,
        idGoal: this.props.idGoal,
        idPipeline: this.props.idPipeline,
      },
      (data: any) => {
        if (data.status == "success") {
          this.setState({data: data.data});
        }
      }
    );
  }

  render(): JSX.Element {
    return (
      <>
        <HubletoChart type={"goals"} data={this.state.data} />
      </>
    );
  }
}
