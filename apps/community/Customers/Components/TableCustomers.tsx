import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCustomer from './FormCustomer';
import { getUrlParam } from 'adios/Helper';
import { FormProps } from 'adios/Form';

interface TableCustomersProps extends TableProps {
}

interface TableCustomersState extends TableState {
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

  translationContext: string = 'hubleto.app.customers.tableCustomers';

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right wide'
      }
    } else return {...super.getFormModalProps()}
  }

  /* getFormProps(): any {
    return {
      ...super.getFormProps(),
    }
  } */

  /*

  constructor(props: TableCustomersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  } */

 /*  getFormModalProps(): any {
    let params: any = super.getFormModalProps();
    params.type = this.state.formId == -1 ? 'centered' : 'right wide';
    return params;
  }
  getStateFromProps(props: TableCustomersProps) {
    return {
      ...super.getStateFromProps(props),
    }
  } */


  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "tags") {
      return (
        <>
          {data.TAGS.map((tag, key) => {
            return <div style={{backgroundColor: tag.TAG.color}} className='badge'>{tag.TAG.name}</div>;
          })}
        </>
      );
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormCustomer {...formProps}/>;
  }
}