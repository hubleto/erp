import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

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
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return <h2>{globalThis.main.translate('New Product')}</h2>;
    } else {
      return <h2>{this.state.record.title ? this.state.record.title : '[Undefined Product Name]'}</h2>
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;7

    return (<>
      <div className='card'>
        <div className='card-body grid grid-cols-2 gap-2'>
          <div className='border-r border-gray-200'>
            {this.inputWrapper('title', {cssClass: 'text-2xl text-primary'})}
            {this.inputWrapper('unit_price')}
            {this.inputWrapper('vat')}
            {this.inputWrapper('margin')}
            {this.inputWrapper('unit')}
            {this.inputWrapper('id_supplier')}
          </div>
          <div className=''>
            {this.inputWrapper('image')}
            {this.inputWrapper('description')}
            {this.inputWrapper('id_product_group')}
            {this.inputWrapper('type')}
            {this.inputWrapper('count_in_package')}
            {this.inputWrapper('is_on_sale')}
            {this.inputWrapper('sale_ended')}
          </div>

          <div className='border-r border-t border-gray-200'>
            {this.inputWrapper('is_single_order_possible')}
            {this.inputWrapper('show_price')}
            {this.inputWrapper('packaging')}
            {this.inputWrapper('needs_reordering')}
            {this.inputWrapper('supplier')}
          </div>
          <div className='border-t border-gray-200'>
            {this.inputWrapper('price_after_reweight')}
            {this.inputWrapper('storage_rules')}
            {this.inputWrapper('table')}
          </div>
        </div>
      </div>
      {/* <div className='card-body flex flex-row gap-2'>

      </div>
      <div className='card-body border-t border-gray-200 flex flex-row gap-2'>

      </div> */}
    </>);
  }
}