import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormDeal from './FormDeal';

interface TableDealsProps extends TableProps {
  showArchive?: boolean,
}

interface TableDealsState extends TableState {
  showArchive: boolean,
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
    model: 'CeremonyCrmMod/Sales/Deals/Models/Deal',
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
      showArchive: props.showArchive ?? false,
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      showArchive: this.state.showArchive ? 1 : 0,
    }
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    if (!this.state.showArchive) {
      elements.push(
        <a className="btn btn-transparent" href="deals/archive">
          <span className="icon"><i className="fas fa-box-archive"></i></span>
          <span className="text">Archive</span>
        </a>
      );
    }

    return elements;
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
    let formProps = this.getFormProps();
    return <FormDeal {...formProps}/>;
  }
}