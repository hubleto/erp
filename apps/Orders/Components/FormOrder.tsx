import React, { Component, createRef, useRef } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableOrderProducts from './TableOrderProducts';
import FormInput from '@hubleto/react-ui/core/FormInput';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import { TabPanel, TabView } from 'primereact/tabview';
import TableHistories from './TableHistories';

export interface FormOrderProps extends HubletoFormProps {
  tableOrderProductsDescription?: any,
}

export interface FormOrderState extends HubletoFormState {
  newEntryId: number,
}

export default class FormOrder<P, S> extends HubletoForm<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Orders/Models/Order',
  };

  props: FormOrderProps;
  state: FormOrderState;

  translationContext: string = 'HubletoApp\\Community\\Orders\\Loader::Components\\FormOrder';

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

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{globalThis.main.translate('New Order')}</h2>;
    } else {
      return <h2>{this.state.record.id_customer ? this.state.record.order_number : '[Undefined Order]'}</h2>
    }
  }

  getSumPrice(recordProducts: any) {
    var sumPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount && product._toBeDeleted_ != true) {
        var sum = product.unit_price * product.amount;
        if (product.vat) sum = sum + (sum * (product.vat / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
        sumPrice += sum;
      }
    });
    return Number(sumPrice.toFixed(2));
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
    const showAdditional = R.id > 0 ? true : false;

    return (<>
      <TabView>
        <TabPanel header={this.translate('Order')}>
          <div className='card'>
            <div className='card-body flex flex-row gap-2'>
              <div className='grow'>
                {showAdditional ? this.inputWrapper('order_number') : <></>}
                {showAdditional ?
                  <div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price')}
                    {this.inputWrapper('id_currency')}
                  </div>
                : <></>}
                {this.inputWrapper('date_order')}
                {this.inputWrapper('required_delivery_date')}
                {this.inputWrapper('shipping_info')}
              </div>
              <div className='border-l border-gray-200'></div>
              <div className='grow'>
                {this.inputWrapper('id_customer')}
                {this.inputWrapper('note')}
              </div>
            </div>

            {R.id > 0 ?
              <>
                <div className='card-body border-t border-gray-200'>
                  <a
                    className="btn btn-add-outline mb-2"
                    onClick={() => {
                      this.setState({ isInlineEditing: true});
                      if (!R.PRODUCTS) R.PRODUCTS = [];
                      R.PRODUCTS.push({
                        id: this.state.newEntryId,
                        id_order: { _useMasterRecordId_: true },
                        amount: 1,
                        unit_price: 0,
                        vat: 0,
                        discount: 0,
                      });
                      this.setState({ record: R });
                      this.setState({ newEntryId: this.state.newEntryId - 1 } as FormOrderState);
                    }}
                  >
                    <span className="icon"><i className="fas fa-add"></i></span>
                    <span className="text">Add product</span>
                  </a>
                  <div className='w-full h-full overflow-x-auto'>
                    <TableOrderProducts
                      sum={"Total: " + R.price + " " + R.CURRENCY.code}
                      uid={this.props.uid + "_table_order_products"}
                      readonly={!this.state.isInlineEditing}
                      isUsedAsInput={true}
                      isInlineEditing={this.state.isInlineEditing}
                      data={{ data: R.PRODUCTS }}
                      customEndpointParams={{idOrder: R.id}}
                      descriptionSource='props'
                      description={{
                        permissions: this.props.tableOrderProductsDescription.permissions,
                        ui: {
                          showHeader: false,
                          showFooter: true,
                          addButtonText: "Add Product"
                        },
                        columns: {
                          title: { type: "varchar", title: this.translate('Title') },
                          id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                            cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                              return (
                                <FormInput>
                                  <Lookup {...this.getInputProps()}
                                    ref={lookupElement}
                                    model='HubletoApp/Community/Products/Models/Product'
                                    cssClass='min-w-44'
                                    value={data.id_product}
                                    onChange={(input: any, value: any) => {
                                      getLookupData();

                                      if (lookupData[value]) {
                                        data.id_product = value;
                                        data.unit_price = lookupData[value].unit_price;
                                        data.vat = lookupData[value].vat;
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
                          vat: { type: "float", title: "Vat (%)"},
                          discount: { type: "float", title: "Discount (%)" },
                          __sum: { type: "none", title: "Sum after vat",
                            cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                              if (data.unit_price && data.amount) {
                                let sum = data.unit_price * data.amount;
                                if (data.vat) sum = sum + (sum * (data.vat / 100));
                                if (data.discount) sum = sum - (sum * (data.discount / 100));
                                sum = Number(sum.toFixed(2));
                                return (<><span>{sum + " " + R.CURRENCY.code}</span></>);
                              }
                            }
                          },
                        },
                        inputs: {
                          id_product: { type: "lookup", title: "Product", model: "HubletoApp/Community/Products/Models/Product",
                            cellRenderer: ( table: TableOrderProducts, data: any, options: any): JSX.Element => {
                              return (
                                <FormInput>
                                  <Lookup {...this.getInputProps('product_lookup')}
                                    ref={lookupElement}
                                    model='HubletoApp/Community/Products/Models/Product'
                                    cssClass='min-w-44'
                                    value={data.id_product}
                                    onChange={(input: any, value: any) => {
                                      getLookupData();

                                      if (lookupData[value]) {
                                        data.id_product = value;
                                        data.unit_price = lookupData[value].unit_price;
                                        data.vat = lookupData[value].vat;
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
                          vat: { type: "float", title: "Vat (%)"},
                          discount: { type: "float", title: "Discount (%)" },
                          __sum: { type: "none", title: "Sum after vat" },
                        }
                      }}
                      onRowClick={() => this.setState({isInlineEditing: true})}
                      onChange={(table: TableOrderProducts) => {
                        this.updateRecord({ price: this.getSumPrice( R.PRODUCTS ), PRODUCTS: table.state.data?.data });
                      }}
                      onDeleteSelectionChange={(table: TableOrderProducts) => {
                        this.updateRecord({ PRODUCTS: table.state.data?.data ?? [], price: this.getSumPrice(R.PRODUCTS) });
                      }}
                    />
                  </div>
                </div>
              </>
            : <></>}
          </div>
        </TabPanel>
        {showAdditional ?
          <TabPanel header={this.translate('History')}>
            <TableHistories
              uid={this.props.uid + "_table_order_history"}
              data={{ data: R.HISTORY }}
              descriptionSource='props'
              description={{
                ui: {
                  showHeader: false,
                  showFooter: false,
                },
                permissions: {
                  canCreate: false,
                  canUpdate: false,
                  canDelete: false,
                  canRead: true,
                },
                columns: {
                  short_description: { type: "text", title: "Short Description" },
                  date: { type: "datetime", title: "Date Time"},
                },
                inputs: {
                  short_description: { type: "text", title: "Short Description" },
                  date: { type: "datetime", title: "Date Time"},
                }
              }}
              isUsedAsInput={true}
              isInlineEditing={false}
              onRowClick={(table: TableHistories, row: any) => table.openForm(row.id)}
            />
          </TabPanel>
        : <></>}
      </TabView>
    </>);
  }
}