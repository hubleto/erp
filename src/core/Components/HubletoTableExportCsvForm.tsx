import React, { Component } from "react";
import Form, { FormDescription, FormProps, FormState } from "adios/Form";
import queryString from 'query-string';

export interface HubletoTableExportCsvFormProps extends FormProps {}
export interface HubletoTableExportCsvFormState extends FormState {}

export default class HubletoTableExportCsvForm<P, S> extends Form<HubletoTableExportCsvFormProps,HubletoTableExportCsvFormState> {
  static defaultProps: any = {
    ...Form.defaultProps
  };

  props: HubletoTableExportCsvFormProps;
  state: HubletoTableExportCsvFormState;

  constructor(props: HubletoTableExportCsvFormProps) {
    super(props);

    this.state = this.getStateFromProps(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Export to CSV</h2>
      <small>{this.props.model}</small>
    </>;
  }

  renderWarningsOrErrors(): null|JSX.Element {
    return null;
  }

  renderFooter(): JSX.Element {
    return <></>;
  }

  renderHeaderLeft(): JSX.Element {
    return <></>;
  }

  renderHeaderRight(): JSX.Element {
    return this.renderCloseButton();
  }

  renderContent(): JSX.Element {
    return <div className="p-2">
      <div className="alert alert-info">
        CSV file with following columns and approximately {this.props.parentTable.state?.data?.total} items will be generated
      </div>
      <table className="table-default dense mt-2">
        <thead>
          <th>Column</th>
          <th>Type</th>
        </thead>
        <tbody>
          {Object.keys(this.props.parentTable.state.description.columns).map((columnName) => {
            const column = this.props.parentTable.state.description.columns[columnName];
            return <tr>
              <td>{columnName}</td>
              <td>{column.type}</td>
            </tr>;
          })}
        </tbody>
      </table>
      <a
        className="btn btn-large mt-2"
        href={globalThis.main.config.rootUrl + "/api/table-export-csv?" + queryString.stringify(this.props.parentTable.getEndpointParams())}
        target="_blank"
      >
        <span className="icon"><i className="fas fa-download"></i></span>
        <span className="text">Export to CSV</span>
      </a>
    </div>;
  }

}
