import React, { Component } from "react";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";
import { Bar, Doughnut, Line } from "react-chartjs-2";
import { Chart as ChartJS, ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale, PointElement, LineElement, LineController } from "chart.js";

ChartJS.register(ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale, PointElement, LineElement, LineController);

export type HubletoChartType = 'bar' | 'doughnut' | 'goals';

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
        return (
          <Bar
            width={0}
            height={0}
            options={{
              scales: {
                x: {
                  ticks: {
                    display: false,
                  },
                },
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
                },
              ],
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
        return (<div className="w-[35vh]">
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
        );
      case "doughnut":
        return (
          <div className="w-[35vh]">
            <Doughnut
              options={{
                responsive: true,
                plugins: {
                  legend: {
                    position: "left",
                  },
                },
              }}
              data={{
                labels: this.state.data ? [...this.state.data.labels] : [],
                datasets: [
                  {
                    data: this.state.data ? [...this.state.data.values] : [],
                    backgroundColor: this.state.data != null ? [...this.state.data.colors] : [],
                  },
                ],
              }}
            />
          </div>
        );
      case "goals":
        return <Bar
          data={{
            datasets: [
              {
                type: 'line',
                label: 'Goals',
                backgroundColor: "#ffb12b",
                borderColor: "#a87316",
                data: this.props.data ? [...this.props.data.goals] : [],
              },
              {
                type: 'bar',
                label: 'Won Deals',
                backgroundColor: "#66c24f",
                data: this.props.data ? [...this.props.data.won] : [],
                stack: "stack"
              },
              {
                type: 'bar',
                label: 'Pending Deals',
                backgroundColor: "#cfcecc",
                data: this.props.data ? [...this.props.data.pending] : [],
                stack: "stack"
              },

            ],
            labels: this.props.data ? [...this.props.data.labels] : [],
          }}
      />;
      default:
        return <></>;
    }
  }
}
