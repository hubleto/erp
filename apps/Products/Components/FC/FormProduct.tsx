import React, { Component } from 'react'
import Form, { FormProps } from '@hubleto/react-ui/components/fc/Form';
import TableProductSuppliers from '../TableProductSuppliers';
import Barcode from 'react-barcode';

const FormProduct = (props: FormProps) => {
  return <Form
    {...props}
    componentName='FormProduct'
    parentApp='Hubleto/App/Community/Products'
    model='Hubleto/App/Community/Products/Models/Product'
    translationContext='Hubleto\\App\\Community\\Products\\Loader'
    translationContextInner='Components\\FormProduct'
    urlSlug='products'
    getTabs={(form: any) => {
      return [
        { uid: 'default', title: <b>{form.translate('Product')}</b> },
        { uid: 'packaging', title: form.translate('Packaging') },
        { uid: 'gallery', title: form.translate('Gallery') },
        { uid: 'suppliers', title: form.translate('Suppliers') },
        ...form.getCustomTabs(),
      ];
    }}
    renderTitle={(form: any): React.JSX.Element => {
      return <>
        <small>{form.translate('Product')}</small>
        <h2>{form.record.ean ?? '-'} {form.record.name ?? '-'}</h2>
      </>;
    }}
    renderTab={(form: any): React.JSX.Element => {
      const R = form.record;

      switch (form.activeTabUid) {
        case 'default':
          return <>
            <div className='grid grid-cols-2 gap-2'>
              <div className='border-r border-gray-200'>
                <div className='flex gap-2'>
                  <div className='flex grow'>{form.renderInputWrapper('ean')}</div>
                  <div className='flex grow'><Barcode value={R.ean} height={30} /></div>
                </div>
                {form.renderInputWrapper('name', {cssClass: 'text-2xl'})}
                {form.renderInputWrapper('is_on_sale')}
                {form.renderInputWrapper('sales_price')}
                {form.renderInputWrapper('id_group')}
                {form.renderInputWrapper('id_category')}
                {form.renderInputWrapper('vat')}
                {form.renderInputWrapper('margin')}
                {form.renderInputWrapper('unit')}
                {form.renderInputWrapper('description')}
                {form.renderInputWrapper('is_single_order_possible')}
                {form.renderInputWrapper('show_price')}
                {form.renderInputWrapper('needs_reordering')}
              </div>
              <div className=''>
                {form.renderInputWrapper('type')}
                {form.renderInputWrapper('invoicing_policy')}
                {form.renderInputWrapper('sale_ended')}
                {form.renderInputWrapper('price_after_reweight')}
                {form.renderInputWrapper('storage_rules')}
              </div>
            </div>
          </>;
        break;
        case 'packaging':
          return <>
            {form.renderInputWrapper('package_unit')}
            {form.renderInputWrapper('package_amount')}
            {form.renderInputWrapper('package_length')}
            {form.renderInputWrapper('package_width')}
            {form.renderInputWrapper('package_height')}
            {form.renderInputWrapper('package_volume')}
            {form.renderInputWrapper('package_mass')}
            {form.renderInputWrapper('package_discount')}
            {form.renderInputWrapper('package_description')}
          </>;
        break;
        case 'gallery':
          return <>
            {form.renderInputWrapper('image_1')}
            {form.renderInputWrapper('image_2')}
            {form.renderInputWrapper('image_3')}
            {form.renderInputWrapper('image_4')}
            {form.renderInputWrapper('image_5')}
          </>;
        break;
        case 'suppliers':
          return (form.id < 0 ?
            <div className="badge badge-info">{form.translate("First create the product.")}</div>
          :
            <TableProductSuppliers
              uid={form.uid + "_table_suppliers"}
              tag="ProductSuppliers"
              parentForm={this}
              idProduct={R.id}
            />
          );
        break;
      }
    }}
  />;
}

export default FormProduct;