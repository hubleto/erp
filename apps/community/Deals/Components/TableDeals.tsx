import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormDeal, { FormDealProps } from './FormDeal';
import request from 'adios/Request';

interface TableDealsProps extends TableProps {
  idCustomer?: number,
  showArchive?: boolean,
}

interface TableDealsState extends TableState {
  showArchive: boolean,
}

export default class TableDeals extends Table<TableDealsProps, TableDealsState> {
  static defaultProps = {
    ...Table.defaultProps,
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

  // renderCell(columnName: string, column: any, data: any, options: any) {
  //   if (columnName == "tags") {
  //     return (
  //       <>
  //         {data.TAGS.map((tag, key) => {
  //           return <div style={{backgroundColor: tag.TAG.color}} className='badge'>{tag.TAG.name}</div>;
  //         })}
  //       </>
  //     );
  //   } else {
  //     return super.renderCell(columnName, column, data, options);
  //   }
  // }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormDealProps;
    formProps.customEndpointParams.idCustomer = this.props.idCustomer;
    formProps.customEndpointParams.showArchive = this.props.showArchive ?? false;
    return <FormDeal {...formProps}/>;
  }
}