import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormDocument, { FormDocumentProps } from './FormDocument';

interface TableDocumentsProps extends HubletoTableProps {}
interface TableDocumentsState extends HubletoTableState {}

export default class TableDocuments extends HubletoTable<TableDocumentsProps, TableDocumentsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Documents/Models/Document',
  }

  props: TableDocumentsProps;
  state: TableDocumentsState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\TableDocuments';

  constructor(props: TableDocumentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDocumentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered small';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/documents/' + id);
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "hyperlink") {
      return <>
        {data[columnName] && data[columnName].length > 28 ? data[columnName].substring(0, 28) + '...' : data[columnName]}
        <a
          href={data[columnName]}
          target='_blank'
          onClick={(e) => { e.stopPropagation(); }}
          className="btn btn-transparent"
        >
          <span className="icon"><i className="fa-solid fa-up-right-from-square"></i></span>
        </a>
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }
  
  renderForm(): JSX.Element {
    let formProps: FormDocumentProps = this.getFormProps();
    return <FormDocument {...formProps}/>;
  }
}