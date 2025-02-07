import React, { Component } from "react";
import Lookup from "adios/Inputs/Lookup";
import FormInput from "adios/FormInput";

import { Chart as ChartJS, ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale } from "chart.js";
import { Bar, Doughnut, Line } from "react-chartjs-2";

ChartJS.register(ArcElement, Tooltip, Legend, BarController, BarElement, CategoryScale, LinearScale);

interface ReportConfig {
  fields: any,
  returnWith: any,
  groupsBy: any,
}

export interface FormReportProps {
  configs: ReportConfig,
  model: string,
  value?: string,
  readonly?: boolean,
  option?: number,
  name?: string,
}

export interface FormReportState {
  selectedField: string,
  selectedOption: number,
  selectedValue: string,
  selectedType: string,
  selectedGroup: string,
  filterOptions: any,
  data: any,
  selectedGraph: string,
}

export default class FormReport extends Component<FormReportProps,FormReportState> {
  constructor(props) {
    super(props);

    this.state = {
      filterOptions: null,
      selectedField: Object.keys(this.props.configs.fields)[0],
      selectedOption: this.props.option || !isNaN(this.props.option) ? this.props.option : 1,
      selectedType: Object.keys(this.props.configs.returnWith)[0]+"/0",
      selectedGroup: this.props.configs.groupsBy[0]["field"],
      selectedValue: this.props.value ?? null,
      selectedGraph: "doughnut",
      data: null,
    };
  }

  componentDidMount(): void {
    this.changeOptions(Object.keys(this.props.configs.fields)[0]);
    if (this.props.readonly) {
      this.setState({selectedOption: this.props.option});
      this.requestData();
    }
  }

  changeOptions(newSelectedField: string): void {
    var input = this.props.configs.fields[newSelectedField];
    this.setState({selectedOption: 1})

    switch (input.type) {
      case "int":
      case "float":
        this.setState({filterOptions: {
          1: "Is",
          2: "Is Not",
          3: "More Than",
          4: "Less Than",
        }});
        break;
      case "varchar":
      case "text":
        this.setState({filterOptions: {
          1: "Is",
          2: "Is Not",
          5: "Contains",
        }});
        break;
      case "date":
      case "datetime":
      case "time":
        this.setState({filterOptions: {
          1: "On",
          2: "Not On",
          6: "Between",
        }});
        break;
      case "lookup":
        this.setState({filterOptions: {
          1: "Is",
          2: "Is Not",
        }});
        break;
      case "boolean":
        this.setState({filterOptions: {
          1: "Is",
        }});
        break;
    }
  }

  renderInputElement(newSelectedField: string): JSX.Element {
    let input = this.props.configs.fields[newSelectedField];

    switch (input.type) {
      case "int":
      case "float":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <input
            readOnly={this.props.readonly ?? false}
            onChange={(e) => this.setState({selectedValue: e.target.value})}
            value={this.props.value ?? null}
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
            onChange={(e) => this.setState({selectedValue: e.target.value})}
            value={this.props.value ?? null}
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
            onChange={(e) => this.setState({selectedValue: e.target.value})}
            value={this.props.value ?? null}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
            type={input.type}
          />
        </div>
      case "lookup":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <FormInput>
            <Lookup
              readonly={this.props.readonly ?? false}
              value={this.props.value ?? this.state.selectedValue}
              onChange={(value: any) => this.setState({selectedValue: value})}
              uid={"lookup_filter_"+newSelectedField}
              model={input.model}
            ></Lookup>
          </FormInput>
        </div>
      case "boolean":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <select
            disabled={this.props.readonly ?? false}
            onChange={(e) => this.setState({selectedValue: e.target.value})}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
          >
            <option value={0}>No</option>
            <option value={1}>Yes</option>
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
    fetch(globalThis.main.config.rewriteBase
      + '/api/get-chart-data?'
      + 'field='+this.state.selectedField
      + '&option='+this.state.selectedOption
      + '&value='+this.state.selectedValue
      + '&type='+this.state.selectedType
      + '&model='+this.props.model
      + '&groupBy='+this.state.selectedGroup
      + '&typeOptions='+JSON.stringify(this.props.configs.returnWith)
    )
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok ' + response.statusText);
      }
      return response.json();
    }).then(returnData => {
      this.setState({data: returnData.data})
    })
  }

  render(): JSX.Element {
    return (
      <>
        <h2>{this.props.name}</h2>
        <div className="card card-body">
          <div className="flex flex-row items-end gap-2">
            {/* --- FIELDS --- */}
            <div className="input-wrapper">
              <label className="input-label">Field</label>
              <select
                disabled={this.props.readonly ?? false}
                name="field"
                id="configs.fields"
                className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                onChange={(e) => {
                  this.setState({selectedField: e.target.value})
                  this.changeOptions(e.target.value)
                }}
              >
                {Object.keys(this.props.configs.fields).map( (key) => (
                  <option
                    value={key}
                  >
                    {this.props.configs.fields[key].title}
                  </option>
                ))}
              </select>
            </div>
            {/* --- OPERATIONS --- */}
            <div className="input-wrapper">
              <select
                disabled={this.props.readonly ?? false}
                name="options"
                id="options"
                value={this.state.selectedOption}
                className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                onChange={(e) => {
                  this.setState({selectedOption: Number(e.target.value)})
                }}
              >
                {this.state.filterOptions ?
                  Object.keys(this.state.filterOptions).map((key) => (
                    <option
                      value={key}
                    >
                      {this.state.filterOptions[key]}
                    </option>
                  ))
                : <option>Empty</option>}
              </select>
            </div>
            {/* --- SEARCH INPUT --- */}
            {this.renderInputElement(this.state.selectedField)}
          </div>
          <div className="flex flex-row items-end gap-2">
            <div className="input-wrapper">
              <label className="input-label">Result</label>
              <select
                disabled={this.props.readonly ?? false}
                name="types"
                id="types"
                value={this.state.selectedType}
                className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                onChange={(e) => {
                  this.setState({selectedType: e.target.value})
                }}
              >
                {Object.keys(this.props.configs.returnWith).map( (key) => (
                  <optgroup label={key}>
                    {Object.keys(this.props.configs.returnWith[key]).map( (item, index) => (
                      <option value={key + "/" + index}>{this.props.configs.returnWith[key][item]["title"]}</option>
                    ))}
                  </optgroup>
                ))}
              </select>
            </div>
            <div className="input-wrapper">
              <label className="input-label">View by</label>
              <select
                disabled={this.props.readonly ?? false}
                name="groups"
                id="groups"
                value={this.state.selectedGroup}
                className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
                onChange={(e) => {
                  this.setState({selectedGroup: e.target.value})
                }}
              >
                {Object.keys(this.props.configs.groupsBy).map( (key) => (
                  <option value={this.props.configs.groupsBy[key]["field"]}>{this.props.configs.groupsBy[key]["title"]}</option>
                ))}
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
