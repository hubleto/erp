import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableProductSuppliers from './TableProductSuppliers';

export interface FormProductProps extends HubletoFormProps {}
export interface FormProductState extends HubletoFormState {}

export default class FormProduct<P, S> extends HubletoForm<FormProductProps,FormProductState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Products/Models/Product',
  };

  props: FormProductProps;
  state: FormProductState;

  translationContext: string = 'HubletoApp\\Community\\Products\\Loader::Components\\FormProduct';

  constructor(props: FormProductProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props)
    };
  }

  getStateFromProps(props: FormProductProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Product')}</b> },
        { uid: 'suppliers', title: this.translate('Suppliers') },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Product</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
          <div className='card'>
            <div className='card-body grid grid-cols-2 gap-2'>
              <div className='border-r border-gray-200'>
                {this.inputWrapper('name', {cssClass: 'text-2xl text-primary'})}
                {this.inputWrapper('sales_price')}
                {this.inputWrapper('vat')}
                {this.inputWrapper('margin')}
                {this.inputWrapper('unit')}
              </div>
              <div className=''>
                {this.inputWrapper('image')}
                {this.inputWrapper('description')}
                {this.inputWrapper('id_product_group')}
                {this.inputWrapper('type')}
                {this.inputWrapper('invoicing_policy')}
                {this.inputWrapper('amount_in_package')}
                {this.inputWrapper('is_on_sale')}
                {this.inputWrapper('sale_ended')}
              </div>

              <div className='border-r border-t border-gray-200'>
                {this.inputWrapper('is_single_order_possible')}
                {this.inputWrapper('show_price')}
                {this.inputWrapper('packaging')}
                {this.inputWrapper('needs_reordering')}
              </div>
              <div className='border-t border-gray-200'>
                {this.inputWrapper('price_after_reweight')}
                {this.inputWrapper('storage_rules')}
                {this.inputWrapper('table')}
              </div>
            </div>
          </div>
        </>;
      break;
      case 'suppliers':
        return (this.state.id < 0 ?
          <div className="badge badge-info">{this.translate("First create the product.")}</div>
        :
          <TableProductSuppliers
            uid={this.props.uid + "_table_suppliers"}
            tag="ProductSuppliers"
            parentForm={this}
            idProduct={R.id}
          />
        );
      break;

      default:
        super.renderTab(tab);
      break;
    }
  }
}