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

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableCustomers';

  getFormModalProps() {
    if (getUrlParam('recordId') > 0) {
      return {
        ...super.getFormModalProps(),
        type: 'right wide'
      }
    } else return {...super.getFormModalProps()}
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

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormCustomer {...formProps}/>;
  }
}