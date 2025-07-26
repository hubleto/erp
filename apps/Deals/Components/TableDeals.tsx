import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormDeal, { FormDealProps } from './FormDeal';
import request from '@hubleto/react-ui/core/Request';

interface TableDealsProps extends HubletoTableProps {
  idCustomer?: number,
  showArchive?: boolean,
}

interface TableDealsState extends HubletoTableState {
  showArchive: boolean,
}

export default class TableDeals extends HubletoTable<TableDealsProps, TableDealsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Deals/Models/Deal',
  }

  props: TableDealsProps;
  state: TableDealsState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\TableDeals';

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
      showArchive: this.props.showArchive ? 1 : 0,
      idCustomer: this.props.idCustomer,
    }
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "title") {
      return <>
        {super.renderCell(columnName, column, data, options)}
        {data['note'] ?
          <div
            className="badge badge-extra-small badge-warning block whitespace-pre truncate"
            style={{maxHeight: '2.7em', maxWidth: '20em', overflow: 'hidden'}}
          ><i className="fas fa-note-sticky mr-2"></i>{data['note']}</div>
        : null}
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormDealProps;
    formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    formProps.customEndpointParams.showArchive = this.props.showArchive ?? false;
    return <FormDeal {...formProps}/>;
  }
}