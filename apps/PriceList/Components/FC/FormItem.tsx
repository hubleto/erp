import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import request from '@hubleto/react-ui/core/Request';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';

export interface FormItemProps extends FormProps {
  idPriceList?: number,
  idProduct?: number,
}

const componentName = 'FormItem'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/PriceLists';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormItemProps) => {
  const form = React.useContext(FormMetaContext);
  const method: number = useRecordField('method', 1);
  const idPriceList: number = useRecordField('id_price_list', 0);
  const idProduct: number = useRecordField('id_product', 0);
  const minQty: number = useRecordField('min_qty', 0);
  const [preview, setPreview] = React.useState<any>(null);

  const showPreview = () => {
    request.get(
      'price-lists/api/explain-price',
      {idProduct: idProduct, qty: minQty > 0 ? minQty : 1, idPriceListHint: idPriceList},
      (data: any) => setPreview(data)
    );
  };

  return <div className='flex-dyn gap-2'>
    <div className='flex-1'>
      <Input field='id_price_list' />
      <div className='font-bold mt-2'>{T.translate('Scope (use only one)')}</div>
      <Input field='id_product' />
      <Input field='id_category' />
      <Input field='id_group' />
      <Input field='id_unit' />
      <Input field='id_supplier' />
      <Input field='supplier_product_code' />
      <div className='font-bold mt-2'>{T.translate('Conditions')}</div>
      <Input field='min_qty' />
      <Input field='max_qty' />
      <Input field='valid_from' />
      <Input field='valid_to' />
      <Input field='is_active' />
      <Input field='priority' />
    </div>
    <div className='flex-1'>
      <Input field='method' />
      {method != 1 ? <Input field='base' /> : null}
      {method != 1 ? <Input field='id_base_price_list' /> : null}
      <Input field='value' />
      <Input field='extra_fee' />
      <Input field='id_vat_rate' />
      <div className='font-bold mt-2'>{T.translate('Limits')}</div>
      <Input field='min_margin_percent' />
      <Input field='min_price' />
      <Input field='max_price' />
      <Input field='note' />

      {form.id > 0 && idProduct > 0 ? <div className='bg-slate-50 p-2 mt-2'>
        <button className='btn btn-small btn-transparent' onClick={showPreview}>
          <span className='icon'><i className='fas fa-flask'></i></span>
          <span className='text'>{T.translate('Preview the resolved price')}</span>
        </button>
        {preview ? <div className='mt-2'>
          <b>{globalThis.hubleto.currencyFormat(preview.result?.unitPrice ?? 0, 4)}</b>
          <ul className='text-xs mt-1'>
            {(preview.trace ?? []).map((line: string, index: number) => <li key={index}>{line}</li>)}
          </ul>
        </div> : null}
      </div> : null}
    </div>
  </div>;
}

/** FormItem */
const FormItem = (props: FormItemProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Item'}
    urlSlug='price-lists/items'
    endpointParams={{idPriceList: props.idPriceList, idProduct: props.idProduct}}
    title={{fields: ['id'], sub: T.translate('Price')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormItem;
