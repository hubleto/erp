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
  graphType: string,
}

export default class FormReport extends Component<FormReportProps,FormReportState> {
  constructor(props) {
    super(props);

    this.state = {
      filterOptions: null,
      selectedField: Object.keys(this.props.configs.fields)[0],
      selectedOption: this.props.option ?? 1,
      selectedType: Object.keys(this.props.configs.returnWith)[0]+"/0",
      selectedGroup: this.props.configs.groupsBy[0]["field"],
      selectedValue: this.props.value ?? null,
      data: null,
      graphType: "doughnut"
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
        return <input readOnly={this.props.readonly ?? false} onChange={(e) => this.setState({selectedValue: e.target.value})} value={this.props.value ?? null} className="border p-2 mb-2 mt-2 rounded-md border-gray-200" type="number"></input>
      case "varchar":
      case "text":
        return <input readOnly={this.props.readonly ?? false} onChange={(e) => this.setState({selectedValue: e.target.value})} value={this.props.value ?? null} className="border p-2 mb-2 mt-2 rounded-md border-gray-200" type="text"></input>
      case "date":
      case "datetime":
      case "time":
        return <input readOnly={this.props.readonly ?? false} onChange={(e) => this.setState({selectedValue: e.target.value})} value={this.props.value ?? null} className="border p-2 mb-2 mt-2 rounded-md border-gray-200" type="date"></input>
      case "lookup":
        return <>
          <FormInput>
            <Lookup
              readonly={this.props.readonly ?? false}
              onChange={(value: any) => this.setState({selectedValue: value})}
              uid={"lookup_filter_"+newSelectedField}
              model={input.model}
            ></Lookup>
          </FormInput>
        </>
      case "boolean":
        return <select
          disabled={this.props.readonly ?? false}
          onChange={(e) => this.setState({selectedValue: e.target.value})}
          className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
        >
          <option value={0}>No</option>
          <option value={1}>Yes</option>
        </select>
      default:
        return <></>
    }
  }

  renderChart(): JSX.Element {
    switch (this.state.graphType) {
      case "bar":
        return <Bar
          options={{
            scales: {
              y: {
                beginAtZero: true,
              },
            }}
          }
          data={ {
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
        return <Doughnut
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
        <div>
          <h2>{this.props.name}</h2>
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

          {this.state.selectedField ?
            this.renderInputElement(this.state.selectedField)
          : this.renderInputElement(Object.keys(this.props.configs.fields)[0])}
          <div>
            <button onClick={() => this.requestData()} className="btn btn-primary"><span className="icon"><i className="fas fa-search"></i></span></button>
            <button onClick={() => this.setState({graphType: "doughnut"})} className="btn btn-primary"><span className="icon"><i className="fas fa-chart-pie"></i></span></button>
            <button onClick={() => this.setState({graphType: "bar"})} className="btn btn-primary"><span className="icon"><i className="fas fa-chart-bar"></i></span></button>
          </div>
        </div>
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
        <div className="h-[500px] w-[800px]">
          {this.renderChart()}
        </div>
      </>
    );
  }
}
