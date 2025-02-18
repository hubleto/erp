import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableLeadServicesProps extends TableProps {
  leadTotal?: any
}

interface TableLeadServicesState extends TableState {
}

export default class TableLeadServices extends Table<TableLeadServicesProps, TableLeadServicesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/LeadService',
  }

  props: TableLeadServicesProps;
  state: TableLeadServicesState;

  translationContext: string = 'HubletoApp/Community/Leads/Components/TableLeadServices';

  constructor(props: TableLeadServicesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderFooter(): JSX.Element {
    return <>
      <div className='flex flex-row justify-start md:justify-end'><strong className='mr-4'>{this.props.leadTotal}</strong></div>
    </>;
  }
}