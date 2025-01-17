import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import TableOrderProducts from './TableOrderProducts';

export interface FormOrderProps extends FormProps {}

export interface FormOrderState extends FormState {
  newEntryId: number
}

export default class FormOrder<P, S> extends Form<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'HubletoApp/Community/Shop/Models/Order',
  };

  props: FormOrderProps;
  state: FormOrderState;

  translationContext: string = 'mod.core.shop.FormOrder';

  constructor(props: FormOrderProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: -1,
    };
  }

  getStateFromProps(props: FormOrderProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{globalThis.main.translate('New Order')}</h2>;
    } else {
      return <h2>{this.state.record.title ? this.state.record.title : '[Undefined Order Number]'}</h2>
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;7

    return (<>
      <div className='card-body flex flex-row gap-2'>
        <div className='grow'>
          {this.inputWrapper('order_number')}
          {this.inputWrapper('price')}
          {this.inputWrapper('date_order')}
          {this.inputWrapper('required_delivery_date')}
          {this.inputWrapper('shipping_info')}
        </div>
        <div className='border-l border-gray-200'></div>
        <div className='grow'>
          {this.inputWrapper('note')}
        </div>
      </div>

      {R.id > 0 ?
        <>
          <div className='card-body border-t border-gray-200 flex flex-row gap-2'>
            <TableOrderProducts
              uid={this.props.uid + "_table_order_products"}
              data={{ data: R.PRODUCTS }}
              descriptionSource='props'
              description={{
                ui: {
                  showHeader: false,
                  showFooter: false,
                  addButtonText: "Add Product"
                },
                permissions: {
                  canCreate: true,
                  canUpdate: true,
                  canDelete: true,
                  canRead: true,
                },
                columns: {
                  id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Shop/Models/Product"},
                  amount: { type: "float", title: "Amount" },
                },
              }}
              isUsedAsInput={true}
              isInlineEditing={this.state.isInlineEditing}
              onRowClick={() => this.setState({isInlineEditing: true})}
              onChange={(table: TableOrderProducts) => {
                this.updateRecord({ SERVICES: table.state.data?.data });
              }}
              onDeleteSelectionChange={(table: TableOrderProducts) => {
                this.updateRecord({ SERVICES: table.state.data?.data ?? [] });
              }}
            />
          </div>
          {this.state.isInlineEditing ? (
            <a
              role="button"
              onClick={() => {
                if (!R.PRODUCTS) R.PRODUCTS = [];
                R.PRODUCTS.push({
                  id: this.state.newEntryId,
                  id_order: { _useMasterRecordId_: true },
                });
                this.setState({ record: R });
                this.setState({ newEntryId: this.state.newEntryId - 1 } as FormOrderState);
              }}>
              + Add service
            </a>
          ) : <></>}
        </>
      : <></>}
    </>);
  }
}