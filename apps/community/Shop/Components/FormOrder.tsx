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

  getSumPrice(recordProducts: any) {
    let sumLeadPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount) {
        var sum = product.unit_price * product.amount
        if (product.discount) {
          sum = sum - (sum * (product.discount / 100))
        }
        if (product.tax) {
          sum = sum - (sum * (product.tax / 100))
        }
        sumLeadPrice += sum;
      }
    });
    return Number(sumLeadPrice.toFixed(2));
  }


  renderContent(): JSX.Element {
    const lookupElement = createRef();

    const getLookupData = () => {
      if (lookupElement.current) {
        lookupElement.current.getData(); // Call child method
      }
    }

    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;7

    return (<>
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
                  id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Shop/Models/Product",
                  cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                    return (
                      <FormInput>
                        <Lookup {...this.getDefaultInputProps()}
                          ref={lookupElement}
                          model='HubletoApp/Community/Shop/Models/Product'
                          cssClass='min-w-44'
                          value={data.id_product}
                          onChange={(value: any) => {
                            getLookupData()

                            /* fetch('../shop/get-product-price?productId='+value)
                            .then(response => {
                              if (!response.ok) {
                                throw new Error('Network response was not ok ' + response.statusText);
                              }
                              return response.json();
                            }).then(returnData => {
                              data.id_product = value;
                              data.unit_price = returnData.unit_price;
                              data.tax = returnData.tax;
                              this.updateRecord({ PRODUCTS: table.state.data?.data });
                              this.updateRecord({ price: this.getSumPrice( R.PRODUCTS )});
                            }) */
                          }}
                        ></Lookup>
                      </FormInput>
                    )
                  }
                },

                amount: { type: "int", title: "Amount" },
                unit_price: { type: "float", title: "Unit Price", readonly: true },
                discount: { type: "float", title: "Discount (%)" },
                tax: { type: "float", title: "Tax (%)", readonly: true },
                __sum: { type: "none", title: "Sum",
                  cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                    if (data.unit_price && data.amount) {
                      let sum = data.unit_price * data.amount
                      if (data.discount) { sum = sum - (sum * (data.discount / 100)) }
                      if (data.tax) { sum = sum - (sum * (data.tax / 100)) }
                      sum = Number(sum.toFixed(2));
                      return (<><span>{sum}</span></>);
                    }
                  }
                },
              }}}
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
          <a className='ml-2'
            role="button"
            onClick={() => {
              this.setState({ isInlineEditing: true});
              if (!R.PRODUCTS) R.PRODUCTS = [];
              R.PRODUCTS.push({
                id: this.state.newEntryId,
                id_order: { _useMasterRecordId_: true },
              });
              this.setState({ record: R });
              this.setState({ newEntryId: this.state.newEntryId - 1 } as FormOrderState);
            }}>
            + Add product
          </a>
        </>
      : <></>}
    </>);
  }
}