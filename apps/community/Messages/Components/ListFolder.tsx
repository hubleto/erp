import React, { Component } from 'react'
import request from "adios/Request";
import Table, { TableProps, TableState } from 'adios/Table';
import Form, { FormProps } from 'adios/Form';
import FormMessage from './FormMessage';
import { ProgressBar } from 'primereact/progressbar';
import ModalForm from "adios/ModalForm";

interface ListFolderProps extends TableProps {
  folder?: string,
}
interface ListFolderState extends TableState {
}

export default class ListFolder extends Table<ListFolderProps, ListFolderState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Messages/Models/Message',
  }

  props: ListFolderProps;
  state: ListFolderState;

  translationContext: string = 'HubletoApp\\Community\\Messages\\Loader::Components\\ListFolder';

  constructor(props: ListFolderProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
    };
  }

  getEndpointParams() {
    return {
      ...super.getEndpointParams(),
      folder: this.props.folder
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormMessage {...formProps}/>;
  }

}