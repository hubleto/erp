import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormLead from './FormLead';

interface TableLeadsProps extends TableProps {
}

interface TableLeadsState extends TableState {
}

export default class TableLeads extends Table<TableLeadsProps, TableLeadsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 20,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Lead',
  }

  props: TableLeadsProps;
  state: TableLeadsState;

  constructor(props: TableLeadsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormLead {...formDescription}/>;
  }
}