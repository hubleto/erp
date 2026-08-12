import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormRecipientStatusProps extends FormProps {}

const componentName = 'FormRecipientStatus'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/EmailMarketing';

const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormRecipientStatusProps) => {
  return <>
    <Input field='email' />
    <Input field='is_unsubscribed' />
    <Input field='is_invalid' />
  </>;
}

/** FormRecipientStatus */
const FormRecipientStatus = (props: FormRecipientStatusProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/RecipientStatus'}
    urlSlug='email-marketing/recipients/statuses'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'email', sub: <>{T.translate('Recipient status')}</>}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormRecipientStatus;
