import React, { Component } from "react";
import Lookup from "adios/Inputs/Lookup";
import FormInput from "adios/FormInput";
import request from "adios/Request";

import { Chart as ChartJS, ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale } from "chart.js";
import { Bar, Doughnut, Line } from "react-chartjs-2";

ChartJS.register(ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale);

export interface FormReportProps {
  config: any,
  model: string,
  readonly?: boolean,
  name?: string,
}

export interface FormReportState {
  data: any,
  selectedGraph: string,
}

export default class FormReport extends Component<FormReportProps,FormReportState> {
  constructor(props) {
    super(props);

    this.state = {
      selectedGraph: "doughnut",
      data: null,
    };
  }

  componentDidMount(): void {
    this.requestData();
  }

  renderOptions(fieldType: string): Object {
    switch (fieldType) {
      case "int":
      case "float":
        return {
          1: "Is",
          2: "Is Not",
          3: "More Than",
          4: "Less Than",
        };
      case "varchar":
      case "text":
        return {
          1: "Is",
          2: "Is Not",
          5: "Contains",
        };
      case "date":
      case "datetime":
      case "time":
        return {
          1: "On",
          2: "Not On",
          6: "Between",
        };
      case "lookup":
        return {
          1: "Is",
          2: "Is Not",
        };
      case "boolean":
        return {
          1: "Is",
        };
    }
  }

  renderInputElement(field: any, value: any): JSX.Element {

    switch (field.type) {
      case "int":
      case "float":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <input
            readOnly={this.props.readonly ?? false}
            value={value ?? null}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
            type="number"
          />
        </div>
      case "varchar":
      case "text":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <input
            readOnly={this.props.readonly ?? false}
            value={value ?? null}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
            type="text"
          />
        </div>
      case "date":
      case "datetime":
      case "time":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <input
            readOnly={this.props.readonly ?? false}
            value={value ?? null}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
            type={field.type}
          />
        </div>
      case "lookup":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <FormInput>
            <Lookup
              readonly={this.props.readonly ?? false}
              value={value ?? null}
              uid={"lookup_filter_"+this.props.model}
              model={field.model}
            ></Lookup>
          </FormInput>
        </div>
      case "boolean":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <select
            disabled={this.props.readonly ?? false}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
          >
            {value ? <option value={1}>Yes</option> : <option value={0}>No</option>}
          </select>
        </div>
      default:
        return <></>
    }
  }

  renderChart(): JSX.Element {
    switch (this.state.selectedGraph) {
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

  requestData(): any {
    request.post(
      'api/get-chart-data',
      {config: this.props.config, model: this.props.model},
      {},
      (chartData: any) => {
        this.setState({data: chartData.data});
      }
    );
    // fetch(globalThis.main.config.rewriteBase
    //   + '/api/get-chart-data?'
    //   + 'config=' + JSON.stringify(this.props.config)
    // )
    // .then(response => {
    //   if (!response.ok) {
    //     throw new Error('Network response was not ok ' + response.statusText);
    //   }
    //   return response.json();
    // }).then(returnData => {
    //   this.setState({data: returnData.data})
    // })
  }

  render(): JSX.Element {
    var searchGroups = this.props.config.searchGroups;

    return (
      <>
        <h2>{this.props.name}</h2>
        <div className="card card-body">
          {Object.keys(searchGroups).map( ( key ) => (
            <div className="flex flex-row items-end gap-2">
              {/* --- FIELDS --- */}
              <div className="input-wrapper">
                <label className="input-label">Field</label>
                <select
                  id="configs.fields"
                  className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                  name="field"
                  value={searchGroups[key].fieldName}
                  disabled={this.props.readonly ?? false}
                >
                  <option value={searchGroups[key].fieldName}>
                    {searchGroups[key].field.title}
                  </option>
                </select>
              </div>
              {/* --- OPERATIONS --- */}
              <div className="input-wrapper">
                <select
                  disabled={this.props.readonly ?? false}
                  name="options"
                  id="options"
                  value={searchGroups[key].option}
                  className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                >
                  {Object.keys(this.renderOptions(searchGroups[key].field.type)).map((option) => (
                    <option value={option}>
                      {this.renderOptions(searchGroups[key].field.type)[option]}
                    </option>
                  ))}
                </select>
              </div>
              {/* --- SEARCH INPUT --- */}
              {this.renderInputElement(searchGroups[key].field, searchGroups[key].value)}
            </div>
          ))}


          {/* RESULT TO RETURN */}
          <div className="flex flex-row items-end gap-2">
            <div className="input-wrapper">
              <label className="input-label">Result</label>
              <select
                disabled={this.props.readonly ?? false}
                name="types"
                id="types"
                className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                value={this.props.config.returnWith[Object.keys(this.props.config.returnWith)[0]][0].field}
              >
                <option value={this.props.config.returnWith[Object.keys(this.props.config.returnWith)[0]][0].field}>
                  {this.props.config.returnWith[Object.keys(this.props.config.returnWith)[0]][0].title}
                </option>
              </select>
            </div>
            <div className="input-wrapper">
              <label className="input-label">View by</label>
              <select
                disabled={this.props.readonly ?? false}
                name="groups"
                id="groups"
                value={this.props.config.groupsBy[0].field}
                className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
              >
                <option value={this.props.config.groupsBy[0].field}>{this.props.config.groupsBy[0].title}</option>
              </select>
            </div>
          </div>
        </div>
        <div className="card card-body">
          {/* --- BUTTONS --- */}
          <div className="flex flex-row gap-1">
            {this.props.readonly ? <></> :
              <button onClick={() => this.requestData()} className="btn btn-primary"><span className="icon"><i className="fas fa-search"></i></span><span className="text">Search</span></button>
            }
            <button onClick={() => this.setState({selectedGraph: "doughnut"})} className="btn btn-primary"><span className="icon"><i className="fas fa-chart-pie"></i></span></button>
            <button onClick={() => this.setState({selectedGraph: "bar"})} className="btn btn-primary"><span className="icon"><i className="fas fa-chart-bar"></i></span></button>
          </div>
          <div className="w-full flex flex-row justify-center h-[35vh]">
            {this.state.data && this.state.data.values.length > 0 ? this.renderChart() : <>No data was found with selected parameters</>}
          </div>
        </div>
      </>
    );
  }
}
