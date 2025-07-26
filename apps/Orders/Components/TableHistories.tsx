import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableHistoriesProps extends TableProps {
  sum?: number,
}

interface TableHistoriesState extends TableState {}

export default class TableHistories extends Table<TableHistoriesProps, TableHistoriesState> {
  static defaultProps = {
    ...Table.defaultProps,
    orderBy: {
      field: "date",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Orders/Models/History',
  }

  props: TableHistoriesProps;
  state: TableHistoriesState;

  translationContext: string = 'HubletoApp\\Community\\Orders\\Loader::Components\\TableHistories';

  constructor(props: TableHistoriesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableHistoriesProps) {
    return {
      ...super.getStateFromProps(props)
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }
}