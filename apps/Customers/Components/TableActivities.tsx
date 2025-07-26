import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import CustomerFormActivity from './CustomerFormActivity';

interface TableActivitiesProps extends TableProps {
}

interface TableActivitiesState extends TableState {
}

export default class TableActivities extends Table<TableActivitiesProps, TableActivitiesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Customers/Models/Activity',
  }

  props: TableActivitiesProps;
  state: TableActivitiesState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableActivities';

  constructor(props: TableActivitiesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableActivitiesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <CustomerFormActivity {...formDescription}/>;
  }
}