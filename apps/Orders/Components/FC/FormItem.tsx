import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecord, useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormRecord } from '@hubleto/react-ui/components/cc/Form';

export interface FormItemProps extends FormProps {
  idOrder?: number,
}

const componentName = 'FormItem'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Orders';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormItemProps) => {
  const form = React.useContext(FormMetaContext);
  const title: string = useRecordField('title', '');
  const unitPrice: number = useRecordField('unit_price', 0);
  const amount: number = useRecordField('amount', 0);
  const discount: number = useRecordField('discount', 0);
  const vat: number = useRecordField('vat', 0);

  return <>
    <div className="flex gap-2 mt-2">
      <div className='flex-5'>
        <Input field='title' />
      </div>
    </div>
    <div className="flex gap-2 mt-2">
      <div className='flex-1'>
        <Input field='id_order' />
        <Input field='id_product' />
        <Input field='unit_price' />
        <Input field='amount' />
        <Input field='discount' />
        <Input field='vat' />
        <div className='bg-slate-50 p-2'>
          <b>{T.translate('Summary')}</b><br/>
          <table className='table-default dense w-full'>
            <thead>
              <tr>
                <th></th>
                <th>{T.translate('Without discount')}</th>
                <th>{T.translate('With discount')}</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>{title}</b></td>
                <td className='text-nowrap'>
                  <div className='flex gap-2'>
                    <div>{globalThis.hubleto.currencyFormat(unitPrice, 4)}</div>
                    <div>x</div>
                    <div>{globalThis.hubleto.numberFormat(amount, 4)}</div>
                  </div>
                </td>
                <td className='text-nowrap'>
                  <div className='flex gap-2'>
                    <div>{globalThis.hubleto.currencyFormat(unitPrice * (1 - discount / 100), 4)}</div>
                    <div>x</div>
                    <div>{globalThis.hubleto.numberFormat(amount, 4)}</div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>{T.translate('Excluding VAT')}</td>
                <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(unitPrice * amount, 4)}</td>
                <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(unitPrice * amount * (1 - discount / 100), 4)}</td>
              </tr>
              <tr>
                <td>{T.translate('Including {vat}% VAT').replace('{vat}', globalThis.hubleto.numberFormat(vat, 0))}</td>
                <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(unitPrice * amount * (1 + vat / 100), 4)}</td>
                <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(unitPrice * amount * (1 + vat / 100) * (1 - discount / 100), 4)}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div className='flex-1'>
        <div className='card'>
          <div className='card-header'>
            Dates
          </div>
          <div className='card-body'>
            <Input field='date_due' />
            <Input field='date_delivery' />
            <Input field='charged_period_start' />
            <Input field='charged_period_end' />
          </div>
        </div>
        <Input field='notes' />
        <Input field='attachment_1' />
        <Input field='attachment_2' />
        <Input field='position' />
        <Input field='is_chargeable' />
        <Input field='id_invoice_item' />
      </div>
    </div>
  </>;
}

/** FormItem */
const FormItem = (props: FormItemProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/XXX'}
    urlSlug='orders/items'
    endpointParams={{idOrder: props.idOrder}}
    onBeforeCopyRecord={(form: FormMeta, record: FormRecord) => {
      return {
        ...record,
        title: 'Copy of ' + (record.title ?? ''),
        id_invoice_item: null,
        date_due: null,
        attachment_1: null,
        attachment_2: null,
        notes: null,
        id: -1
      };
    }}
    title={{fields: ['position', 'title'], sub: T.translate('Order Item')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormItem;


