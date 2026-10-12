import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import request from '@hubleto/react-ui/core/Request';
import { type FormMeta, type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import TableItems from './TableItems';
import TableAssignments from './TableAssignments';
import TableRules from './TableRules';
import TableHistories from './TableHistories';
import TablePriceListHasDocuments from './TablePriceListHasDocuments';

export interface FormPriceListProps extends FormProps {}

const componentName = 'FormPriceList'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/PriceLists';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const STATUS_DRAFT = 1;
const STATUS_ACTIVE = 2;
const STATUS_ARCHIVED = 3;

/** TabDefault */
const TabDefault = (props: FormPriceListProps) => {
  const form = React.useContext(FormMetaContext);
  const status: number = useRecordField('status', STATUS_DRAFT);
  const basis: number = useRecordField('basis', 1);

  const call = (url: string, params: any, message?: string) => {
    request.get(url, params, (data: any) => {
      if (data.status == 'success') form.reload();
      else alert(data.message ?? T.translate('The operation failed.'));
    });
  };

  return <>
    {form.id > 0 ? <div className='flex gap-2 flex-wrap mb-2'>
      {status != STATUS_ACTIVE ? <button className='btn btn-small btn-primary' onClick={() => call('price-lists/api/set-status', {idPriceList: form.id, status: STATUS_ACTIVE})}>
        <span className='icon'><i className='fas fa-check'></i></span>
        <span className='text'>{T.translate('Activate')}</span>
      </button> : null}
      {status == STATUS_ACTIVE ? <button className='btn btn-small btn-transparent' onClick={() => call('price-lists/api/set-status', {idPriceList: form.id, status: STATUS_ARCHIVED})}>
        <span className='icon'><i className='fas fa-box-archive'></i></span>
        <span className='text'>{T.translate('Archive')}</span>
      </button> : null}
      {status != STATUS_DRAFT ? <button className='btn btn-small btn-transparent' onClick={() => call('price-lists/api/set-status', {idPriceList: form.id, status: STATUS_DRAFT})}>
        <span className='text'>{T.translate('Set to draft')}</span>
      </button> : null}
      <button className='btn btn-small btn-transparent' onClick={() => {
        request.get('price-lists/api/copy-price-list', {idPriceList: form.id}, (data: any) => {
          if (data.status == 'success') globalThis.window.open(globalThis.hubleto.config.projectUrl + '/price-lists/' + data.idPriceList);
          else alert(data.message);
        });
      }}>
        <span className='icon'><i className='fas fa-copy'></i></span>
        <span className='text'>{T.translate('Copy')}</span>
      </button>
      <button className='btn btn-small btn-transparent' onClick={() => call('price-lists/api/generate-pdf', {idPriceList: form.id})}>
        <span className='icon'><i className='fas fa-file-pdf'></i></span>
        <span className='text'>{T.translate('Generate PDF')}</span>
      </button>
      <button className='btn btn-small btn-transparent' onClick={() => {
        const to = globalThis.window.prompt(T.translate('E-mail address of the recipient'));
        if (to) call('price-lists/api/send-by-email', {idPriceList: form.id, to: to});
      }}>
        <span className='icon'><i className='fas fa-envelope'></i></span>
        <span className='text'>{T.translate('Send by e-mail')}</span>
      </button>
      <a className='btn btn-small btn-transparent' href={globalThis.hubleto.config.projectUrl + '/price-lists/matrix?idPriceList=' + form.id} target='_blank'>
        <span className='icon'><i className='fas fa-table-cells'></i></span>
        <span className='text'>{T.translate('Matrix')}</span>
      </a>
    </div> : null}

    <div className='flex-dyn gap-2'>
      <div className='flex-1'>
        <Input field='code' />
        <Input field='name' />
        <Input field='description' />
        <Input field='type' />
        <Input field='status' />
        <Input field='id_currency' />
        <Input field='prices_include_vat' />
        <Input field='is_default' />
        <Input field='priority' />
        <Input field='valid_from' />
        <Input field='valid_to' />
      </div>
      <div className='flex-1'>
        <Input field='basis' />
        {basis == 4 ? <Input field='id_parent_price_list' /> : null}
        {basis != 1 ? <>
          <Input field='derive_method' />
          <Input field='derive_value' />
        </> : null}
        <Input field='rounding_policy' />
        <Input field='rounding_step' />
        <Input field='rounding_ending' />
        <Input field='id_owner' />
        <Input field='id_manager' />
        <Input field='shared_with' />
        <Input field='id_workflow_step' />
        <Input field='note' />
      </div>
    </div>
  </>;
}

/** TabPrices */
const TabPrices = (props: FormPriceListProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the price list first')}</div>;
  return <TableItems
    tag={'table_price_list_item'}
    parentForm={form}
    uid={props.uid + '_table_price_list_item'}
    idPriceList={form.id}
  />;
}

/** TabAssignments */
const TabAssignments = (props: FormPriceListProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the price list first')}</div>;
  return <TableAssignments
    tag={'table_price_list_assignment'}
    parentForm={form}
    uid={props.uid + '_table_price_list_assignment'}
    idPriceList={form.id}
  />;
}

/** TabRules */
const TabRules = (props: FormPriceListProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the price list first')}</div>;
  return <TableRules
    tag={'table_price_list_rule'}
    parentForm={form}
    uid={props.uid + '_table_price_list_rule'}
    idPriceList={form.id}
  />;
}

/** TabHistory */
const TabHistory = (props: FormPriceListProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the price list first')}</div>;
  return <TableHistories
    tag={'table_price_list_history'}
    parentForm={form}
    uid={props.uid + '_table_price_list_history'}
    idPriceList={form.id}
  />;
}

/** TabDocuments */
const TabDocuments = (props: FormPriceListProps) => {
  const form = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the price list first')}</div>;
  return <TablePriceListHasDocuments
    tag={'table_price_list_document'}
    parentForm={form}
    uid={props.uid + '_table_price_list_document'}
    idPriceList={form.id}
  />;
}

/** FormPriceList */
const FormPriceList = (props: FormPriceListProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/PriceList'}
    urlSlug='price-lists'
    title={{fields: ['code', 'name'], sub: T.translate('Price list')}}
    tabs={{
      default: {title: <b>{T.translate('Price list')}</b>, content: () => <TabDefault {...props} />},
      prices: {title: T.translate('Prices'), content: () => <TabPrices {...props} />},
      assignments: {title: T.translate('Assignments'), content: () => <TabAssignments {...props} />},
      rules: {title: T.translate('Rules'), content: () => <TabRules {...props} />},
      history: {title: T.translate('History'), content: () => <TabHistory {...props} />},
      documents: {title: T.translate('Documents'), content: () => <TabDocuments {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormPriceList;
