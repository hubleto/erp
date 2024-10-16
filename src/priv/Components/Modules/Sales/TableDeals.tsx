import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormDeal from './FormDeal';

interface TableDealsProps extends TableProps {
}

interface TableDealsState extends TableState {
}

export default class TableDeals extends Table<TableDealsProps, TableDealsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 20,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Deal',
  }

  props: TableDealsProps;
  state: TableDealsState;

  constructor(props: TableDealsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDealsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "labels") {
      return (
        <div className='flex flex-row gap-2'>
          {data.LABELS.map((label, key) => {
            return (
              <div
                style={{color: label.LABEL.color, borderColor: label.LABEL.color}}
                className='border rounded px-1'>
                {label.LABEL.name}
              </div>
            );
          })}
        </div>
      );
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormDeal {...formDescription}/>;
  }
}