import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormExpense from './FormExpense';

interface TableExpensesProps extends TableExtendedProps {
  idProject?: number,
}

interface TableExpensesState extends TableExtendedState {
}

export default class TableExpenses extends TableExtended<TableExpensesProps, TableExpensesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Projects/Models/Expense',
  }

  props: TableExpensesProps;
  state: TableExpensesState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\TableExpenses';

  constructor(props: TableExpensesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableExpensesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idProject: this.props.idProject,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idProject = this.props.idProject;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_project: this.props.idProject };
    return <FormExpense {...formProps}/>;
  }
}