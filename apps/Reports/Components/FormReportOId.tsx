import React, { Component } from "react";
import Lookup from "@hubleto/react-ui/core/Inputs/Lookup";
import FormInput from "@hubleto/react-ui/core/FormInput";
import request from "@hubleto/react-ui/core/Request";
import HubletoChart, { HubletoChartType } from "@hubleto/src/core/Components/HubletoChart";

export interface FormReportProps {
  reportUrlSlug: string,
  config: any,
  readonly?: boolean,
  name?: string,
}

export interface FormReportState {
  data: any,
  selectedGraph: HubletoChartType,
}

export default class FormReport extends Component<FormReportProps,FormReportState> {
  constructor(props) {
    super(props);

    this.state = {
      selectedGraph: "bar",
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
          6: "Between",
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

  renderInputElement(field: any, value: any, value2?: any): JSX.Element {
    switch (field.type) {
      case "int":
      case "float":
        return <div className="input-wrapper">
          <label className="input-label">{value2 ? "Search Between": "Search"}</label>
          <input
            readOnly={this.props.readonly ?? false}
            value={value ?? null}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
            type="number"
          />
          {value2 ?
            <input
              readOnly={this.props.readonly ?? false}
              value={value2 ?? null}
              className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
              type="number"
            />
          : <></>}
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
          <label className="input-label">{value2 ? "Search Between": "Search"}</label>
          <input
            readOnly={this.props.readonly ?? false}
            value={value ?? null}
            className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
            type={field.type}
          />
          {value2 ?
            <input
              readOnly={this.props.readonly ?? false}
              value={value2 ?? null}
              className="border p-2 mb-2 mt-2 rounded-md border-gray-200"
              type={field.type}
            />
          : <></>}
        </div>
      case "lookup":
        return <div className="input-wrapper">
          <label className="input-label">Search</label>
          <FormInput>
            <Lookup
              readonly={this.props.readonly ?? false}
              value={value ?? null}
              uid={"lookup_filter_"+field.model}
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

  requestData(): any {
    request.post(
      'reports/' + this.props.reportUrlSlug + '/load-data',
      {config: this.props.config},
      {},
      (chartData: any) => {
        this.setState({data: chartData.data});
      }
    );
  }

  render(): JSX.Element {
    var searchGroups = this.props.config.searchGroups;

    return (
      <>
        <div className="mt-2 card card-body">
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
              {this.renderInputElement(searchGroups[key].field, searchGroups[key].value, searchGroups[key].value2 ?? null)}
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
                value={this.props.config.returnWith[Object.keys(this.props.config.returnWith)[0]].field}
              >
                <option value={this.props.config.returnWith[Object.keys(this.props.config.returnWith)[0]].field}>
                  {this.props.config.returnWith[Object.keys(this.props.config.returnWith)[0]].title}
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

        {/* --- BUTTONS --- */}
        <div className="card card-body bg-white/80">
          <div className="flex flex-row gap-1">
            {this.props.readonly ? <></> :
              <button onClick={() => this.requestData()} className="btn btn-primary"><span className="icon"><i className="fas fa-search"></i></span><span className="text">Search</span></button>
            }
            <button onClick={() => this.setState({selectedGraph: "doughnut"})} className="btn btn-primary"><span className="icon"><i className="fas fa-chart-pie"></i></span></button>
            <button onClick={() => this.setState({selectedGraph: "bar"})} className="btn btn-primary"><span className="icon"><i className="fas fa-chart-bar"></i></span></button>
          </div>
          <div className="w-full flex flex-row justify-center h-[35vh]">
            {this.state.data && this.state.data.values.length > 0
              ? <HubletoChart type={this.state.selectedGraph} data={this.state.data} />
              : <>No data was found with selected parameters</>
            }
          </div>
        </div>
      </>
    );
  }
}
