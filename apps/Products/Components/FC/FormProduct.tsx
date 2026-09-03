import React, { Component } from 'react'
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import TableProductSuppliers from './TableProductSuppliers';
import Barcode from 'react-barcode';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Translator from '@hubleto/react-ui/core/Translator';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';

const componentName = 'FormProduct'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Products';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormProps) => {
  const ean = useRecordField('ean');

  return <div className='grid grid-cols-2 gap-2'>
    <div className='border-r border-gray-200'>
      <div className='flex gap-2'>
        <div className='flex grow'><Input field='ean' /></div>
        <div className='flex grow'><Barcode value={ean} height={30} /></div>
      </div>
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='is_on_sale' customInputProps={{yesText: 'On sale'}} />
      <Input field='sales_price' />
      <Input field='id_group' />
      <Input field='id_category' />
      <Input field='vat' />
      <Input field='margin' />
      <Input field='unit' />
      <Input field='description' />
      <Input field='is_single_order_possible' customInputProps={{yesText: 'Single unit order possible'}} />
      <Input field='show_price' customInputProps={{yesText: 'Show price to customer'}} />
      <Input field='needs_reordering' customInputProps={{yesText: 'Needs reordering'}} />
    </div>
    <div className=''>
      <Input field='type' />
      <Input field='invoicing_policy' />
      <Input field='sale_ended' />
      <Input field='price_after_reweight' />
      <Input field='storage_rules' />
    </div>
  </div>;
}

/** TabPackaging */
const TabPackaging = (props: FormProps) => {
  return <>
    <Input field='package_unit' />
    <Input field='package_amount' />
    <Input field='package_length' />
    <Input field='package_width' />
    <Input field='package_height' />
    <Input field='package_volume' />
    <Input field='package_mass' />
    <Input field='package_discount' />
    <Input field='package_description' />
  </>;
}

/** TabGallery */
const TabGallery = (props: FormProps) => {
  return <>
    <Input field='image_1' />
    <Input field='image_2' />
    <Input field='image_3' />
    <Input field='image_4' />
    <Input field='image_5' />
  </>;
}

/** TabSuppliers */
const TabSuppliers = (props: FormProps) => {
  const form = React.useContext(FormMetaContext);
  
  return (form.id < 0 ?
    <div className="badge badge-info">{T.translate("First create the product.")}</div>
  :
    <TableProductSuppliers
      uid={props.uid + "_table_suppliers"}
      tag="ProductSuppliers"
      parentForm={form}
      idProduct={form.id}
    />
  );
}

const FormProduct = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Product'}
    urlSlug='products'
    title={{fields: ['ean', 'name'], sub: T.translate('Product')}}
    tabs={{
      default: {title: <b>{T.translate('Product')}</b>, content: () => <TabDefault {...props} />},
      packaging: {title: T.translate('Packaging'), content: () => <TabPackaging {...props} />},
      gallery: {title: T.translate('Gallery'), content: () => <TabGallery {...props} />},
      suppliers: {title: T.translate('Suppliers'), content: () => <TabSuppliers {...props} />},
    }}
    {...props}
  />;
}

export default FormProduct;