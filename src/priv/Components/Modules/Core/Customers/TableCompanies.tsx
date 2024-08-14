import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import Form from 'adios/Form';

interface TablePersonProps extends TableProps {
  endpoint: string,
}

interface TablePersonState extends TableState {
}

export default class TablePerson extends Table<TablePersonProps, TablePersonState> {
  static defaultProps = {
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
  }

  props: TablePersonProps;

  getEndpointUrl(): string {
    return this.props.endpoint;
  }

  getFormModalParams(): any {
    return {
      ...super.getFormModalParams(),
      // type: 'centered tiny',
    }
  }

  getFormParams(): any {
    return {
      ...super.getFormParams(),
      isInlineEditing: false,
    }
  }

  loadParams(successCallback?: (params: any) => void): void {
    this.setState({
      addButtonText: 'Add Company',
      title: "Company",
      showHeader: true,
      canCreate: this.props.canCreate ?? true,
      canDelete: this.props.canDelete ?? true,
      canRead: this.props.canRead ?? true,
      canUpdate: this.props.canUpdate ?? true,
      columns: {
        name: { type: 'varchar', title: 'Name' },
        id_account: { type: 'lookup', title: 'Account', model: 'CeremonyCrmApp/Modules/Core/Customers/Models/Account', },
        street: { type: 'varchar', title: 'Street'},
        city: { type: 'varchar', title: 'City',},
        country: { type: 'varchar', title: 'Country',},
        postal_code: { type: 'varchar', title: 'Postal Code',},
        tax_id: { type: 'varchar', title: 'Tax ID',},
        vat_id: { type: 'varchar', title: 'VAT ID',},
        company_id: { type: 'varchar', title: 'Company ID',},
      },
    });
  }
}