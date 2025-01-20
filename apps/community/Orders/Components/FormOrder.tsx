import React, { Component, createRef, useRef } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import TableOrderProducts from './TableOrderProducts';
import FormInput from 'adios/FormInput';
import Lookup from 'adios/Inputs/Lookup';

export interface FormOrderProps extends FormProps {}

export interface FormOrderState extends FormState {
  newEntryId: number
}

export default class FormOrder<P, S> extends Form<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'HubletoApp/Community/Orders/Models/Order',
  };

  props: FormOrderProps;
  state: FormOrderState;

  translationContext: string = 'mod.core.orders.FormOrder';

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
      return <h2>{this.state.record.id_company ? this.state.record.order_number + " - " + this.state.record.CUSTOMER.name : '[Undefined Order]'}</h2>
    }
  }

  getSumPrice(recordProducts: any) {
    var sumLeadPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount) {
        var sum = product.unit_price * product.amount;
        if (product.tax) sum = sum + (sum * (product.tax / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
        sumLeadPrice += sum;
      }
    });
    return Number(sumLeadPrice.toFixed(2));
  }


  renderContent(): JSX.Element {
    const lookupElement = createRef();
    var lookupData;

    const getLookupData = () => {
      if (lookupElement.current) {
        lookupData = lookupElement.current.state.data;
      }
    }

    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;7

    return (<>
      <div className='card'>
        <div className='card-body flex flex-row gap-2'>
          <div className='grow'>
            {showAdditional ? this.inputWrapper('order_number') : <></>}
            {showAdditional ? this.inputWrapper('price') : <></>}
            {this.inputWrapper('date_order')}
            {this.inputWrapper('required_delivery_date')}
            {this.inputWrapper('shipping_info')}
          </div>
          <div className='border-l border-gray-200'></div>
          <div className='grow'>
            {this.inputWrapper('id_company')}
            {this.inputWrapper('note')}
          </div>
        </div>

        {R.id > 0 ?
          <>
            <div className='card-body border-t border-gray-200'>
              <a className='ml-2 mb-2 block'
                role="button"
                onClick={() => {
                  this.setState({ isInlineEditing: true});
                  if (!R.PRODUCTS) R.PRODUCTS = [];
                  R.PRODUCTS.push({
                    id: this.state.newEntryId,
                    id_order: { _useMasterRecordId_: true },
                    amount: 1,
                    unit_price: 0,
                    tax: 0,
                    discount: 0,
                  });
                  this.setState({ record: R });
                  this.setState({ newEntryId: this.state.newEntryId - 1 } as FormOrderState);
                }}>
                + Add product
              </a>
              <TableOrderProducts
                sum={R.price ?? 0}
                uid={this.props.uid + "_table_order_products"}
                data={{ data: R.PRODUCTS }}
                descriptionSource='props'
                description={{
                  ui: {
                    showHeader: false,
                    showFooter: true,
                    addButtonText: "Add Product"
                  },
                  permissions: {
                    canCreate: false,
                    canUpdate: true,
                    canDelete: this.state.isInlineEditing,
                    canRead: true,
                  },
                  columns: {
                    id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                      cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                        return (
                          <FormInput>
                            <Lookup {...this.getDefaultInputProps()}
                              ref={lookupElement}
                              model='HubletoApp/Community/Products/Models/Product'
                              cssClass='min-w-44'
                              value={data.id_product}
                              onChange={(value: any) => {
                                getLookupData();

                                if (lookupData[value]) {
                                  data.id_product = value;
                                  data.unit_price = lookupData[value].unit_price;
                                  data.tax = lookupData[value].tax;
                                  this.updateRecord({ PRODUCTS: table.state.data?.data });
                                  this.updateRecord({ price: this.getSumPrice( R.PRODUCTS )});
                                }
                              }}
                            ></Lookup>
                          </FormInput>
                        )
                      }
                    },
                    amount: { type: "int", title: "Amount" },
                    unit_price: { type: "float", title: "Unit Price"},
                    tax: { type: "float", title: "Tax (%)"},
                    discount: { type: "float", title: "Discount (%)" },
                    __sum: { type: "none", title: "Sum after tax",
                      cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                        if (data.unit_price && data.amount) {
                          let sum = data.unit_price * data.amount;
                          if (data.tax) sum = sum + (sum * (data.tax / 100));
                          if (data.discount) sum = sum - (sum * (data.discount / 100));
                          sum = Number(sum.toFixed(2));
                          return (<><span>{sum}</span></>);
                        }
                      }
                    },
                  }
                }}
                isUsedAsInput={true}
                isInlineEditing={this.state.isInlineEditing}
                onRowClick={() => this.setState({isInlineEditing: true})}
                onChange={(table: TableOrderProducts) => {
                  this.updateRecord({ price: this.getSumPrice( R.PRODUCTS ), PRODUCTS: table.state.data?.data });
                }}
                onDeleteSelectionChange={(table: TableOrderProducts) => {
                  this.updateRecord({ PRODUCTS: table.state.data?.data ?? [] });
                }}
              />
            </div>
          </>
        : <></>}
      </div>
    </>);
  }
}