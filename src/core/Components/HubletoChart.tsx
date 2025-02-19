import React, { Component } from "react";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";

import { Chart as ChartJS, ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale } from "chart.js";
import { Bar, Doughnut, Line } from "react-chartjs-2";

ChartJS.register(ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale);

export type HubletoChartType = 'bar' | 'doughnut';

export interface HubletoChartProps {
  type: HubletoChartType,
  data: any,
}

export interface HubletoChartState {
  type: HubletoChartType,
  data: any,
}

export default class HubletoChart<P, S> extends Component<HubletoChartProps,HubletoChartState> {
  props: HubletoChartProps;
  state: HubletoChartState;

  constructor(props: HubletoChartProps) {
    super(props);

    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: HubletoChartProps) {
    return {
      type: props.type,
      data: props.data,
    }
  }

  render(): JSX.Element {
    switch (this.state.type) {
      case "bar":
        return <Bar
          options={{
            scales: {
              y: {
                beginAtZero: true,
              },
            },
          }}
          data={{
            labels: this.state.data != null ? [...this.state.data.labels] : [],
            datasets: [
              {
                data: this.state.data != null ? [...this.state.data.values] : [],
                backgroundColor: this.state.data != null ? [...this.state.data.colors] : [],
              }
            ]
          }}

        />
      case "doughnut":
        return <div className="w-[35vh]">
          <Doughnut
            options={{
              plugins: {
                legend: {
                  position: "left",
                }
              }
            }}
            data={ {
              labels: this.state.data ? [...this.state.data.labels] : [],
              datasets: [
                {
                  data: this.state.data ? [...this.state.data.values] : [],
                  backgroundColor: this.state.data != null ? [...this.state.data.colors] : [],
                }
              ]
            }}
          />
        </div>
      default:
        return <></>;
    }
  }

}
