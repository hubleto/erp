import React, { Component } from "react";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";
import { Bar, Doughnut, Pie, Line, Scatter } from "react-chartjs-2";
import 'chartjs-adapter-date-fns';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, Filler, BarController, BarElement, CategoryScale, LinearScale, PointElement, LineElement, LineController, TimeScale } from "chart.js";
import request from "adios/Request";
import { ProgressBar } from 'primereact/progressbar';

ChartJS.register(ArcElement, Tooltip, Legend, Filler, BarController, BarElement, CategoryScale, LinearScale, TimeScale, PointElement, LineElement, LineController);

export type HubletoChartType = 'bar' | 'doughnut' | 'pie' | 'goals' | 'scatter' | 'line';

export interface HubletoChartProps {
  type: HubletoChartType,
  data?: any,
  legend?: any,
  options?: any,
  async?: boolean,
  endpoint?: string,
  endpointParams?: any,
}

export interface HubletoChartState {
  data?: any,
  legend?: any,
  options?: any,
  initialized: boolean,
}

export default class HubletoChart<P, S> extends Component<HubletoChartProps,HubletoChartState> {
  props: HubletoChartProps;
  state: HubletoChartState;

  constructor(props: HubletoChartProps) {
    super(props);

    this.state = {
      data: this.props.data,
      legend: this.props.legend,
      options: this.props.options,
      initialized: false,
    }
  }

  componentDidMount() {
    if (this.props.async) this.loadChart();
  }

  loadChart() {
    request.get(
      this.props.endpoint,
      this.props.endpointParams ?? {},
      (chart: any) => {
        try {
          this.setState({
            data: chart.data,
            legend: chart.legend,
            options: chart.options,
          });
        } catch (err) {
          console.error(err);
        }
      }
    );
  }

  render(): JSX.Element {
    if (!this.state.data) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    const data = this.state.data;

    let labels: any = [];
    let dataset: any = {};

    labels = this.state.data.labels ?? [];

    dataset.data = this.state.data.values ?? [];
    if (data.colors) dataset.backgroundColor = data.colors;

    switch (this.props.type) {
      case "scatter":
        return <Scatter options={this.state.options} data={this.state.data} />;
      break;
      case "line":
        return <Line options={this.state.options} data={this.state.data} />;
      break;
      case "bar":
        return (
          <Bar
            width={0}
            height={0}
            options={{
              scales: {
                x: { ticks: { display: false } },
                y: { beginAtZero: true },
              },
            }}
            data={{
              labels: labels,
              datasets: [ dataset ],
            }}
          />
        );
      case "doughnut":
        return (
          <div className="h-full m-auto relative">
            <Doughnut
              options={{
                responsive: true,
                plugins: {
                  legend: this.props.legend ? this.props.legend : {
                    display: false,
                  },
                },
              }}
              data={{
                labels: labels,
                datasets: [ dataset ],
              }}
            />
          </div>
        );
      case "pie":
        return (
          <div className="h-full w-full m-auto relative">
            <Pie
              options={{
                responsive: true,
                aspectRatio: 2,
                plugins: {
                  legend: this.props.legend ? this.props.legend : {
                    display: false,
                  },
                },
              }}
              data={{
                labels: labels,
                datasets: [ dataset ],
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
