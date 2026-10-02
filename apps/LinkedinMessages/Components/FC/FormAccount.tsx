import React from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { type FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import request from '@hubleto/react-ui/core/Request';
import TableMessages from './TableMessages';

export interface FormAccountProps extends FormProps {}

const componentName = 'FormAccount'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormAccountProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const picture: string = useRecordField('picture_url', '');
  const status: number = useRecordField('status', 0);
  const lastSyncInfo: string = useRecordField('last_sync_info', '');

  const connect = () => {
    window.location.href = globalThis.hubleto.config.projectUrl + '/linkedin-messages/api/oauth-connect';
  }

  const syncNow = () => {
    request.get('linkedin-messages/api/sync', { idAccount: form.id }, (data: any) => {
      globalThis.hubleto.showDialogInfo
        ? globalThis.hubleto.showDialogInfo(data.message ?? '')
        : alert(data.message ?? '');
      form.reload();
    });
  }

  const disconnect = () => {
    request.get('linkedin-messages/api/disconnect', { idAccount: form.id }, () => form.reload());
  }

  return <div className='flex flex-col gap-2 md:flex-row'>
    <div className='flex-1'>
      <div className='flex flex-row gap-2 mb-2'>
        <button className='btn btn-add' onClick={connect}>
          <span className='icon'><i className='fab fa-linkedin'></i></span>
          <span className='text'>{status == 1 ? T.translate('Reconnect to LinkedIn') : T.translate('Connect to LinkedIn')}</span>
        </button>
        {form.id > 0 ? <>
          <button className='btn btn-transparent' onClick={syncNow}>
            <span className='icon'><i className='fas fa-rotate'></i></span>
            <span className='text'>{T.translate('Synchronize now')}</span>
          </button>
          <button className='btn btn-transparent' onClick={disconnect}>
            <span className='icon'><i className='fas fa-link-slash'></i></span>
            <span className='text'>{T.translate('Disconnect')}</span>
          </button>
        </> : null}
      </div>
      {lastSyncInfo ? <div className='alert alert-info text-xs mb-2'>{lastSyncInfo}</div> : null}
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='headline' />
      <Input field='email' />
      <Input field='profile_url' />
      <Input field='locale' />
    </div>
    <div className='flex-1'>
      {picture ? <img src={picture} alt='' className='w-24 h-24 rounded-full mb-2' /> : null}
      <Input field='status' />
      <Input field='id_owner' />
      <Input field='scopes' />
      <Input field='token_expires_at' />
      <Input field='last_sync_at' />
    </div>
  </div>;
}

/** TabMessages */
const TabMessages = (props: FormAccountProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the account first')}</div>;
  return <TableMessages
    tag={"table_account_messages"}
    parentForm={form}
    uid={form.uid + "_table_account_messages"}
    idAccount={form.id}
  />;
}


/** FormAccount */
const FormAccount = (props: FormAccountProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Account'}
    urlSlug='linkedin-messages/accounts'
    tabs={{
      default: { title: <b>{T.translate('Account')}</b>, content: () => <TabDefault {...props} /> },
      messages: { title: T.translate('Messages'), content: () => <TabMessages {...props} /> },
    }}
    title={{fields: ['name'], sub: T.translate('LinkedIn account')}}
    {...props}
  ></Form>;
}

export default FormAccount;
