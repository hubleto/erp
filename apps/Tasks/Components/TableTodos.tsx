import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import FormTodo from './FormTodo';

interface TableTodosProps extends TableExtendedProps {
}

interface TableTodosState extends TableExtendedState {
}

export default class TableTodos extends TableExtended<TableTodosProps, TableTodosState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Tasks/Models/Todo',
  }

  props: TableTodosProps;
  state: TableTodosState;

  translationContext: string = 'Hubleto\\App\\Community\\Tasks\\Loader';
  translationContextInner: string = 'Components\\TableTodos';

  refActivityModal: any;
  refActivityForm: any;

  constructor(props: TableTodosProps) {
    super(props);
    this.refActivityModal = React.createRef();
    this.refActivityForm = React.createRef();
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTodosProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'centered small';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/tasks/todo/' + (id > 0 ? id : 'add'));
  }

  rowClassName(rowData: any): string {
    return rowData.is_closed ? 'bg-slate-300' : super.rowClassName(rowData);
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    // formProps.idCustomer = this.props.idCustomer;
    // if (!formProps.description) formProps.description = {};
    // formProps.description.defaultValues = { id_customer: this.props.idCustomer };
    return <FormTodo {...formProps}/>;
  }

}