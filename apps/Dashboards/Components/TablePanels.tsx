import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormPanel from './FormPanel';

interface TablePanelsProps extends TableProps {
}

interface TablePanelsState extends TableState {
}

export default class TablePanels extends Table<TablePanelsProps, TablePanelsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Dashboards/Models/Panel',
  }

  props: TablePanelsProps;
  state: TablePanelsState;

  translationContext: string = 'Hubleto\\App\\Community\\Dashboards\\Loader';
  translationContextInner: string = 'Components\\TablePanels';

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  constructor(props: TablePanelsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormPanel {...formProps}/>;
  }
}