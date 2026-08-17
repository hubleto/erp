import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormProfileProps extends FormProps {}

const componentName = 'FormProfile'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormProfileProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <Input field='is_default' />
    <Input field='name' />
    <Input field='headline' />
    <Input field='id_company' />
    <Input field='id_currency' />
    <Input field='id_payment_method' />
    <Input field='iban' />
    <Input field='swift' />
    <Input field='due_days' />
    <Input field='numbering_pattern' />
    <Input field='invoice_type_prefixes' />
  </>;
}

/** TabTemplate */
const TabTemplate = (props: FormProfileProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <Input field='id_template' />
    <Input field='stamp_and_signature' />
  </>;
}

/** TabEmails */
const TabEmails = (props: FormProfileProps) => {
  const form = React.useContext(FormMetaContext);
  return <>
    <Input field='id_sender_account' />
    <div className='card mt-2'>
      <div className='card-header'>
        {T.translate('Send invoice')}
      </div>
      <div className='card-body'>
        <Input field='mail_send_invoice_subject' />
        <Input field='mail_send_invoice_body' />
        <Input field='mail_send_invoice_cc' />
        <Input field='mail_send_invoice_bcc' />
      </div>
    </div>
    <div className='card mt-2'>
      <div className='card-header'>
        <Input field='Send warning about due invoice' />
      </div>
      <div className='card-body'>
        <Input field='mail_send_due_warning_subject' />
        <Input field='mail_send_due_warning_body' />
        <Input field='mail_send_due_warning_cc' />
        <Input field='mail_send_due_warning_bcc' />
      </div>
    </div>
  </>;
}

/** FormProfile */
const FormProfile = (props: FormProfileProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Profile'}
    urlSlug='invoices/profiles'
    endpointParams={{}}
    title={{field: 'name', sub: T.translate('Invoicing profile')}}
    tabs={{
      default: { title: <b>{T.translate('Profile')}</b>, content: () => <TabDefault {...props} /> },
      template: { title: T.translate('Template'), content: () => <TabTemplate {...props} /> },
      emails: { title: T.translate('E-mails'), content: () => <TabEmails {...props} /> },
    }}
    {...props}
  ></Form>;
}

export default FormProfile;
