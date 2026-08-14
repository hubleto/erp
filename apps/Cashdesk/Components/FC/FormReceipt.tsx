import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import IntInput from '@hubleto/react-ui/components/fc/Inputs/Int';
import Barcode from 'react-barcode';

export interface FormReceiptProps extends FormProps {}

const componentName = 'FormReceipt'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Cashdesk';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormReceiptProps) => {
  const form = React.useContext(FormMetaContext);
  const COMPANY: any = useRecordField('COMPANY', {});
  const ITEMS: any = useRecordField('ITEMS', {});
  const CASH_REGISTER: any = useRecordField('CASH_REGISTER', {});
  const number: string = useRecordField('number', '');
  const created: string = useRecordField('created', '');
  const totalPriceInclVat: number = useRecordField('total_price_incl_vat', 0);
  
  const updateItem = (index: number, newItem: any) => {
    let newITEMS = ITEMS;
    if (!newITEMS[index]) newITEMS[index] = {};
    newITEMS[index].id_receipt = { _useMasterRecordId_: true };
    newITEMS[index] = {...newITEMS[index], ...newItem};
    form.changeRecord({ITEMS: newITEMS});
  }

return <div className='flex gap-2'>
    <div className='w-full flex-3'>
      <Input field='id_company' />
      <Input field='number' />
      <Input field='id_cash_register' />
      <Input field='created' />
      <Input field='sent_to_cash_register' />
      <table className='table-default dense mt-2'>
        <thead>
          <tr>
            <th>{T.translate('Product')}</th>
            <th>{T.translate('Qty.')}</th>
            <th>{T.translate('Unit price incl. VAT (€)')}</th>
            <th>{T.translate('VAT (%)')}</th>
            <th>{T.translate('Total price incl. VAT (€)')}</th>
          </tr>
        </thead>
        <tbody>
          {ITEMS ? ITEMS.map((item, index) => {
            return <tr>
              <td>
                <LookupInput
                  model='Hubleto/App/Community/Products/Models/Product'
                  value={item.id_product}
                  cssClass='font-bold'
                  onChange={(input: any, value: any) => { updateItem(index, {id_product: value}); }}
                ></LookupInput>
              </td>
              <td>
                <IntInput
                  value={item.quantity}
                  description={{decimals: 4, unit: 'x'}}
                  onChange={(input: any, value: any) => { updateItem(index, {quantity: value}); }}
                ></IntInput>
              </td>
              <td>
                <IntInput
                  value={item.vat_percent}
                  description={{decimals: 4, unit: '%'}}
                  onChange={(input: any, value: any) => { updateItem(index, {vat_percent: value}); }}
                ></IntInput>
              </td>
              <td>
                <IntInput
                  value={item.unit_price_excl_vat}
                  description={{decimals: 4, unit: '€'}}
                  onChange={(input: any, value: any) => { updateItem(index, {unit_price_incl_vat: value}); }}
                ></IntInput>
              </td>
              <td>
                <IntInput
                  value={item.total_price_incl_vat}
                  description={{decimals: 4, unit: '€'}}
                  onChange={(input: any, value: any) => { updateItem(index, {total_price_incl_vat: value}); }}
                ></IntInput>
              </td>
            </tr>;
          }) : null}
        </tbody>
      </table>
      <button
        className='btn btn-add mt-2'
        onClick={() => {
          let newITEMS = ITEMS;
          newITEMS.push({});
          form.changeRecord({ITEMS: newITEMS});
        }}
      >
        <span className='icon'><i className='fas fa-plus'></i></span>
        <span className='text'>{T.translate('Add item')}</span>
      </button>

      <div className='mt-8 text-center'>
        <button className='btn btn-add-outline btn-extra-large'>
          <span className='icon'><i className='fas fa-arrow-right'></i></span>
          <span className='text'>{T.translate('Send to cash register')}</span>
        </button>
      </div>
    </div>
    <div className='w-full flex-1'>
      <div className='m-4 shadow p-2 rounded-lg border border-gray-200'>
        <div className='border-dashed border-b p-2 text-center'>
          <b>{COMPANY.name}</b><br/>
          {T.translate('TAX ID:')} {COMPANY.tax_id}<br/>
          {T.translate('VAT ID:')} {COMPANY.vat_id}<br/>
          {COMPANY.street_1}<br/>
          {COMPANY.street_2}<br/>
          {COMPANY.zip} {COMPANY.city}<br/>
          {COMPANY.country}
        </div>
        <div className='text-center font-bold border-dashed border-b p-2'>
          {T.translate('RECEIPT')} #{number}
        </div>
        <div className='border-dashed border-b p-2'>
          {ITEMS ? ITEMS.map((item, index) => {
            return <div className='w-full my-2'>
              <div className='text-blue-400'>{item.PRODUCT.ean ?? '-'}</div>
              <div className='font-bold'>{item.PRODUCT.name}</div>
              <div className='flex gap justify-between w-full'>
                <div>{globalThis.hubleto.numberFormat(item.quantity, 2, ",", " ")}x</div>
                <div>{globalThis.hubleto.numberFormat(item.unit_price_incl_vat, 2, ",", " ")} €</div>
                <div>{globalThis.hubleto.numberFormat(item.vat_percent, 2, ",", " ")} %</div>
                <div>{globalThis.hubleto.numberFormat(item.total_price_incl_vat, 2, ",", " ")} €</div>
              </div>
            </div>;
          }) : null}
        </div>
        <div className='p-2 mt-2 bg-gray-600 text-white text-3xl'>
          {T.translate('TOTAL:')} {globalThis.hubleto.numberFormat(totalPriceInclVat, 2, ",", " ")} €
        </div>
        <div className='mt-4 p-2 text-center'>
          {created}<br/>
          {T.translate('Cash register identifier:')} {CASH_REGISTER.identifier}<br/>
          <br/>
          <div className='m-auto'>
            <Barcode value={CASH_REGISTER.identifier} height={30} lineColor='#444444' margin={0} className='w-full'/>
          </div>
        </div>
      </div>
    </div>
  </div>
}

/** FormReceipt */
const FormReceipt = (props: FormReceiptProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Receipt'}
    urlSlug='cashdesk/receipts'
    endpointParams={{saveRelations: ['ITEMS']}}
    // onAfterFormInitialized={(form: any) => {}}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    title={{field: 'number', sub: T.translate('Receipt')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormReceipt;
