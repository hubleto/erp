import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCustomer, { FormCustomerProps } from './FormCustomer';
import { getUrlParam } from 'adios/Helper';
import { FormProps } from 'adios/Form';
import request from 'adios/Request';

interface TableCustomersProps extends TableProps {
}

interface TableCustomersState extends TableState {
  tablePersonsDescription?: any,
  tableLeadsDescription?: any,
  tableDealsDescription?: any,
  tableDocumentsDescription?: any,
}

export default class TableCustomers extends Table<TableCustomersProps, TableCustomersState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Customers/Models/Customer',
  }

  props: TableCustomersProps;
  state: TableCustomersState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableCustomers';

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide'
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "tags") {
      return (
        <>
          {data.TAGS.map((tag, key) => {
            return <div style={{backgroundColor: tag.TAG.color}} className='badge' key={'tag-' + data.id + '-' + key}>{tag.TAG.name}</div>;
          })}
        </>
      );
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  onAfterLoadTableDescription(description: any): any {
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Contacts/Models/Person',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tablePersonsDescription: description} as TableCustomersState);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Leads/Models/Lead',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableLeadsDescription: description} as TableCustomersState);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Deals/Models/Deal',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableDealsDescription: description} as TableCustomersState);
      }
    );
    request.get(
      'api/table/describe',
      {
        model: 'HubletoApp/Community/Customers/Models/CustomerDocument',
        idCustomer: this.props.recordId,
      },
      (description: any) => {
        this.setState({tableDocumentsDescription: description} as TableCustomersState);
      }
    );

    return description;
  }

  renderForm(): JSX.Element {
    let formProps: FormCustomerProps = this.getFormProps() as FormCustomerProps;
    formProps.tablePersonsDescription = this.state.tablePersonsDescription;
    formProps.tableLeadsDescription = this.state.tableLeadsDescription;
    formProps.tableDealsDescription = this.state.tableDealsDescription;
    formProps.tableDocumentsDescription = this.state.tableDocumentsDescription;
    return <FormCustomer {...formProps}/>;
  }
}