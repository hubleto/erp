import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';

export interface FormAccountProps extends FormProps {}

const componentName = 'FormAccount'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Mail';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormAccountProps) => <>
  <Input field='name' />
  <Divider>{T.translate('Receiving (IMAP)')}</Divider>
  <Input field='imap_host' />
  <Input field='imap_port' />
  <Input field='imap_encryption' />
  <Input field='imap_username' />
  <Input field='imap_password' />
  <Input field='max_attachment_size' />
</>;

/** TabSmtp */
const TabSmtp = (props: FormAccountProps) => <>
  <Input field='sender_email' />
  <Input field='sender_name' />
  <Input field='smtp_host' />
  <Input field='smtp_port' />
  <Input field='smtp_encryption' />
  <Input field='smtp_username' />
  <Input field='smtp_password' />
</>;


/** FormAccount */
const FormAccount = (props: FormAccountProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Account'}
    urlSlug='mail/accounts'
    endpointParams={{}}
    title={{field: 'ssubject', sub: T.translate('Account')}}
    tabs={{
      default: {title: <b>{T.translate('Email account')}</b>, content: () => <TabDefault {...props} />},
      smtp: {title: T.translate('Sending (SMTP)'), content: () => <TabSmtp {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormAccount;

