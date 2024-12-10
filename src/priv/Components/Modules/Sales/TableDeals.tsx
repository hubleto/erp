import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormDeal from './FormDeal';

interface TableDealsProps extends TableProps {
  archive?: any
}

interface TableDealsState extends TableState {
  archive: any
}

export default class TableDeals extends Table<TableDealsProps, TableDealsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Deals/Models/Deal',
  }

  props: TableDealsProps;
  state: TableDealsState;

  translationContext: string = 'mod.core.sales.tableDeals';

  constructor(props: TableDealsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDealsProps) {
    return {
      ...super.getStateFromProps(props),
      archive: props.archive ?? false,
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      archive: this.state.archive,
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