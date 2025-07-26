import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import { FormProps } from '@hubleto/react-ui/core/Form';
import FormDocument from './FormDocument';

interface TableDocumentsProps extends TableProps {}
interface TableDocumentsState extends TableState {}

export default class TableDocuments extends Table<TableDocumentsProps, TableDocumentsState> {
  static defaultProps = {
    ...Table.defaultProps,
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

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormDocument {...formProps}/>;
  }
}