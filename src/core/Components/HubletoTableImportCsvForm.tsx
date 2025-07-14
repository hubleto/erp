import React, { Component, createRef } from "react";
import Form, { FormProps, FormState } from "adios/Form";
import InputFile from "adios/Inputs/File";
import request from "adios/Request";

export interface HubletoTableImportCsvFormProps extends FormProps {}
export interface HubletoTableImportCsvFormState extends FormState {
  csvData: string,
}

export default class HubletoTableImportCsvForm<P, S> extends Form<HubletoTableImportCsvFormProps,HubletoTableImportCsvFormState> {
  static defaultProps: any = {
    ...Form.defaultProps
  };

  props: HubletoTableImportCsvFormProps;
  state: HubletoTableImportCsvFormState;

  refCsvFileInput: any;

  constructor(props: HubletoTableImportCsvFormProps) {
    super(props);
    this.refCsvFileInput = createRef();

    this.state = {
      ...this.getStateFromProps(props),
      csvData: '',
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>Import to CSV</h2>
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
        How to import:
        <ul>
          <li className="ml-2">• Prepare your CSV file. Read <a href="" target="_blank" className="btn btn-transparent">this guide</a> to learn how the CSV file shall be structured.</li>
          <li className="ml-2">• Upload the CSV file.</li>
          <li className="ml-2">• Start the import by clicking on "Import from CSV" button.</li>
        </ul>
      </div>
      <div style={{zoom: 2}}>
        <InputFile
          type='file'
          uid={this.props.parentTable.uid + '_import_csv_file'}
          ref={this.refCsvFileInput}
          onChange={(input: any, value: any) => {
            console.log(value);
            this.setState({csvData: value.fileData ?? ''});
          }}
          uploadButtonText='Select CSV file'
        />
      </div>

      <div className="alert alert-info mt-2">CSV file size: {Math.round(this.state.csvData.length * 100 / 1024) / 100} kB</div>
      <button
        className="btn btn-large mt-2"
        onClick={() => {
          console.log(this.refCsvFileInput,this.refCsvFileInput.current, this.refCsvFileInput.current.value);
          request.post(
            "api/table-import-csv",
            {
              ...this.props.parentTable.getEndpointParams(),
              csvData: this.state.csvData,
            },
            {},
            (data: any) => {
              console.log(data);
            }
          );
        }}
      >
        <span className="icon"><i className="fas fa-download"></i></span>
        <span className="text">Import from CSV</span>
      </button>
    </div>;
  }

}
