import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormDocument, { FormDocumentProps } from './FormDocument';

interface TableDocumentsProps extends TableExtendedProps {}
interface TableDocumentsState extends TableExtendedState {}

export default class TableDocuments extends TableExtended<TableDocumentsProps, TableDocumentsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Documents/Models/Document',
  }

  props: TableDocumentsProps;
  state: TableDocumentsState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\TableDocuments';

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
    params.type = 'right';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/documents/' + (id > 0 ? id : 'add'));
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