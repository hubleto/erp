import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormActivity from './FormActivity';

interface TableActivitiesProps extends TableProps {
}

interface TableActivitiesState extends TableState {
}

export default class TableActivities extends Table<TableActivitiesProps, TableActivitiesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmMod/Core/Customers/Models/Activity',
  }

  props: TableActivitiesProps;
  state: TableActivitiesState;

  translationContext: string = 'ceremonycrmmod.core.customers.tableActivities';

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
    return <FormActivity {...formDescription}/>;
  }
}