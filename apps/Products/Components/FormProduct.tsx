import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableProductSuppliers from './TableProductSuppliers';
import Barcode from 'react-barcode';

export interface FormProductProps extends FormExtendedProps {}
export interface FormProductState extends FormExtendedState {}

export default class FormProduct<P, S> extends FormExtended<FormProductProps,FormProductState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Products/Models/Product',
  };

  props: FormProductProps;
  state: FormProductState;

  parentApp: string = 'Hubleto/App/Community/Products';

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\FormProduct';

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
        { uid: 'packaging', title: this.translate('Packaging') },
        { uid: 'gallery', title: this.translate('Gallery') },
        { uid: 'suppliers', title: this.translate('Suppliers') },
        ...this.getCustomTabs()
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'products/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Product')}</small>
      <h2>{this.state.record.ean ?? '-'} {this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='grid grid-cols-2 gap-2'>
            <div className='border-r border-gray-200'>
              <div className='flex gap-2'>
                <div className='flex grow'>{this.inputWrapper('ean')}</div>
                <div className='flex grow'><Barcode value={R.ean} height={30} /></div>
              </div>
              {this.inputWrapper('name', {cssClass: 'text-2xl'})}
              {this.inputWrapper('is_on_sale')}
              {this.inputWrapper('sales_price')}
              {this.inputWrapper('id_group')}
              {this.inputWrapper('id_category')}
              {this.inputWrapper('vat')}
              {this.inputWrapper('margin')}
              {this.inputWrapper('unit')}
              {this.inputWrapper('description')}
              {this.inputWrapper('is_single_order_possible')}
              {this.inputWrapper('show_price')}
              {this.inputWrapper('needs_reordering')}
            </div>
            <div className=''>
              {this.inputWrapper('type')}
              {this.inputWrapper('invoicing_policy')}
              {this.inputWrapper('sale_ended')}
              {this.inputWrapper('price_after_reweight')}
              {this.inputWrapper('storage_rules')}
            </div>
          </div>
        </>;
      break;
      case 'packaging':
        return <>
          {this.inputWrapper('package_unit')}
          {this.inputWrapper('package_amount')}
          {this.inputWrapper('package_length')}
          {this.inputWrapper('package_width')}
          {this.inputWrapper('package_height')}
          {this.inputWrapper('package_volume')}
          {this.inputWrapper('package_mass')}
          {this.inputWrapper('package_discount')}
          {this.inputWrapper('package_description')}
        </>;
      break;
      case 'gallery':
        return <>
          {this.inputWrapper('image_1')}
          {this.inputWrapper('image_2')}
          {this.inputWrapper('image_3')}
          {this.inputWrapper('image_4')}
          {this.inputWrapper('image_5')}
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
        super.renderTab(tabUid);
      break;
    }
  }
}